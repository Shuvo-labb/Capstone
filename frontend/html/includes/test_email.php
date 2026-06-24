<?php
// Test email functionality with PHPMailer
require_once __DIR__ . '/EmailHelper.php';

$emailHelper = new EmailHelper();

// Test sending a simple email
$to = 'ggg@gg.com';
$subject = 'Test Email - PHPMailer';
$body = '
<html>
<body>
    <h2>Test Email</h2>
    <p>This is a test email sent using PHPMailer via Mailtrap.</p>
    <p>If you receive this, the email configuration is working correctly!</p>
</body>
</html>';

$result = $emailHelper->send($to, $subject, $body);

if ($result) {
    echo "Email sent successfully! Check your Mailtrap inbox.";
} else {
    echo "Failed to send email. Check error logs.";
}
