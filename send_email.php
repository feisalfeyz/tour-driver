<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $subject = 'Check out this page';
    $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
    $headers = 'From: no-reply@yourdomain.com' . "\r\n" .
               'Reply-To: no-reply@yourdomain.com' . "\r\n" .
               'X-Mailer: PHP/' . phpversion();

    if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
        if (mail($to, $subject, $message, $headers)) {
            echo 'Email sent successfully';
        } else {
            echo 'Failed to send email';
        }
    } else {
        echo 'Invalid email address';
    }
}
?>
