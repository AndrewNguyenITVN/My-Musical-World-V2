<?php
session_start(); // Bắt đầu session để kiểm tra đăng nhập người dùng

include('connection.php'); // Kết nối CSDL

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['email_address']) || !isset($_SESSION['user_id'])) {
    echo "<div class='alert alert-danger'>You have not login yet!</div>";
    exit;
}


$limit = 6; // Số bài hát hiển thị mỗi trang
$page = isset($_POST['page_no']) ? (int)$_POST['page_no'] : 1; 
$offset = ($page - 1) * $limit;

// Lấy ID thể loại nhạc (mặc định là 2 nếu không gửi lên)
$cat_id = isset($_POST['cat_id']) ? (int)$_POST['cat_id'] : 2;
$user_id = $_SESSION['user_id']; // Lấy ID người dùng từ session

$songs = ""; // Biến chứa HTML danh sách bài hát

// Lấy danh sách bài hát thuộc thể loại đã chọn, phân trang
$sql = "SELECT * FROM songs WHERE cat_id = ? ORDER BY song_id ASC LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iii", $cat_id, $limit, $offset);
mysqli_stmt_execute($stmt);
$res_songs = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

// Lặp qua từng bài hát để hiển thị và kiểm tra trạng thái yêu thích
while ($row = mysqli_fetch_assoc($res_songs)) { //lấy 1 dòng dữ liệu từ kết quả truy vấn $res_songs cho đến khi không còn dòng nào trả về false.
    $song_id     = $row['song_id'];
    $song_name   = $row['song_name'];
    $singer_name = $row['singer_name'];
    $song_image  = $row['song_image'];
    $audio_file  = $row['audio_file'];

    // Kiểm tra xem bài hát này đã được người dùng thêm vào "yêu thích" chưa
    $check_sql = "SELECT 1 FROM favorite_songs WHERE user_id = ? AND song_id = ?";
    $stmt2 = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt2, "ii", $user_id, $song_id);
    mysqli_stmt_execute($stmt2);
    $check_res = mysqli_stmt_get_result($stmt2);
    mysqli_stmt_close($stmt2);

    $is_favorite = (mysqli_num_rows($check_res) > 0); // true nếu có trong favorite
    $heart_class = $is_favorite ? 'text-danger' : ''; // Đặt màu đỏ cho icon nếu đã yêu thích

    // Tạo đường dẫn ảnh và audio
    $image_path = "songs/img/$song_image";
    $audio_path = "songs/$audio_file";

    // Gắn HTML bài hát vào biến $songs
    $songs .= "
        <div class='col-md-4 mb-4'>
            <div class='card-song'>
                <img class='card-img-top' src='$image_path' alt=''>
                <div class='card-body'>
                    <h5 class='card-title' style='color: white; padding: 5px 0; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; line-height: 20px; height: 24px;'>$song_name</h5>
                    <p class='card-text'>By $singer_name </p>
                    <audio controls style='width: 100%;' preload='none'> 
                        <source src='$audio_path' type='audio/mp3'>
                    </audio><br>
                    <button class='btn-card add-to-fav' data-songid='$song_id' data-catid='$cat_id'>
                        <i class='fa fa-heart $heart_class'></i>
                    </button><br>  
                </div>
            </div>
        </div>";
}

// Tính tổng số bài hát để tạo phân trang
$count_sql = "SELECT COUNT(*) AS total FROM songs WHERE cat_id = ?";
$stmt3 = mysqli_prepare($conn, $count_sql);
mysqli_stmt_bind_param($stmt3, "i", $cat_id);
mysqli_stmt_execute($stmt3);
$result_count = mysqli_stmt_get_result($stmt3);
$total_records = mysqli_fetch_assoc($result_count)['total'] ?? 0;
mysqli_stmt_close($stmt3);

$total_pages = ceil($total_records / $limit); // Tổng số trang

// Tạo HTML phân trang
$pagination = "<nav aria-label='Page navigation'><ul class='pagination justify-content-center'>";

// Nút "Previous"
if ($page > 1) {
    $prev_page = $page - 1;
    $pagination .= "<li class='page-item'><a class='page-link' href='#' data-page='{$prev_page}'>Previous</a></li>";
}

// Các số trang
for ($i = 1; $i <= $total_pages; $i++) {
    if ($i == $page) {
        $pagination .= "<li class='page-item active'><span class='page-link'>{$i}</span></li>";
    } else {
        $pagination .= "<li class='page-item'><a class='page-link' href='#' data-page='{$i}'>{$i}</a></li>";
    }
}

// Nút "Next"
if ($page < $total_pages) {
    $next_page = $page + 1;
    $pagination .= "<li class='page-item'><a class='page-link' href='#' data-page='{$next_page}'>Next</a></li>";
}

$pagination .= "</ul></nav>";

// Trả dữ liệu dạng JSON gồm danh sách bài hát và phần phân trang
echo "

    <section class='details-card'>
        <div class='container'>
            <div class='row justify-content-center' id='song-list'>
                $songs
            </div>
        </div>
    </section>
    <div class='pagination text-center justify-content-center' id='pagination'>
        $pagination
    </div>

";