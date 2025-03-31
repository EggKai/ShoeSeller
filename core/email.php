<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/log.php';
/**
 * Send an email using PHPMailer configured for Gmail SMTP.
 *
 * @param string $toEmail   Recipient email.
 * @param string $toName    Recipient name.
 * @param string $subject   Email subject.
 * @param string $htmlBody  HTML content of the email.
 * @param string $altBody   Plain text alternative content.
 * @return bool             True on success, false on failure.
 */
function sendEmail($toEmail, $toName, $subject, $htmlBody, $altBody) {
    $mail = new PHPMailer(true);
    try {
        // Load environment variables as needed
        $protocol = $_ENV['PROTOCOL'];
        $domain   = $_ENV['DOMAIN'];

        // SMTP configuration for Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['EMAIL'];
        $mail->Password   = $_ENV['EMAIL_APP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Set sender and recipient
        $mail->setFrom($_ENV['EMAIL'], 'Shoe Seller');
        $mail->addAddress($toEmail, $toName);
        
        // Set email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = $altBody;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Use your custom log function to record errors.
        logError("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Build and send the receipt email.
 *
 * @param array $order      The order details.
 * @param array $orderItems The items in the order.
 * @return bool             True on success, false on failure.
 */
function sendReceiptEmail($order, $orderItems) {
    $protocol = $_ENV['PROTOCOL'];
    $domain   = $_ENV['DOMAIN'];

    // Build HTML receipt content inline.
    $html = '<div style="font-family: Lucida Sans, sans-serif; max-width:600px; margin:0 auto; padding:20px; border:1px solid #ccc;">';
    $html .= '<h1 style="text-align:center;">Order Receipt</h1>';
    $html .= '<p><strong>Order Number:</strong> ' . htmlspecialchars($order['id']) . '</p>';
    $html .= '<p><strong>Date:</strong> ' . htmlspecialchars($order['created_at']) . '</p>';
    $html .= '<p><strong>Email:</strong> ' . htmlspecialchars($order['email']) . '</p>';
    $html .= '<h2 style="margin-top:20px;">Items</h2>';
    $html .= '<table style="width:100%; border-collapse:collapse;">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th style="border:1px solid #ccc; padding:8px;">Product</th>';
    $html .= '<th style="border:1px solid #ccc; padding:8px;">Size</th>';
    $html .= '<th style="border:1px solid #ccc; padding:8px;">Quantity</th>';
    $html .= '<th style="border:1px solid #ccc; padding:8px;">Unit Price</th>';
    $html .= '<th style="border:1px solid #ccc; padding:8px;">Total</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    
    if (!empty($orderItems)) {
        foreach ($orderItems as $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $html .= '<tr>';
            $html .= '<td style="border:1px solid #ccc; padding:8px;">' . htmlspecialchars($item['name']) . '</td>';
            $html .= '<td style="border:1px solid #ccc; padding:8px;">' . htmlspecialchars($item['size']) . '</td>';
            $html .= '<td style="border:1px solid #ccc; padding:8px;">' . htmlspecialchars($item['quantity']) . '</td>';
            $html .= '<td style="border:1px solid #ccc; padding:8px;">$' . number_format($item['price'], 2) . '</td>';
            $html .= '<td style="border:1px solid #ccc; padding:8px;">$' . number_format($itemTotal, 2) . '</td>';
            $html .= '</tr>';
        }
    } else {
        $html .= '<tr><td colspan="5" style="border:1px solid #ccc; padding:8px;">No items found.</td></tr>';
    }
    
    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '<h3 style="text-align:right; margin-top:20px;">Total: $' . number_format($order['total_price'], 2) . '</h3>';
    
    // Add a "View Receipt" button link
    $html .= '<div style="text-align:center; margin-top:20px;">';
    $html .= '<a href="' . $protocol . '://' . $domain . '/index.php?url=checkout/receipt&order_id=' . htmlspecialchars($order['id']) . '" ';
    $html .= 'style="display:inline-block; background-color:#000; color:#fff; padding:0.75rem 1.5rem; border-radius:4px; text-decoration:none;">';
    $html .= 'View Receipt</a>';
    $html .= '</div>';
    
    $html .= '</div>';

    // Build alternative plain text body.
    $altBody = "Order Receipt\nOrder Number: {$order['id']}\nDate: {$order['created_at']}\nEmail: {$order['email']}\nTotal: $" . number_format($order['total_price'], 2);
    
    // Send the email.
    return sendEmail($order['email'], explode('@', $order['email'])[0], "Order Receipt: Order #{$order['id']}", $html, $altBody);
}


/**
 * Send a password reset email to the user.
 *
 * @param string $userEmail The recipient's email address.
 * @param string $userName  The recipient's name.
 * @param string $token     The unique reset token.
 * @return bool             True on success, false on failure.
 */
function sendPasswordResetEmail($userEmail, $userName, $token) {
    // Load protocol and domain from environment variables.
    $protocol = $_ENV['PROTOCOL'];
    $domain   = $_ENV['DOMAIN'];
    
    // Construct the password reset URL.
    $resetUrl = $protocol . '://' . $domain . '/auth/reset_password&token=' . urlencode($token);
    
    // Build HTML email content.
    $html  = '<div style="font-family: Arial, sans-serif; max-width:600px; margin:0 auto; padding:20px;">';
    $html .= '<h1>Password Reset Request</h1>';
    $html .= '<p>You have requested to reset your password. Please click the button below to proceed. This link will expire in <b>one hour</b>.</p>';
    $html .= '<div style="text-align: center; margin-top: 20px;">';
    $html .= '<a href="' . $resetUrl . '" style="display: inline-block; background-color: #000; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Reset Password</a>';
    $html .= '</div>';
    $html .= '<p style="margin-top: 20px;">If you did not request a password reset, please ignore this email.</p>';
    $html .= '</div>';
    
    // Build alternative plain text email content.
    $altBody  = "Password Reset Request\n\n";
    $altBody .= "You have requested to reset your password. Please use the following link to reset your password:\n";
    $altBody .= $resetUrl . "\n\n";
    $altBody .= "If you did not request a password reset, please ignore this email.";
    
    // Send the email using the sendEmail function.
    return sendEmail($userEmail, $userName, "Password Reset Request", $html, $altBody);
}
