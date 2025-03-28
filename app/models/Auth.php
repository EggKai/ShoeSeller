<?php
require_once __DIR__ . '/../../core/Security.php';
require_once __DIR__ . '/../../core/Model.php';

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
    public const ALLOWED_EXTENTIONS = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'avif' => 'image/avif'
    ];
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


    public function emailExists($email) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS count FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
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
    public function updateUser($id, $name, $email, $address, $profilePic = null) {
        if ($profilePic === null) { // Update without modifying the profile_pic field.
            $sql = "UPDATE users 
                    SET name = :name, email = :email, address = :address 
                    WHERE id = :id";
            $params = [
                'name'    => $name,
                'email'   => $email,
                'address' => $address,
                'id'      => $id,
            ];
        } else { // Update including the profile_pic field.
            $sql = "UPDATE users 
                    SET name = :name, email = :email, address = :address, profile_pic = :profile_pic 
                    WHERE id = :id";
            $params = [
                'name'        => $name,
                'email'       => $email,
                'address'     => $address,
                'profile_pic' => $profilePic,
                'id'          => $id,
            ];
        }
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Get user details by user ID.
     *
     * @param int $id
     * @return array|false Returns user data as an associative array, or false if user not found.
     */
    public function getUserById($id)
    {
        return $this->findById('users', $id);
    }

    public function getAllUsers() {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function deleteUser($userId) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $userId]);
    }
    public function getUserByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    /**
     * Create a new reset token record.
     *
     * @param int    $userId
     * @param string $token
     * @param string $expiresAt
     * @return bool
     */
    public function createResetToken($userId, $token, $expiresAt) {
        $stmt = $this->pdo->prepare("
            INSERT INTO reset_password (user_id, token, created_at, expires_at, used)
            VALUES (:user_id, :token, NOW(), :expires_at, 0)
        ");
        return $stmt->execute([
            'user_id'    => $userId,
            'token'      => $token,
            'expires_at' => $expiresAt
        ]);
    }

    /**
     * Get reset token record by token.
     *
     * @param string $token
     * @return array|false
     */
    public function getResetToken($token) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM reset_password
            WHERE token = :token
        ");
        $stmt->execute(['token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Mark token as used.
     *
     * @param int $id
     * @return bool
     */
    public function markTokenUsed($id) {
        $stmt = $this->pdo->prepare("
            UPDATE reset_password
            SET used = 1
            WHERE id = :id
        ");
        return $stmt->execute(['id' => $id]);
    }
    public function updatePassword($userId, $hashedPassword) {
        $stmt = $this->pdo->prepare("
            UPDATE users
            SET password = :password
            WHERE id = :id
        ");
        return $stmt->execute([
            'password' => $hashedPassword,
            'id'       => $userId
        ]);
    }
}