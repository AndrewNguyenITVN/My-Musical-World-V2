<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7"></script>
<?php
// Thêm extension session 
if (extension_loaded('session')) {
    if (!isset($_SESSION)) {
        session_start();
    }
} else {
    die('PHP Session extension is not loaded');
}

if (isset($_POST['register'])) {

    include('connection.php');

    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $mobile_number = mysqli_real_escape_string($conn, trim($_POST['mobile_number']));
    $email_address = mysqli_real_escape_string($conn, trim($_POST['email_address']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));
    $confirm_password = mysqli_real_escape_string($conn, trim($_POST['confirm_password']));

    // Kiểm tra mật khẩu trống
    if (empty($password) || empty($confirm_password)) {
        echo '<script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                sweetAlert("Oops...", "Please enter your password!", "error");
            });
        </script>';
        return;
    }

    // Kiểm tra độ dài mật khẩu
    if (strlen($password) < 6) {
        echo '<script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                sweetAlert("Oops...", "Password must be at least 6 characters!", "error");

            });
        </script>';
        return;
    }

    // Kiểm tra mật khẩu trùng khớp
    if ($password !== $confirm_password) {
        echo '<script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                sweetAlert("Oops...", "Passwords do not match!", "error");

            });
        </script>';
        return;
    }

    if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email_address)) {
        if (preg_match("/^(\+?\d{1,4})?[-.\s]?(\()?(\d{1,3})(?(2)\))[-.\s]?\d{1,4}[-.\s]?\d{1,9}$/", $mobile_number)) {

            $sql_email = "SELECT email_address FROM user WHERE email_address='$email_address'";
            $result_email = mysqli_query($conn, $sql_email);

            $sql_mobile = "SELECT mobile_number FROM user WHERE mobile_number='$mobile_number'";
            $result_mobile = mysqli_query($conn, $sql_mobile);

            if (mysqli_num_rows($result_email) > 0) {
                echo '<script type="text/javascript">';
                echo 'setTimeout(function () { 
                            Swal.fire({
                                title: "Oops...",
                                text: "Email Address ' . $email_address . ' is already exists!. Please try another one.",
                                icon: "error",
                                confirmButtonText: "OK"
                            }).then(() => {
                                window.location.href = "index.php"; // Chuyển hướng sau khi nhấn OK
                            });
                          }, 500);';
                echo '</script>';
            } else if (mysqli_num_rows($result_mobile) > 0) {
                echo '<script type="text/javascript">';
                echo 'setTimeout(function () { 
                            Swal.fire({
                                title: "Oops...",
                                text: "Mobile number ' . $mobile_number . ' is already exists!. Please try another one.",
                                icon: "error",
                                confirmButtonText: "OK"
                            }).then(() => {
                                window.location.href = "index.php"; // Chuyển hướng sau khi nhấn OK
                            });
                          }, 500);';
                echo '</script>';
            } else {
                $activation_code = hash('sha256', mt_rand(0, 1000));
                $hash_password = md5($password);

                $sql = "INSERT INTO user (`username`, `password`, `mobile_number`, `email_address`, `activation_code`) 
                            VALUES ('$username', '$hash_password', '$mobile_number', '$email_address', '$activation_code')";

                $result = mysqli_query($conn, $sql);

                if (!$result) {
                    die("Error while updating!!!...") . mysqli_error($conn);
                } else {
                    $_SESSION['username'] = $username;
                    $_SESSION['mobile_number'] = $mobile_number;
                    $_SESSION['email_address'] = $email_address;
                    $_SESSION['activation_code'] = $activation_code;
                    $_SESSION['password'] = $password;

                    include('activate_email.php');
                }
            }
        } else {
            //invalid mobile number error message
            echo '<script type="text/javascript">';
            echo 'setTimeout(function () { sweetAlert("Oops...","Mobile number ' . $mobile_number . ' is invalid!","error");';
            echo '}, 500);</script>';
        }
    } else {
        //email address invalid error messaage
        echo '<script type="text/javascript">';
        echo 'setTimeout(function () { sweetAlert("Oops...","Email address ' . $email_address . ' is invalid!","error");';
        echo '}, 500);</script>';
    }
}


