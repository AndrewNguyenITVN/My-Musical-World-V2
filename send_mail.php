<?php
require 'phpmailer/PHPMailerAutoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json; charset=utf-8'); // ✅ Thêm dòng này
    // Lấy dữ liệu từ form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];

    $mail = new PHPMailer;

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'mymusicworld.2025@gmail.com';                 // SMTP username
    $mail->Password = 'vtpb htgv btuk xqpa';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    $mail->setFrom($email, $name);
    $mail->addAddress('mymusicworld.2025@gmail.com');     // Add a recipient

    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = "New message from: " . $name;
    $mail->Body = "
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Phone number:</strong> $phone</p>
        <p><strong>Message:</strong></p>
        <p>$message</p>
    ";

    if (!$mail->send()) {
        echo json_encode(['status' => 'error', 'message' => 'Email could not be sent: ' . $mail->ErrorInfo]);
    } else {
        echo json_encode(['status' => 'success', 'message' => 'Email sent successfully!']);
    }
    exit;
}
