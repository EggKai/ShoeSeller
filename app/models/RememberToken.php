<?php
require_once __DIR__ . './Auth.php';
require_once __DIR__ . '/../../core/Model.php';

class RememberToken extends Model {

    /**
     * Store a new remember me token.
     *
     * @param int    $userId
     * @param string $tokenHash
     * @param string $expiresAt (formatted as 'Y-m-d H:i:s')
     * @return bool
     */
    public function storeToken($userId, $tokenHash, $expiresAt) {
        $stmt = $this->pdo->prepare("
            INSERT INTO user_remember_tokens (user_id, token_hash, expires_at)
            VALUES (:user_id, :token_hash, :expires_at)
        ");
        return $stmt->execute([
            'user_id'    => $userId,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt
        ]);
    }

    /**
     * Retrieve the most recent valid token record for a user.
     *
     * @param int    $userId
     * @param string $token   The raw token from the cookie.
     * @return array|false    The token record if valid, false otherwise.
     */
    public function getTokenRecord($userId, $token) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM user_remember_tokens
            WHERE user_id = :user_id AND expires_at > NOW()
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($record && password_verify($token, $record['token_hash'])) {
            return $record;
        }
        return false;
    }

    /**
     * Optionally, delete a token by its ID.
     *
     * @param int $tokenId
     * @return bool
     */
    public function deleteToken($tokenId) {
        $stmt = $this->pdo->prepare("DELETE FROM user_remember_tokens WHERE id = :id");
        return $stmt->execute(['id' => $tokenId]);
    }

    public function deleteTokensByUser($userId) {
        $stmt = $this->pdo->prepare("DELETE FROM user_remember_tokens WHERE user_id = :user_id");
        return $stmt->execute(['user_id' => $userId]);
    }
    
}

if (!isset($_SESSION['user']) && isset($_COOKIE['remember_me'])) {
    $cookieData = json_decode($_COOKIE['remember_me'], true);
    if ($cookieData && isset($cookieData['user_id'], $cookieData['token'])) {
        $userId = $cookieData['user_id'];
        $token  = $cookieData['token'];
        $rememberModel = new RememberToken();
        $record = $rememberModel->getTokenRecord($userId, $token);
        if ($record) {
            $userModel = new Auth();
            $_SESSION['user'] = $userModel->getUserById($userId);
        } else {
            setcookie('remember_me', '', time() - 3600, '/', '', true, true); // Invalid token - clear the cookie.
        }
    }
}
