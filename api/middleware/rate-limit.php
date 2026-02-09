<?php
// ============================================
// JSchoolAdmin — Simple File-Based Rate Limiter
// ============================================

/**
 * Rate limit by IP address
 * 
 * @param string $action   Action key (e.g., 'login')
 * @param int    $maxAttempts  Maximum attempts allowed
 * @param int    $windowSeconds  Time window in seconds
 */
function rateLimit(string $action = 'login', int $maxAttempts = 5, int $windowSeconds = 300): void {
    $ip      = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key     = md5($action . '_' . $ip);
    $tmpDir  = sys_get_temp_dir() . '/jschooladmin_ratelimit/';

    if (!is_dir($tmpDir)) {
        mkdir($tmpDir, 0755, true);
    }

    $file = $tmpDir . $key . '.json';

    $data = ['attempts' => [], 'blocked_until' => 0];

    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true) ?: $data;
    }

    $now = time();

    // Check if currently blocked
    if ($data['blocked_until'] > $now) {
        $remaining = $data['blocked_until'] - $now;
        jsonError("Too many attempts. Try again in {$remaining} seconds.", 429);
    }

    // Clean old attempts outside window
    $data['attempts'] = array_filter($data['attempts'], function ($ts) use ($now, $windowSeconds) {
        return ($now - $ts) < $windowSeconds;
    });

    // Check if over limit
    if (count($data['attempts']) >= $maxAttempts) {
        $data['blocked_until'] = $now + $windowSeconds;
        file_put_contents($file, json_encode($data));
        jsonError("Too many attempts. Try again in {$windowSeconds} seconds.", 429);
    }

    // Record this attempt
    $data['attempts'][] = $now;
    file_put_contents($file, json_encode($data));
}

/**
 * Clear rate limit for an IP after successful action
 */
function clearRateLimit(string $action = 'login'): void {
    $ip     = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key    = md5($action . '_' . $ip);
    $file   = sys_get_temp_dir() . '/jschooladmin_ratelimit/' . $key . '.json';

    if (file_exists($file)) {
        unlink($file);
    }
}
