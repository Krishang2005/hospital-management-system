<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();
requireAdmin();

$method = $_SERVER["REQUEST_METHOD"];

if ($method == "POST") {
    $input = getJsonInput();
    $user_id = intval($input['user_id']);
    $email = $conn->real_escape_string($input['email']);
    $subject = $conn->real_escape_string($input['subject']);
    $message = $conn->real_escape_string($input['message']);
    
    if (empty($email) || empty($subject) || empty($message) || $user_id <= 0) {
        sendJsonResponse('error', 'Missing required fields');
    }

    // Save internal notification first
    try {
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("is", $user_id, $message);
            $stmt->execute();
        } else {
            // Log error but don't crash the email flow
            file_put_contents('../../logs/error.log', "[" . date('Y-m-d H:i:s') . "] DB Prep Error: " . $conn->error . "\n", FILE_APPEND);
        }
    } catch (Exception $e) {
        file_put_contents('../../logs/error.log', "[" . date('Y-m-d H:i:s') . "] DB Exec Error: " . $e->getMessage() . "\n", FILE_APPEND);
    }

    // Modern HTML Email Header
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: KCG Multispecialist Hospital <noreply@hospital-kcg.com>' . "\r\n";

    // Prepare full message
    $full_message = "
    <html>
    <head>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
            .header { background: #007bff; color: white; padding: 15px; border-radius: 10px 10px 0 0; text-align: center; }
            .content { padding: 20px; }
            .footer { font-size: 12px; color: #888; text-align: center; margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>KCG Multispecialist Hospital</h2>
            </div>
            <div class='content'>
                <p>" . nl2br($message) . "</p>
            </div>
            <div class='footer'>
                This is an automated notification. Please do not reply directly to this email.
            </div>
        </div>
    </body>
    </html>
    ";

    // Log the email first
    if (!file_exists('../../logs')) {
        mkdir('../../logs', 0777, true);
    }
    $log_entry = "[" . date('Y-m-d H:i:s') . "] To: $email | Subject: $subject | Message: $message\n";
    file_put_contents('../../logs/emails.log', $log_entry, FILE_APPEND);

    // On local XAMPP/WAMP environments, mail() often hangs if not configured.
    // We will return success as long as it's logged, to avoid the 'Sending...' hang.
    // To enable actual email, configure SMTP in php.ini and uncomment the line below.
    // @mail($email, $subject, $full_message, $headers);
    
    sendJsonResponse('success', 'Email processed and logged in logs/emails.log');
} else {
    sendJsonResponse('error', 'Invalid request method');
}
?>
