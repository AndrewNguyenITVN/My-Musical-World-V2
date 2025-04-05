<?php
include('connection.php'); // Kết nối đến cơ sở dữ liệu
session_start(); // Bắt đầu phiên làm việc
?>

<!DOCTYPE html>
<html>

<head>
    <title>Verify Account</title> <!-- Tiêu đề trang -->
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Thiết lập viewport cho responsive -->
    <link rel="icon" href="KEERTHANA KUTEERA LOGO-BLACK-01.png" type="image/png"> <!-- Favicon -->
    <!-- Liên kết Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- Liên kết thư viện SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <?php
    // Kiểm tra nếu có tham số email và mã kích hoạt trong URL
    if (isset($_GET['email_address']) && isset($_GET['activation_code'])) {
        // Làm sạch dữ liệu đầu vào
        $email_address = mysqli_real_escape_string($conn, $_GET['email_address']);
        $activation_code = mysqli_real_escape_string($conn, $_GET['activation_code']);

        // Tìm kiếm tài khoản chưa kích hoạt với email và mã kích hoạt khớp
        $search = mysqli_query($conn, "SELECT email_address, activation_code, confirm_status, username FROM user WHERE email_address = '$email_address' AND activation_code = '$activation_code' AND confirm_status = '0'");

        // Nếu tìm thấy tài khoản phù hợp
        if (mysqli_num_rows($search) > 0) {
            $row = mysqli_fetch_assoc($search); // Lấy thông tin người dùng
            $username = $row['username'];

            // Cập nhật trạng thái kích hoạt tài khoản
            mysqli_query($conn, "UPDATE user SET confirm_status = '1' WHERE email_address = '$email_address'");

            // Hiển thị thông báo thành công bằng SweetAlert2
            echo "<script>
            setTimeout(function () {
                Swal.fire({
                    title: 'Success',
                    icon: 'success',
                    text: 'Thank you $username, your account has been activated successfully!'
                }).then(() => {
                    window.location.href = 'index.php'; // Chuyển hướng về trang chủ sau khi kích hoạt
                });
            }, 500);
        </script>";
        } else {
            // Hiển thị thông báo lỗi nếu URL không hợp lệ hoặc tài khoản đã kích hoạt
            echo "<script>
            setTimeout(function () {
                Swal.fire('Oops...', 'The URL is either invalid or the account has already been activated.', 'error');
            }, 500);
        </script>";
        }
    } else {
        // Hiển thị thông báo lỗi nếu truy cập trực tiếp không qua link email
        echo "<script>
        setTimeout(function () {
            Swal.fire('Oops...', 'Invalid approach. Please use the link that was sent to your email.', 'error');
        }, 500);
    </script>";
    }
    ?>

</body>

</html>