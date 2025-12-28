<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LoginAttempt;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Login dengan rate limiting
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $ipAddress = $request->ip();
        $email = $request->email;
        $userAgent = $request->userAgent();
        // ✅ Check rate limiting (5 attempts per minute per IP)
        $key = 'login-attempts:' . $ipAddress;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'message' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit.',
                'retry_after' => $seconds,
            ], 429);
        }
        // Find user
        $user = User::where('email', $email)->first();
        // Check user exists dan password benar
        if (!$user || !Hash::check($request->password, $user->password)) {
            // ❌ Log failed attempt
            LoginAttempt::log($email, $ipAddress, $userAgent, false);

            // Increment rate limiter
            RateLimiter::hit($key, 60); // 60 seconds decay

            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        }
        // Check role admin
        if (!$user->isAdmin()) {
            // ❌ Log failed attempt (bukan admin)
            LoginAttempt::log($email, $ipAddress, $userAgent, false);

            RateLimiter::hit($key, 60);

            return response()->json([
                'message' => 'Akses ditolak. Hanya admin yang bisa login.'
            ], 403);
        }
        // ✅ Login successful - clear rate limiter
        RateLimiter::clear($key);

        // ✅ Log successful attempt
        LoginAttempt::log($email, $ipAddress, $userAgent, true);

        // ✅ Log to Activity Logs
        ActivityLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => 'login',
            'description' => "Login berhasil: {$user->name} ({$user->email})",
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);

        // Generate token
        $token = $user->createToken('admin-token')->plainTextToken;
        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        // ✅ Log to Activity Logs before deleting tokens
        ActivityLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => 'logout',
            'description' => "Logout: {$user->name} ({$user->email})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Hapus semua token user (revoke all sessions)
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }
}