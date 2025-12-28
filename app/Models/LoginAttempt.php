<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class LoginAttempt extends Model
{
    const UPDATED_AT = null; // Tidak pakai updated_at
    
    protected $fillable = [
        'ip_address',
        'email',
        'user_agent',
        'successful',
    ];
    protected $casts = [
        'successful' => 'boolean',
        'created_at' => 'datetime',
    ];
    /**
     * Log login attempt
     */
    public static function log(string $email, string $ipAddress, ?string $userAgent, bool $successful): void
    {
        self::create([
            'email' => $email,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'successful' => $successful,
        ]);
    }
    /**
     * Get failed attempts count dalam timeframe tertentu
     */
    public static function getFailedAttempts(string $ipAddress, int $minutes = 1): int
    {
        return self::where('ip_address', $ipAddress)
            ->where('successful', false)
            ->where('created_at', '>=', Carbon::now()->subMinutes($minutes))
            ->count();
    }
    /**
     * Cleanup old attempts (optional - bisa dipanggil via scheduler)
     */
    public static function cleanup(int $days = 30): void
    {
        self::where('created_at', '<', Carbon::now()->subDays($days))->delete();
    }
}