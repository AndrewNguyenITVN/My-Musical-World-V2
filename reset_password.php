<?php
// Kết nối đến cơ sở dữ liệu
include('connection.php');

// Khởi động session nếu chưa có
if (!isset($_SESSION)) {
    session_start();
}

// Kiểm tra token và email được truyền từ URL (liên kết khôi phục mật khẩu)
if (isset($_GET['token']) && isset($_GET['email'])) {
    $token = $_GET['token'];
    $email = $_GET['email'];

    // Truy vấn để xác nhận token và email có hợp lệ không
    $sql = "SELECT * FROM user WHERE email_address = '$email' AND reset_token = '$token'";
    $result = mysqli_query($conn, $sql);

    // Nếu có kết quả phù hợp -> lưu thông tin vào session để xử lý bước sau
    if ($result && mysqli_num_rows($result) > 0) {
        $_SESSION['email_address'] = $email;
        $_SESSION['reset_token'] = $token;
    } else {
        // Nếu token/email không hợp lệ -> chuyển hướng về trang đăng nhập
        header('location:index.php');
        exit();
    }
}

// Xử lý khi người dùng nhấn nút "Reset Password"
if (isset($_POST['reset'])) {
    // Lấy dữ liệu từ form và tránh SQL Injection
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $email = $_SESSION['email_address'];
    $token = $_SESSION['reset_token'];

    // Kiểm tra mật khẩu và xác nhận có trùng nhau không
    if ($password == $confirm_password) {
        // Mã hóa mật khẩu bằng md5 (Lưu ý: nên dùng bcrypt hoặc Argon2 thay vì md5 vì bảo mật tốt hơn)
        $hash_password = md5($password);

        // Cập nhật mật khẩu mới và xóa token khôi phục
        $sql = "UPDATE user SET 
                password = '$hash_password',
                reset_token = NULL,
                reset_token_expiry = NULL 
                WHERE email_address = '$email' 
                AND reset_token = '$token'";

        // Nếu cập nhật thành công
        if (mysqli_query($conn, $sql)) {
            // Xóa session sau khi cập nhật xong
            unset($_SESSION['email_address']);
            unset($_SESSION['reset_token']);

            // Hiển thị thông báo thành công bằng SweetAlert2
?>
            <script>
                window.onload = function() {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Password has been reset successfully. Please login with your new password.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4CAF50'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'index.php'; // Chuyển về trang đăng nhập
                        }
                    });
                }
            </script>
        <?php
        }
    } else {
        // Hiển thị lỗi nếu mật khẩu không trùng khớp
        ?>
        <script>
            window.onload = function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'Passwords do not match. Please check and try again.',
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
            }
        </script>
<?php
    }
}
?>

<!DOCTYPE HTML>
<html>

<head>
    <title>Reset Password - My Musical World</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">

    <!-- Favicon -->
    <link rel="icon" href="images/i1.png" />

    <!-- Bootstrap và các thư viện CSS -->
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.14/css/mdb.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- SweetAlert2 để hiển thị popup -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- CSS tùy chỉnh -->
    <style>
        body {
            background: #000000;
        }

        .reset-form {
            background: #121212;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 100px;
            color: #fff;
            border: 1px solid #333;
        }

        .btn-reset {
            background: #4CAF50;
            color: white;
            width: 100%;
            padding: 10px;
        }
    </style>
</head>

<body>
    <!-- Container chính -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <!-- Form reset password -->
                <div class="reset-form">
                    <h3 class="text-center mb-4">Reset Your Password</h3>
                    <form method="post" id="resetForm">
                        <div class="form-group mb-3">
                            <label>New Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Confirm Password</label>
                            <input type="password" class="form-control" name="confirm_password" required>
                        </div>
                        <button type="submit" name="reset" class="btn btn-reset">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script cho jQuery và Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>