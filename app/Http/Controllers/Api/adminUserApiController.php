<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserApiController extends Controller
{
    use LogsActivity;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort = $request->sort ?? 'created_at';
        $order = $request->order ?? 'desc';

        return response()->json(
            User::orderBy($sort, $order)->paginate(10),
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi role super_admin hanya boleh 1
        if ($request->role === 'super_admin') {
            $existingSuperAdmin = User::where('role', 'super_admin')->exists();
            if ($existingSuperAdmin) {
                return response()->json([
                    'message' => 'Super Admin sudah ada. Hanya boleh 1 Super Admin.',
                    'errors' => [
                        'role' => ['Super Admin sudah ada']
                    ]
                ], 422);
            }
        }

        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,super_admin,admin_ppdb'
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = User::create($validatedData);

        // ✅ Log activity
        $this->logCreate('User', $user);

        return response()->json([
            'message' => 'User berhasil ditambahkan!',
            'user' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json($user, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Cegah edit Super Admin oleh admin biasa
        if ($user->isSuperAdmin() && !$request->user()->isSuperAdmin()) {
            return response()->json([
                'message' => 'Forbidden. Tidak bisa edit Super Admin.'
            ], 403);
        }

        // Validasi role super_admin hanya boleh 1 (kecuali update user yg sama)
        if ($request->role === 'super_admin' && $user->role !== 'super_admin') {
            $existingSuperAdmin = User::where('role', 'super_admin')->exists();
            if ($existingSuperAdmin) {
                return response()->json([
                    'message' => 'Super Admin sudah ada',
                    'errors' => [
                        'role' => ['Super Admin sudah ada']
                    ]
                ], 422);
            }
        }

        $rules = [
            'name' => 'required|max:255',
            'role' => 'required|in:admin,super_admin,admin_ppdb'
        ];

        if ($request->email !== $user->email) {
            $rules['email'] = 'required|email|unique:users,email';
        }

        $validatedData = $request->validate($rules);

        // Store old values for logging
        $oldValues = $user->toArray();

        $user->update($validatedData);

        // ✅ Log activity
        $this->logUpdate('User', $user, $oldValues);

        return response()->json([
            'message' => 'User berhasil diperbarui!',
            'user' => $user
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
        // Cegah hapus Super Admin
        if ($user->isSuperAdmin()) {
            return response()->json([
                'message' => 'Forbidden. Super Admin tidak bisa dihapus.'
            ], 403);
        }

        if ($user->id === $request->user()->id) {
            return response()->json([
                'message' => 'Tidak bisa menghapus akun sendiri.'
            ], 403);
        }

        // ✅ Log activity before delete
        $this->logDelete('User', $user);

        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus'
        ], 200);
    }

    /**
     * Check if super admin exists (untuk frontend)
     */
    public function checkSuperAdmin()
    {
        $exists = User::where('role', 'super_admin')->exists();

        return response()->json([
            'super_admin_exists' => $exists
        ]);
    }
}