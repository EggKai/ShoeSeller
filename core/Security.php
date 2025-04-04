<?php
require_once __DIR__ . '/log.php';
function getIP()
{ //get IP addr w elvis operators
    return isset($_SERVER['HTTP_CLIENT_IP'])
        ? $_SERVER['HTTP_CLIENT_IP']
        : (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
            ? $_SERVER['HTTP_X_FORWARDED_FOR']
            : $_SERVER['REMOTE_ADDR']);
}
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
        if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
            return true;
        } 
        logAction('ALERT '.getIP().'('.(isset($_SESSION['user'])?'"'.$_SESSION['user']['name'].'" id:'.$_SESSION['user']['id']:'guest').') has failed CSRF check');
        return false;
    }
}

// function isUserType($user_type){
//     return isset($_SESSION['user']) && $_SESSION['user']['user_type'] === $user_type;
// }

// function isAdmin() {
//     return isUserType('admin');
// }
// function isEmployee() {
//     return isUserType('employee');
// }
// function isUser() {
//     return isUserType('user');
// }

function convertImageToJpeg($sourcePath, $quality = 60) {
    $imageType = exif_imagetype($sourcePath);

    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($sourcePath);
            break;
        default:
            throw new Exception("Unsupported image type.");
    }

    ob_start();
    imagejpeg($image, null, $quality);
    $jpegData = ob_get_clean();
    imagedestroy($image);
    return $jpegData;
}
function google_recaptcha($recaptcha){
    $secretKey = $_ENV['GOOGLE_CAPTCHA_SECRET_KEY'];
    $remoteIp = $_SERVER['REMOTE_ADDR'];
    $verifyUrl = "https://www.google.com/recaptcha/api/siteverify?secret=" . urlencode($secretKey) . "&response=" . urlencode($recaptcha) . "&remoteip=" . urlencode($remoteIp);

    // Use file_get_contents to send the request.
    $responseData = file_get_contents($verifyUrl);
    return json_decode($responseData, true);
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
