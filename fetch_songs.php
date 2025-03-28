<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('connection.php');
header('Content-Type: application/json; charset=utf-8');

// Kiểm tra đăng nhập
if (!isset($_SESSION['email_address']) || !isset($_SESSION['user_id'])) {
     exit(json_encode(['error' => 'Unauthorized']));
 }

$limit = 6; // Số bài hát mỗi trang
$page = isset($_POST['page_no']) ? (int)$_POST['page_no'] : 1;
$offset = ($page - 1) * $limit;

// Nhận cat_id
$cat_id = isset($_POST['cat_id']) ? (int)$_POST['cat_id'] : 2;
$user_id = $_SESSION['user_id'];

$songs = "";

// Lấy danh sách bài hát
$sql = "SELECT * FROM songs WHERE cat_id = ? ORDER BY song_id ASC LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iii", $cat_id, $limit, $offset);
mysqli_stmt_execute($stmt);
$res_songs = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

while ($row = mysqli_fetch_assoc($res_songs)) {
    $song_id     = $row['song_id'];
    $song_name   = $row['song_name'];
    $singer_name = $row['singer_name'];
    $song_image  = $row['song_image'];
    $audio_file  = $row['audio_file'];

    // Kiểm tra đã có trong favorite chưa
    $check_sql = "SELECT 1 FROM favorite_songs WHERE user_id = ? AND song_id = ?";
    $stmt2 = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt2, "ii", $user_id, $song_id);
    mysqli_stmt_execute($stmt2);
    $check_res = mysqli_stmt_get_result($stmt2);
    mysqli_stmt_close($stmt2);

    $is_favorite = (mysqli_num_rows($check_res) > 0);
    $heart_class = $is_favorite ? 'text-danger' : '';

    // Đường dẫn ảnh và audio (chỉ còn 1 thư mục chung)
    $image_path = "songs/img/$song_image";
    $audio_path = "songs/$audio_file";
        $songs .= "
            <div class='col-md-3'>
                <div class='card-deck'>
                    <div class='card-img'>
                        <img src='$image_path' style='width:300px;height:250px' alt=''>
                    </div>
                    <div class='card-desc'>
                        <h3 style='padding: 10px 0; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; line-height: 14px; height: 24px;'>$song_name | $singer_name</h3>
                        <audio class='embed-responsive-item' controls='' preload='none'> 
                            <source src='$audio_path' type='audio/mp3'>
                        </audio><br>
                        <button class='btn-card add-to-fav' data-songid='$song_id' data-catid='$cat_id'>
                            <i class='fa fa-heart $heart_class'></i>
                        </button><br>  
                    </div>
                </div>
            </div>";
}

// Tạo phân trang
// 2) Tạo pagination
$count_sql = "SELECT COUNT(*) AS total FROM songs WHERE cat_id = ?";
$stmt3 = mysqli_prepare($conn, $count_sql);
mysqli_stmt_bind_param($stmt3, "i", $cat_id);
mysqli_stmt_execute($stmt3);
$result_count = mysqli_stmt_get_result($stmt3);
$total_records = mysqli_fetch_assoc($result_count)['total'] ?? 0;
mysqli_stmt_close($stmt3);

$total_pages = ceil($total_records / $limit);

$pagination = "<nav aria-label='Page navigation'><ul class='pagination justify-content-center'>";

if ($page > 1) {
    $prev_page = $page - 1;
    $pagination .= "<li class='page-item'><a class='page-link' href='#' data-page='{$prev_page}'>Previous</a></li>";
} else {
    $pagination .= "<li class='page-item disabled'><span class='page-link'>Previous</span></li>";
}

for ($i = 1; $i <= $total_pages; $i++) {
    if ($i == $page) {
        $pagination .= "<li class='page-item active'><span class='page-link'>{$i}</span></li>";
    } else {
        $pagination .= "<li class='page-item'><a class='page-link' href='#' data-page='{$i}'>{$i}</a></li>";
    }
}

if ($page < $total_pages) {
    $next_page = $page + 1;
    $pagination .= "<li class='page-item'><a class='page-link' href='#' data-page='{$next_page}'>Next</a></li>";
} else {
    $pagination .= "<li class='page-item disabled'><span class='page-link'>Next</span></li>";
}

$pagination .= "</ul></nav>";

echo json_encode([
    "songs" => $songs, 
    "pagination" => $pagination
]);
