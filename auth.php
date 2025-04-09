<?php
// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

// Kết nối CSDL
include('connection.php');

// Lấy user_id ra dùng
$user_id = $_SESSION['user_id'];
?>
