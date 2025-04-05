<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7"></script>
<?php
// Kiểm tra và khởi tạo session
if (extension_loaded('session')) {
    if (!isset($_SESSION)) {
        session_start();
    }
} else {
    die('PHP Session extension is not loaded');
}

// Xử lý đăng ký tài khoản
if (isset($_POST['register'])) {

    include('connection.php'); // Kết nối database

    // Lấy và làm sạch dữ liệu từ form
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

    // Kiểm tra định dạng email hợp lệ
    if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email_address)) {
        // Kiểm tra định dạng số điện thoại hợp lệ
        if (preg_match("/^(\+?\d{1,4})?[-.\s]?(\()?(\d{1,3})(?(2)\))[-.\s]?\d{1,4}[-.\s]?\d{1,9}$/", $mobile_number)) {

            // Kiểm tra email đã tồn tại chưa
            $sql_email = "SELECT email_address FROM user WHERE email_address='$email_address'";
            $result_email = mysqli_query($conn, $sql_email);

            // Kiểm tra số điện thoại đã tồn tại chưa
            $sql_mobile = "SELECT mobile_number FROM user WHERE mobile_number='$mobile_number'";
            $result_mobile = mysqli_query($conn, $sql_mobile);

            if (mysqli_num_rows($result_email) > 0) {
                // Hiển thị thông báo nếu email đã tồn tại
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
                // Hiển thị thông báo nếu số điện thoại đã tồn tại
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
                // Tạo mã kích hoạt và hash mật khẩu
                $activation_code = hash('sha256', mt_rand(0, 1000));
                $hash_password = md5($password);

                // Thêm tài khoản mới vào database
                $sql = "INSERT INTO user (`username`, `password`, `mobile_number`, `email_address`, `activation_code`) 
                            VALUES ('$username', '$hash_password', '$mobile_number', '$email_address', '$activation_code')";

                $result = mysqli_query($conn, $sql);

                if (!$result) {
                    die("Error while updating!!!...") . mysqli_error($conn);
                } else {
                    // Lưu thông tin vào session
                    $_SESSION['username'] = $username;
                    $_SESSION['mobile_number'] = $mobile_number;
                    $_SESSION['email_address'] = $email_address;
                    $_SESSION['activation_code'] = $activation_code;
                    $_SESSION['password'] = $password;

                    // Gửi email kích hoạt
                    include('activate_email.php');
                }
            }
        } else {
            // Hiển thị lỗi nếu số điện thoại không hợp lệ
            echo '<script type="text/javascript">';
            echo 'setTimeout(function () { sweetAlert("Oops...","Mobile number ' . $mobile_number . ' is invalid!","error");';
            echo '}, 500);</script>';
        }
    } else {
        // Hiển thị lỗi nếu email không hợp lệ
        echo '<script type="text/javascript">';
        echo 'setTimeout(function () { sweetAlert("Oops...","Email address ' . $email_address . ' is invalid!","error");';
        echo '}, 500);</script>';
    }
}

// Xử lý đăng nhập
if (isset($_POST['login'])) {

    session_start(); // Bắt đầu session

    include('connection.php'); // Kết nối database

    // Lấy và làm sạch dữ liệu từ form
    $email_address = mysqli_real_escape_string($conn, $_POST['email_address']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $hash_password = md5($password); // Mã hóa mật khẩu

    // Kiểm tra thông tin đăng nhập
    $sql = "SELECT * FROM user WHERE email_address = '$email_address' AND password = '$hash_password' ";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        // Hiển thị lỗi nếu có vấn đề khi đăng nhập
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
                // Hiển thị thông báo nếu tài khoản chưa kích hoạt
                echo '<script type="text/javascript">';
                echo 'setTimeout(function () { sweetAlert("Warning...","Please activate your account first!..","warning");';
                echo '}, 500);</script>';
            } else {
                // Lưu thông tin người dùng vào session
                $_SESSION['username'] = $username;
                $_SESSION['email_address'] = $email_address;
                $_SESSION['user_id'] = $user_id;

                // Kiểm tra nếu là admin thì chuyển hướng đến trang admin
                if ($email_address == 'admin@gmail.com' && $row['password'] == '21232f297a57a5a743894a0e4a801fc3') {
                    header('location:admin_page.php');
                } else {
                    // Người dùng thường chuyển đến trang profile
                    header('location:profile.php');
                }
            }
        } else {
            // Hiển thị lỗi nếu thông tin đăng nhập không đúng
            echo '<script type="text/javascript">';
            echo 'setTimeout(function () { sweetAlert("Oops...","Wrong username or Password!...","error");';
            echo '}, 500);</script>';
        }
    }
}

