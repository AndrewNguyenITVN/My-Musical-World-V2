<?php
session_start(); // Bắt đầu phiên làm việc (session) để truy cập thông tin đăng nhập của người dùng
include('connection.php'); // Kết nối cơ sở dữ liệu

// Đảm bảo rằng phản hồi từ server luôn có định dạng JSON
header('Content-Type: application/json');

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['email_address']) || !isset($_SESSION['user_id'])) {
    http_response_code(401); // Trả về mã lỗi 401 - Unauthorized
    exit(json_encode([
        'status' => 'error',
        'message' => 'Please log in to add songs to favorites'
    ]));
}

// Đọc dữ liệu JSON từ body của request
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Kiểm tra dữ liệu đầu vào có hợp lệ không (phải có 'song_id')
if (!$data || !isset($data['song_id'])) {
    http_response_code(400); // Trả về lỗi 400 - Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

// Ép kiểu song_id và user_id thành số nguyên để đảm bảo an toàn
$song_id = (int)$data['song_id'];
$user_id = (int)$_SESSION['user_id'];

try {
    // Kiểm tra xem bài hát này đã có trong danh sách yêu thích của người dùng chưa
    $check_sql = "SELECT id FROM favorite_songs WHERE song_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $check_sql); // Chuẩn bị câu lệnh SQL
    mysqli_stmt_bind_param($stmt, "ii", $song_id, $user_id); // Gán giá trị cho câu lệnh
    mysqli_stmt_execute($stmt); // Thực thi câu lệnh
    $result = mysqli_stmt_get_result($stmt); // Lấy kết quả truy vấn

    // Nếu đã tồn tại, không thêm lại nữa
    if (mysqli_num_rows($result) > 0) {
        echo json_encode(['status' => 'warning', 'message' => 'This song is already in your favorites']);
        exit;
    }
    mysqli_stmt_close($stmt); // Đóng statement sau khi dùng xong

    // Lấy thông tin chi tiết của bài hát từ bảng 'songs'
    $song_sql = "SELECT song_name, singer_name, song_image, audio_file FROM songs WHERE song_id = ?";
    $stmt = mysqli_prepare($conn, $song_sql);
    mysqli_stmt_bind_param($stmt, "i", $song_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $song = mysqli_fetch_assoc($result); // Lấy thông tin bài hát dưới dạng mảng kết hợp
    mysqli_stmt_close($stmt);

    // Nếu không tìm thấy bài hát, ném ra ngoại lệ
    if (!$song) {
        throw new Exception("Song not found");
    }

    // Thêm bài hát vào danh sách yêu thích
    $insert_sql = "INSERT INTO favorite_songs (song_id, user_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($stmt, "ii", $song_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Trả về phản hồi thành công
    echo json_encode([
        'status' => 'success',
        'message' => 'Song "' . $song['song_name'] . '" added to favorites'
    ]);
} catch (Exception $e) {
    // Nếu xảy ra lỗi trong quá trình xử lý, trả về mã lỗi 500
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Error adding song: ' . $e->getMessage()
    ]);
}
