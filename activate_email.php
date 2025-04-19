<?php
if (!isset($_SESSION)) {
	session_start();
}

if (!isset($_SESSION['email_address']))
	header('location:index.php');

require 'phpmailer/PHPMailerAutoload.php';

$mail = new PHPMailer;
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'YOUR EMAIL';
$mail->Password = 'YOUR EMAIL PASSWORD';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$to = $_SESSION['email_address'];
$username = $_SESSION['username'];
$password = $_SESSION['password'];
$activation_code = $_SESSION['activation_code'];

$mail->setFrom('YOUR EMAIL', 'My Musical World');
$mail->addAddress($to);
$mail->isHTML(true);
$mail->Subject = 'Account Confirmation Message';

$verify_url = "http://localhost/webamnhac/My-Musical-World/verify.php?email_address=" . urlencode($to) . "&activation_code=" . urlencode($activation_code);

$mail->Body = "
    Thank You $username for signing up!<br>
    Your account has been created. You can log in with the following credentials after activating your account using the link below:<br><br>
    ------------------------<br>
    Username: $to<br>
    Password: $password<br>
    ------------------------<br><br>
    <a href='" . $verify_url . "' style='padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Click here to activate your account</a>
    
    <br><br>
        
    <pre>Thanks,
    <b>From My Musical World with love</b></pre>
";

?>

<!DOCTYPE html>
<html>

<head>
	<title></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="img/KEERTHANA KUTEERA LOGO-ICON-01.png" type="image/x-icon">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
	<?php
	if (!$mail->send()) {
		echo "<script>
        Swal.fire({
            title: 'Error!',
            icon: 'error',
            text: 'Message could not be sent. Mailer Error: " . addslashes($mail->ErrorInfo) . "'
        });
    </script>";
	} else {
		echo "<script>
        Swal.fire({
            title: 'Success',
            icon: 'success',
            html: '<b>Thank you $username! A confirmation link has been sent to your email address: $to</b>'
        }).then(() => {
            window.location.href = 'index.php';
        });
    </script>";
	}
	?>
</body>

</html>