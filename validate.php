<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    // Kiểm tra độ dài mật khẩu
    if (strlen($password) < 6) {
        echo '<script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Oops...",
                    text: "Password must be at least 6 characters!",
                    icon: "error",
                    confirmButtonText: "OK"
                }).then(() => {
                    window.history.back(); // Quay lại trang trước
                });
            });
        </script>';
        return;
    }

    // Kiểm tra mật khẩu trùng khớp
    if ($password !== $confirm_password) {
        echo '<script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Oops...",
                    text: "Passwords do not match!",
                    icon: "error",
                    confirmButtonText: "OK"
                }).then(() => {
                    window.history.back(); // Quay lại trang trước
                });
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
                                window.history.back();
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
                                window.history.back();
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
            echo 'setTimeout(function () { 
                        Swal.fire({
                            title: "Oops...",
                            text: "Mobile number ' . $mobile_number . ' is invalid!",
                            icon: "error",
                            confirmButtonText: "OK"
                        }).then(() => {
                            window.history.back();
                        });
                        }, 500);';
            echo '</script>';
        }
    } else {
        // Hiển thị lỗi nếu email không hợp lệ
        echo '<script type="text/javascript">';
        echo 'setTimeout(function () { 
                        Swal.fire({
                            title: "Oops...",
                            text: "Email address ' . $email_address . ' is invalid!",
                            icon: "error",
                            confirmButtonText: "OK"
                        }).then(() => {
                            window.history.back();
                        });
                        }, 500);';
        echo '</script>';
    }
}

// Xử lý đăng nhập
if (isset($_POST['login'])) {

    //session_start(); // Bắt đầu session

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
        echo 'setTimeout(function () { Swal.fire("Warning...","Error while loggin in!..","warning");';
        echo '}, 500);</script>';
    } else {   
        $count = mysqli_num_rows($result);
        if ($count == 1) {
            $row = mysqli_fetch_array($result);
            $username = $row['username'];
            $user_id = $row['user_id'];
            if (isset($row['confirm_status']) && $row['confirm_status'] == 0) {
                echo '<script>setTimeout(function () {
                    Swal.fire("Warning...", "Please activate your account first!", "warning");
                }, 500);</script>';
            } else {
                $_SESSION['username'] = $username;
                $_SESSION['email_address'] = $email_address;
                $_SESSION['user_id'] = $user_id;

                // Dựa vào user_id để phân quyền
                if ($user_id < 100) {
                    header('location:admin_page.php');
                } else {
                    header('location:profile.php');
                }
                exit();
            }
        } else {
            // Hiển thị lỗi nếu thông tin đăng nhập không đúng
            echo '<script type="text/javascript">';
            echo 'setTimeout(function () { 
                        Swal.fire({
                            title: "Oops...",
                            text: "Wrong username or Password!...",
                            icon: "error",
                            confirmButtonText: "OK"
                        }).then(() => {
                            window.history.back();
                        });
                        }, 500);';
            echo '</script>';
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
                Swal.fire({
                    title: 'Email Not Found',
                    text: 'Email address <?php echo $email_address; ?> does not exist!',
                    icon: 'error',
                    showCancelButton: false,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK',
                    showConfirmButton: true
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
        <b>My Musical World</b></pre>";

        if (!$mail->send()) {
            // Hiển thị lỗi nếu gửi email không thành công
        ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Email Sending Failed',
                        text: 'Error while sending email. Please check your internet connection!',
                        icon: 'error',
                        showCancelButton: false,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK',
                        showConfirmButton: true
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
                        showConfirmButton: true
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

?>