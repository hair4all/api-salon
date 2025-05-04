<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtHelper
{
    /**
     * Generate a JWT token.
     *
     * @param  array  $payload
     * @param  int    $expiresIn  Token expiration in seconds (default 3600 seconds)
     * @return string
     */
    public static function generateToken(array $payload, int $expiresIn = 19200): string
    {
        $secretKey = env('JWT_SECRET');

        $issuedAt = time();
        $expire = $issuedAt + $expiresIn;

        // Merge custom payload with standard claims
        $payload = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $expire,
        ]);

        // Encode the payload to create the JWT token (using HS256 algorithm)
        return JWT::encode($payload, $secretKey, 'HS256');
    }

    /**
     * Verify and decode a JWT token.
     *
     * @param  string  $token
     * @return array
     *
     * @throws Exception If the token is invalid.
     */
    public static function verifyToken(string $token): array
    {
        $secretKey = env('JWT_SECRET');

        try {
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            // Convert the decoded object into an associative array
            return (array) $decoded;
        } catch (Exception $e) {
            throw new Exception('Invalid token');
        }
    }
}