if (isset($_POST['login'])) {

    session_start(); 

    include('connection.php');

    $email_address = mysqli_real_escape_string($conn, $_POST['email_address']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $hash_password = md5($password);

    $sql = "SELECT * FROM user WHERE email_address = '$email_address' AND password = '$hash_password' ";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        echo '<script type="text/javascript">';
        echo 'setTimeout(function () { sweetAlert("Warning...","Error while loggin in!..","warning");';
        echo '}, 500);</script>';
    } else {
        $row = mysqli_fetch_array($result);
        $count = mysqli_num_rows($result);
        $username = $row['username'];
        $user_id = $row['user_id']; 

        if ($count == 1) {
            if ($row['confirm_status'] == 0) {
                echo '<script type="text/javascript">';
                echo 'setTimeout(function () { sweetAlert("Warning...","Please activate your account first!..","warning");';
                echo '}, 500);</script>';
            } else {
                $_SESSION['username'] = $username;
                $_SESSION['email_address'] = $email_address;
                $_SESSION['user_id'] = $user_id; 

                if ($email_address == 'admin@gmail.com' && $row['password'] == '21232f297a57a5a743894a0e4a801fc3') {
                    header('location:admin_page.php');
                } else {
                    header('location:profile.php');
                }
            }
        } else {
            echo '<script type="text/javascript">';
            echo 'setTimeout(function () { sweetAlert("Oops...","Wrong username or Password!...","error");';
            echo '}, 500);</script>';
        }
    }
}

if (isset($_POST['forgot'])) {
    include('connection.php');

    $email_address = mysqli_real_escape_string($conn, $_POST['email_address']);
    $sql = "SELECT email_address FROM user WHERE email_address = '$email_address'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 0) {
        // Email không tồn tại
?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                sweetAlert({
                    title: 'Email Not Found',
                    text: 'Email address <?php echo $email_address; ?> does not exist!',
                    type: 'error',
                    showCancelButton: false,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK',
                    closeOnConfirm: true
                }, function() {
                    document.getElementById('ForgotPasswordModal').style.display = 'none';
                    $('.modal-backdrop').remove();
                });
            });
        </script>
        <?php
    } else {
        require 'phpmailer/PHPMailerAutoload.php';

        // Tạo token ngẫu nhiên và thời gian hết hạn
        $reset_token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Lưu token vào database
        $update_sql = "UPDATE user SET reset_token = '$reset_token', reset_token_expiry = '$expiry' 
                      WHERE email_address = '$email_address'";
        mysqli_query($conn, $update_sql);

        // Lấy thông tin username
        $sql = "SELECT username FROM user WHERE email_address = '$email_address'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        $username = $row['username'];

        $reset_link = "http://localhost/webamnhac/My-Musical-World/reset_password.php?token=" . $reset_token . "&email=" . urlencode($email_address);

        $mail = new PHPMailer;
        // $mail->SMTPDebug = 3;                               // Enable verbose debug output

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'mymusicworld.2025@gmail.com';                 // SMTP username
        $mail->Password = 'vtpb htgv btuk xqpa';                         // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to
        $to = $email_address;
        $mail->setFrom('mymusicworld.2025@gmail.com', 'My Musical World');
        $mail->addAddress($to);     // Add a recipient
        $mail->SMTPDebug = 0;  // Hiển thị chi tiết lỗi SMTP
        //$mail->Debugoutput = 'html';


        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'Reset Your Account';
        $mail->Body = "
        <br><br>
        <b>Hello, " . $username . ",</b><br>
        You recently requested to reset your password for your My Musical World account. Click the button below to reset it. <br>
        <br><br>
        <a href='" . $reset_link . "' style='padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Reset Password</a>
        <br><br>
        If you did not request to reset your password please ignore this email or reply to let us know.<br><br>
        <pre>Thanks,
        <b>Musical World</b></pre>";

        if (!$mail->send()) {
            // Gửi email thất bại
        ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    sweetAlert({
                        title: 'Email Sending Failed',
                        text: 'Error while sending email. Please check your internet connection!',
                        type: 'error',
                        showCancelButton: false,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK',
                        closeOnConfirm: true
                    }, function() {
                        document.getElementById('ForgotPasswordModal').style.display = 'none';
                        $('.modal-backdrop').remove();
                    });
                });
            </script>
        <?php
        } else {
            // Gửi email thành công
        ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    sweetAlert({
                        title: 'Email Sent!',
                        text: 'A password reset link has been sent to <?php echo $email_address; ?>. Please check your email.',
                        type: 'success',
                        showCancelButton: false,
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'OK',
                        closeOnConfirm: true
                    }, function() {
                        document.getElementById('ForgotPasswordModal').style.display = 'none';
                        $('.modal-backdrop').remove();
                    }).then (function() {
                        window.location.href = 'index.php';
                    });
                });
            </script>
