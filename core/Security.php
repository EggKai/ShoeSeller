<?php
// app/models/Security.php

class Csrf
{
    /**
     * Generate a CSRF token and store it in session if it doesn't already exist.
     *
     * @return string CSRF token.
     */
    public static function generateToken()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validate the provided CSRF token against the token stored in session.
     *
     * @param string $token The token to validate.
     * @return bool True if valid; otherwise false.
     */
    public static function validateToken($token)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

/**
 * Generate a random token using CHARSET.
 *
 * @return string
 */
function generate_token($charset = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', $length = 255)
{
    $pieces = [];
    $max = mb_strlen($charset, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces[] = $charset[random_int(0, $max)];
    }
    return implode('', $pieces);
}
