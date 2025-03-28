<?php
session_start();
include('connection.php');

// Đảm bảo response luôn là JSON
header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['email_address']) || !isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode([
        'status' => 'error',
        'message' => 'Please log in to add songs to favorites'
    ]));
}

// Đọc và kiểm tra dữ liệu đầu vào
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['song_id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

$song_id = (int)$data['song_id'];
$user_id = (int)$_SESSION['user_id'];

try {
    // Kiểm tra đã có trong favorite chưa
    $check_sql = "SELECT id FROM favorite_songs WHERE song_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt, "ii", $song_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) > 0) {
        echo json_encode(['status' => 'warning', 'message' => 'This song is already in your favorites']);
        exit;
    }
    mysqli_stmt_close($stmt);

    // Lấy thông tin bài hát
    $song_sql = "SELECT song_name, singer_name, song_image, audio_file FROM songs WHERE song_id = ?";
    $stmt = mysqli_prepare($conn, $song_sql);
    mysqli_stmt_bind_param($stmt, "i", $song_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $song = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$song) {
        throw new Exception("Song not found");
    }


    $insert_sql = "INSERT INTO favorite_songs (song_id, user_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($stmt, "ii", $song_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo json_encode([
        'status' => 'success',
        'message' => 'Song "' . $song['song_name'] . '" added to favorites'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Error adding song: ' . $e->getMessage()
    ]);
}