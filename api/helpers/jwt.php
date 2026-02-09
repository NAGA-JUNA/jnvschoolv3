<?php
// ============================================
// JSchoolAdmin — JWT Token Helper (HMAC-SHA256)
// No external libraries needed
// ============================================

function base64UrlEncode(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64UrlDecode(string $data): string {
    return base64_decode(strtr($data, '-_', '+/'));
}

/**
 * Create a JWT token
 */
function jwtEncode(array $payload): string {
    $header = base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));

    $payload['iat'] = $payload['iat'] ?? time();
    $payload['exp'] = $payload['exp'] ?? (time() + JWT_EXPIRY);

    $payloadEncoded = base64UrlEncode(json_encode($payload));
    $signature      = base64UrlEncode(
        hash_hmac('sha256', "$header.$payloadEncoded", JWT_SECRET, true)
    );

    return "$header.$payloadEncoded.$signature";
}

/**
 * Decode and verify a JWT token.
 * Returns the payload array on success, or null on failure.
 */
function jwtDecode(string $token): ?array {
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return null;
    }

    [$header, $payload, $signature] = $parts;

    // Verify signature
    $expected = base64UrlEncode(
        hash_hmac('sha256', "$header.$payload", JWT_SECRET, true)
    );

    if (!hash_equals($expected, $signature)) {
        return null;
    }

    $data = json_decode(base64UrlDecode($payload), true);
    if (!$data) {
        return null;
    }

    // Check expiry
    if (isset($data['exp']) && $data['exp'] < time()) {
        return null;
    }

    return $data;
}
