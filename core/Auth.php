<?php
// app/models/Auth.php

require_once __DIR__ . '/Model.php';

class Auth extends Model
{
    protected const FILTERS = array(
        'email' => [
            'filter' => FILTER_SANITIZE_EMAIL,
            'flag' => FILTER_VALIDATE_EMAIL,
        ],
        'password' => [
            'flags' => FILTER_REQUIRE_SCALAR,
        ],
        'name' => FILTER_SANITIZE_SPECIAL_CHARS,
        // 'phone' => [
        //     'filter' => FILTER_VALIDATE_REGEXP,
        //     'options' => array('regexp' => "/^([+]([0-9]{2}))? ?[0-9]{4} ?[0-9]{4}/"),
        // ]
    );
    public const ALLOWED_EXTENTIONS = ['jpg', 'jpeg', 'png', 'avif'];
    /**
     * Authenticate a user by email and password.
     *
     * @param string $email    User's email address.
     * @param string $password Plain-text password.
     * @return array|false     Returns user data array if valid, or false if authentication fails.
     */
    public function authenticate($email, $password)
    {
        // Sanitize and validate email.
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify the password using password_verify.
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    /**
     * Filter incoming data using defined filters.
     *
     * @param array $data
     * @return array Sanitized data.
     */
    private function filter($data)
    {
        return filter_var_array($data, self::FILTERS);
    }

    /**
     * Register a new user.
     *
     * @param string $name        The user's full name.
     * @param string $email       The user's email address.
     * @param string $password    Plain-text password (will be hashed).
     * @param string $address     Optional: the user's address.
     * @param mixed  $profile_pic Optional: the user's profile picture.
     * @param string $user_type   Optional: user type, defaults to 'user'.
     * @return int|false          The newly created user ID on success, or false on failure.
     */
    public function register($name, $email, $password, $address = '', $profile_pic = null, $user_type = 'user')
    {
        // Sanitize input fields.
        $name = filter_var(trim($name), FILTER_SANITIZE_STRING);
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        $address = filter_var(trim($address), FILTER_SANITIZE_STRING);

        // Validate email.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Hash the password for secure storage.
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("
            INSERT INTO users (name, email, password, address, profile_pic, user_type)
            VALUES (:name, :email, :password, :address, :profile_pic, :user_type)
        ");

        $params = [
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'address' => $address,
            'profile_pic' => $profile_pic,
            'user_type' => $user_type
        ];

        if ($stmt->execute($params)) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    /**
     * Get user details by user ID.
     *
     * @param int $id
     * @return array|false Returns user data as an associative array, or false if user not found.
     */
    public function getUserById($id)
    {
        return $this->findById('user', $id);
    }
}

class Csrf {
    /**
     * Generate a CSRF token and store it in session if it doesn't already exist.
     *
     * @return string CSRF token.
     */
    public static function generateToken() {
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
    public static function validateToken($token) {
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