<?php
        }
    }
}

// Kiểm tra token và email từ URL
if (isset($_GET['token']) && isset($_GET['email'])) {
    $token = $_GET['token'];
    $email = $_GET['email'];

    // Kiểm tra token có hợp lệ và chưa hết hạn
    $sql = "SELECT * FROM user WHERE email_address = '$email' 
            AND reset_token = '$token' 
            AND reset_token_expiry > NOW()";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 0) {
        header('location:index.php');
        exit();
    }

    // Nếu token hợp lệ, lưu email vào session
    session_start();
    $_SESSION['email_address'] = $email;
    $_SESSION['reset_token'] = $token;
}

// Xử lý khi form reset password được submit
if (isset($_POST['reset'])) {
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $email = $_SESSION['email_address'];
    $token = $_SESSION['reset_token'];

    if ($password == $confirm_password) {
        $hash_password = md5($password);

        // Cập nhật mật khẩu mới và xóa token
        $sql = "UPDATE user SET 
                password = '$hash_password',
                reset_token = NULL,
                reset_token_expiry = NULL 
                WHERE email_address = '$email' 
                AND reset_token = '$token'";

        $result = mysqli_query($conn, $sql);

        if ($result) {
            echo '<script type="text/javascript">';
            echo 'setTimeout(function () { 
                sweetAlert("Success","Password updated successfully. Please login with your new password.","success");
                window.location.href = "index.php";
            }, 1000);</script>';
        }
    } else {
        echo '<script type="text/javascript">';
        echo 'setTimeout(function () { sweetAlert("Oops...","The two passwords do not match!","error"); }, 500);</script>';
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="KEERTHANA KUTEERA LOGO-BLACK-01.png" type="image/png">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"> -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/sweetalert2@7.28.11/dist/sweetalert2.min.js"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7"></script> -->
</head>

<body>
    <script>
        // Đảm bảo jQuery đã được load
        if (typeof jQuery === 'undefined') {
            document.write('<script src="https://code.jquery.com/jquery-3.6.0.min.js"><\/script>');
        }
    </script>
</body>
        <!-- Modal Update Song -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="admin_page.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel">Update Song</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="update_song_id" id="update_song_id">
                        <input type="hidden" name="update_category" id="update_category">
                        <div class="form-group">
                            <label for="update_song_name">Song Name</label>
                            <input type="text" class="form-control" name="update_song_name" id="update_song_name" required>
                        </div>
                        <div class="form-group">
                            <label for="update_singer_name">Singer Name</label>
                            <input type="text" class="form-control" name="update_singer_name" id="update_singer_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_song" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal Update Song -->
</html>