// Xử lý quên mật khẩu
if (isset($_POST['forgot'])) {
    include('connection.php'); // Kết nối database

    $email_address = mysqli_real_escape_string($conn, $_POST['email_address']);
    // Kiểm tra email có tồn tại không
    $sql = "SELECT email_address FROM user WHERE email_address = '$email_address'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 0) {
        // Hiển thị thông báo nếu email không tồn tại
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
        // Sử dụng PHPMailer để gửi email
        require 'phpmailer/PHPMailerAutoload.php';

        // Tạo token và thời gian hết hạn (1 giờ)
        $reset_token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Cập nhật token vào database
        $update_sql = "UPDATE user SET reset_token = '$reset_token', reset_token_expiry = '$expiry' 
                      WHERE email_address = '$email_address'";
        mysqli_query($conn, $update_sql);

        // Lấy thông tin username
        $sql = "SELECT username FROM user WHERE email_address = '$email_address'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        $username = $row['username'];

        // Tạo link reset mật khẩu
        $reset_link = "http://localhost/webamnhac/My-Musical-World/reset_password.php?token=" . $reset_token . "&email=" . urlencode($email_address);

        // Cấu hình PHPMailer
        $mail = new PHPMailer;
        $mail->isSMTP(); // Sử dụng SMTP
        $mail->Host = 'smtp.gmail.com'; // Máy chủ SMTP
        $mail->SMTPAuth = true; // Xác thực SMTP
        $mail->Username = 'mymusicworld.2025@gmail.com'; // Email gửi
        $mail->Password = 'vtpb htgv btuk xqpa'; // Mật khẩu
        $mail->SMTPSecure = 'tls'; // Bảo mật TLS
        $mail->Port = 587; // Cổng kết nối
        $to = $email_address;
        $mail->setFrom('mymusicworld.2025@gmail.com', 'My Musical World');
        $mail->addAddress($to); // Email nhận
        $mail->SMTPDebug = 0; // Tắt debug

        $mail->isHTML(true); // Định dạng email là HTML
        $mail->Subject = 'Reset Your Account'; // Tiêu đề email
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
            // Hiển thị lỗi nếu gửi email không thành công
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
            // Hiển thị thông báo thành công nếu gửi email thành công
        ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
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
                    }).then(function() {
                        window.location.href = 'index.php';
                    });
                });
            </script>
<?php
        }
    }
}

// Xử lý token reset mật khẩu từ URL
if (isset($_GET['token']) && isset($_GET['email'])) {
    $token = $_GET['token'];
    $email = $_GET['email'];

    // Kiểm tra token hợp lệ và chưa hết hạn
    $sql = "SELECT * FROM user WHERE email_address = '$email' 
            AND reset_token = '$token' 
            AND reset_token_expiry > NOW()";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 0) {
        header('location:index.php');
        exit();
    }

    // Lưu email và token vào session nếu hợp lệ
    session_start();
    $_SESSION['email_address'] = $email;
    $_SESSION['reset_token'] = $token;
}

// Xử lý form reset mật khẩu
if (isset($_POST['reset'])) {
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $email = $_SESSION['email_address'];
    $token = $_SESSION['reset_token'];

    // Kiểm tra mật khẩu nhập lại khớp
    if ($password == $confirm_password) {
        $hash_password = md5($password); // Mã hóa mật khẩu mới

        // Cập nhật mật khẩu mới và xóa token
        $sql = "UPDATE user SET 
                password = '$hash_password',
                reset_token = NULL,
                reset_token_expiry = NULL 
                WHERE email_address = '$email' 
                AND reset_token = '$token'";

        $result = mysqli_query($conn, $sql);

        if ($result) {
            // Hiển thị thông báo thành công
            echo '<script type="text/javascript">';
            echo 'setTimeout(function () { 
                sweetAlert("Success","Password updated successfully. Please login with your new password.","success");
                window.location.href = "index.php";
            }, 1000);</script>';
        }
    } else {
        // Hiển thị lỗi nếu mật khẩu không khớp
        echo '<script type="text/javascript">';
        echo 'setTimeout(function () { sweetAlert("Oops...","The two passwords do not match!","error"); }, 500);</script>';
    }
}
?>