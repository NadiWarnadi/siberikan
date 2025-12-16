<?php

namespace App\Services;

use App\Models\SecurityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SecurityLogService
{
    /**
     * Log login success
     */
    public static function logLoginSuccess($pengguna)
    {
        self::createLog(
            $pengguna->id,
            'login_success',
            'User berhasil login: ' . $pengguna->email,
        );

        Log::channel('security')->info('Login Success', [
            'user_id' => $pengguna->id,
            'email' => $pengguna->email,
            'ip' => request()->ip(),
            'time' => now(),
        ]);
    }

    /**
     * Log login failed
     */
    public static function logLoginFailed($email, $reason = 'Invalid credentials')
    {
        self::createLog(
            null,
            'login_failed',
            'Login gagal untuk email: ' . $email . ' - ' . $reason,
        );

        Log::channel('security')->warning('Login Failed', [
            'email' => $email,
            'reason' => $reason,
            'ip' => request()->ip(),
            'time' => now(),
        ]);
    }

    /**
     * Log brute force attempt
     */
    public static function logBruteForce($email, $attempt = 1)
    {
        $description = "Brute force attempt #$attempt untuk email: $email";
        
        self::createLog(
            null,
            'login_brute_force',
            $description,
        );

        Log::channel('security')->alert('Brute Force Attempt', [
            'email' => $email,
            'attempt' => $attempt,
            'ip' => request()->ip(),
            'time' => now(),
        ]);
    }

    /**
     * Log unauthorized access
     */
    public static function logUnauthorizedAccess($resource, $reason = 'Permission denied')
    {
        self::createLog(
            Auth::id(),
            'unauthorized_access',
            'Unauthorized access attempt ke: ' . $resource . ' - ' . $reason,
        );

        Log::channel('security')->warning('Unauthorized Access', [
            'user_id' => Auth::id(),
            'resource' => $resource,
            'reason' => $reason,
            'ip' => request()->ip(),
            'url' => request()->url(),
            'time' => now(),
        ]);
    }

    /**
     * Log file upload
     */
    public static function logFileUpload($filename, $size, $mimetype, $status = 'success')
    {
        $description = "File upload $status: $filename ({$size} bytes, $mimetype)";
        
        self::createLog(
            Auth::id(),
            'file_upload',
            $description,
        );

        Log::channel('security')->info('File Upload', [
            'user_id' => Auth::id(),
            'filename' => $filename,
            'size' => $size,
            'mimetype' => $mimetype,
            'status' => $status,
            'time' => now(),
        ]);
    }

    /**
     * Log SQL injection attempt
     */
    public static function logSQLInjectionAttempt($input, $detected_by = 'WAF')
    {
        self::createLog(
            Auth::id(),
            'sql_injection_attempt',
            "SQL Injection attempt detected by $detected_by: " . substr($input, 0, 100),
        );

        Log::channel('security')->alert('SQL Injection Attempt', [
            'user_id' => Auth::id(),
            'input' => substr($input, 0, 100),
            'detected_by' => $detected_by,
            'ip' => request()->ip(),
            'time' => now(),
        ]);
    }

    /**
     * Log XSS attempt
     */
    public static function logXSSAttempt($input, $field = 'unknown')
    {
        self::createLog(
            Auth::id(),
            'xss_attempt',
            "XSS attempt di field $field: " . substr($input, 0, 100),
        );

        Log::channel('security')->alert('XSS Attempt', [
            'user_id' => Auth::id(),
            'field' => $field,
            'input' => substr($input, 0, 100),
            'ip' => request()->ip(),
            'time' => now(),
        ]);
    }

    /**
     * Log CSRF violation
     */
    public static function logCSRFViolation()
    {
        self::createLog(
            Auth::id(),
            'csrf_violation',
            'CSRF token validation failed',
        );

        Log::channel('security')->warning('CSRF Violation', [
            'user_id' => Auth::id(),
            'ip' => request()->ip(),
            'url' => request()->url(),
            'time' => now(),
        ]);
    }

    /**
     * Log password change
     */
    public static function logPasswordChange($pengguna_id)
    {
        self::createLog(
            $pengguna_id,
            'password_change',
            'Password berhasil diubah',
        );

        Log::channel('security')->info('Password Changed', [
            'user_id' => $pengguna_id,
            'time' => now(),
        ]);
    }

    /**
     * Log permission denied
     */
    public static function logPermissionDenied($action, $resource = '')
    {
        self::createLog(
            Auth::id(),
            'permission_denied',
            "Permission denied untuk action: $action pada: $resource",
        );

        Log::channel('security')->warning('Permission Denied', [
            'user_id' => Auth::id(),
            'action' => $action,
            'resource' => $resource,
            'ip' => request()->ip(),
            'time' => now(),
        ]);
    }

    /**
     * Log invalid input
     */
    public static function logInvalidInput($field, $value, $error = '')
    {
        $description = "Invalid input di field $field: " . substr($value, 0, 50);
        if ($error) {
            $description .= " - $error";
        }

        self::createLog(
            Auth::id(),
            'invalid_input',
            $description,
        );

        Log::channel('security')->info('Invalid Input', [
            'user_id' => Auth::id(),
            'field' => $field,
            'value' => substr($value, 0, 50),
            'error' => $error,
            'time' => now(),
        ]);
    }

    /**
     * Log logout
     */
    public static function logLogout()
    {
        self::createLog(
            Auth::id(),
            'logout',
            'User berhasil logout',
        );

        Log::channel('security')->info('Logout', [
            'user_id' => Auth::id(),
            'time' => now(),
        ]);
    }

    /**
     * Create log entry
     */
    private static function createLog($pengguna_id, $tipe_event, $deskripsi)
    {
        try {
            SecurityLog::create([
                'pengguna_id' => $pengguna_id,
                'tipe_event' => $tipe_event,
                'deskripsi' => $deskripsi,
                'ip_address' => request()->ip() ?? '0.0.0.0',
                'user_agent' => request()->userAgent() ?? 'Unknown',
                'request_url' => request()->url() ?? '',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create security log: ' . $e->getMessage());
        }
    }

    /**
     * Get login attempts dari IP address
     */
    public static function getLoginAttempts($ip, $minutes = 15)
    {
        return SecurityLog::where('tipe_event', 'login_failed')
            ->where('ip_address', $ip)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    /**
     * Check if IP should be blocked for brute force
     */
    public static function shouldBlockIP($ip, $maxAttempts = 5, $minutes = 15)
    {
        $attempts = self::getLoginAttempts($ip, $minutes);
        return $attempts >= $maxAttempts;
    }
}
