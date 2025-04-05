<?php
session_start();
if (!isset($_SESSION['email_address']))
    header('location:index.php');

include('connection.php');

$user_id = $_SESSION['user_id'];

/* ===== XỬ LÝ UPDATE SONG ===== */
if (isset($_POST['update_song'])) {
    $update_song_id   = $_POST['update_song_id'];
    $update_category  = $_POST['update_category'];
    // Dùng mysqli_real_escape_string để tránh SQL injection (bảo mật)
    $update_song_name = mysqli_real_escape_string($conn, $_POST['update_song_name']);
    $update_singer_name = mysqli_real_escape_string($conn, $_POST['update_singer_name']);

    // Kiểm tra category để update đúng bảng
    if ($update_category == 'vietnam') {
        $sql = "UPDATE songs
                SET song_name='$update_song_name',
                    singer_name='$update_singer_name'
                WHERE song_id='$update_song_id'
                  AND cat_id=2";
    } elseif ($update_category == 'english') {
        $sql = "UPDATE songs
                SET song_name='$update_song_name',
                    singer_name='$update_singer_name'
                WHERE song_id='$update_song_id'
                  AND cat_id=3";
    } else {
        // Thêm loại khác ...
    }

    $result = mysqli_query($conn, $sql);
    // Phản hồi trạng thái cập nhật
    if ($result) {
        $_SESSION['message'] = '<script type="text/javascript">
                setTimeout(function () { 
                  sweetAlert("Uploaded"," <b>Uploaded song successfully ' . $update_song_name . '</b>","success");
                }, 500);
              </script>';
    } else {
        $_SESSION['message'] = '<script type="text/javascript">
                setTimeout(function () { 
                  sweetAlert("Error","Error while updating. Please check your internet connection!","error");
                }, 500);
              </script>';
    }
    // Sau khi xử lý xong, chuyển hướng người dùng về admin_page.php và dừng chương trình
    header("Location: admin_page.php");
    exit();
}

/* ===== XỬ LÝ DELETE SONG ===== */
if (isset($_POST['delete_song_id'])) {
    $delete_song_id  = $_POST['delete_song_id'];
    $sql_del = "SELECT * FROM songs WHERE song_id='$delete_song_id'";
    $result = mysqli_query($conn, $sql_del);
    $row = mysqli_fetch_array($result);
    $song_name = $row['song_name'];

    $sql_del = "DELETE FROM songs WHERE song_id='$delete_song_id'";
    $result = mysqli_query($conn, $sql_del);
    if ($result) {
        $_SESSION['message'] = '<script type="text/javascript">
                setTimeout(function () { 
                  sweetAlert("Deleted"," <b>Deleted song successfully ' . $song_name . '</b>","success");
                }, 500);
              </script>';
        header("Location: admin_page.php");
        exit();
    } else {
        $_SESSION['message'] = '<script type="text/javascript">
                setTimeout(function () { 
                  sweetAlert("Error","Error while deleting. Please check your internet connection!","error");
                }, 500);
              </script>';
        header("Location: admin_page.php");
        exit();
    }
}

/* ===== XỬ LÝ UPLOAD SONGS ===== */
// Upload vietnam
if (isset($_POST['upload_vietnam'])) {
    // Kiểm tra file upload
    if (!isset($_FILES['audio_file']) || $_FILES['audio_file']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['message'] = '<script type="text/javascript">
                setTimeout(function () { 
                    sweetAlert("Error","Error while uploading. Please check your file!","error");
                }, 500);
            </script>';
        header("Location: admin_page.php");
        exit();
    }

    if (!isset($_FILES['song_image']) || $_FILES['song_image']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['message'] = '<script type="text/javascript">
                setTimeout(function () { 
                    sweetAlert("Error","Error while uploading. Please check your cover image!","error");
                }, 500);
            </script>';
        header("Location: admin_page.php");
        exit();
    }
    $audio_file  = $_FILES['audio_file']['name'];
    $song_name   = mysqli_real_escape_string($conn, $_POST['song_name']);
    $song_image  = $_FILES['song_image']['name'];
    $singer_name = mysqli_real_escape_string($conn, $_POST['singer_name']);

    $singer_id = 1;
    $cat_id    = 2;

    // Nếu có file nhạc: lấy đường dẫn tạm thời và di chuyển file vào thư mục songs/vietnam_albums
    if (isset($_FILES['audio_file'])) {
        $file_tmp = $_FILES['audio_file']['tmp_name'];
        move_uploaded_file($file_tmp, "songs/" . $audio_file);
    }
    // Nếu có file ảnh: lấy đường dẫn tạm thời và di chuyển file vào thư mục songs/vietnam_albums/img
    if (isset($_FILES['song_image'])) {
        $file_tmp = $_FILES['song_image']['tmp_name'];
        move_uploaded_file($file_tmp, "songs/img/" . $song_image);
    }
    $sql = "INSERT INTO songs(`singer_id`, `cat_id`, `song_name`, `singer_name`, `song_image`, `audio_file`)
            VALUES($singer_id, $cat_id, '$song_name', '$singer_name', '$song_image', '$audio_file')";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $_SESSION['message'] = '<script type="text/javascript">
                setTimeout(function () { 
                  sweetAlert("Uploaded"," <b>Uploaded song successfully ' . $song_name . '</b>","success");
                }, 500);
              </script>';
        header("Location: admin_page.php");
        exit();
    } else {
        $_SESSION['message'] = '<script type="text/javascript">
                setTimeout(function () { 
                  sweetAlert("Error","Error while uploading. Please check your internet connection!","error");
                }, 500);
              </script>';
        header("Location: admin_page.php");
        exit();
    }
}
//Upload english được viết kỹ hơn, có check input
if (isset($_POST['upload_english'])) {
    // Kiểm tra file upload
    if (!isset($_FILES['audio_file']) || $_FILES['audio_file']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['message'] = '<script type="text/javascript">
                setTimeout(function () { 
                    sweetAlert("Error","Error while uploading. Please check your file!","error");
                }, 500);
            </script>';
        header("Location: admin_page.php");
        exit();
    }

    if (!isset($_FILES['song_image']) || $_FILES['song_image']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['message'] = '<script type="text/javascript">
                setTimeout(function () { 
                    sweetAlert("Error","Error while uploading. Please check your cover image!","error");
                }, 500);
            </script>';
        header("Location: admin_page.php");
        exit();
    }
    $audio_file  = $_FILES['audio_file']['name'];
    $song_name   = mysqli_real_escape_string($conn, $_POST['song_name']);
    $song_image  = $_FILES['song_image']['name'];
    $singer_name = mysqli_real_escape_string($conn, $_POST['singer_name']);

    $singer_id = 1;
    $cat_id    = 3;

    // Nếu có file nhạc: lấy đường dẫn tạm thời và di chuyển file vào thư mục songs/vietnam_albums
    if (isset($_FILES['audio_file'])) {
        $file_tmp = $_FILES['audio_file']['tmp_name'];
        move_uploaded_file($file_tmp, "songs/" . $audio_file);
    }
    // Nếu có file ảnh: lấy đường dẫn tạm thời và di chuyển file vào thư mục songs/vietnam_albums/img
    if (isset($_FILES['song_image'])) {
        $file_tmp = $_FILES['song_image']['tmp_name'];
        move_uploaded_file($file_tmp, "songs/img/" . $song_image);
    }
    $sql = "INSERT INTO songs(`singer_id`, `cat_id`, `song_name`, `singer_name`, `song_image`, `audio_file`)
            VALUES($singer_id, $cat_id, '$song_name', '$singer_name', '$song_image', '$audio_file')";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $_SESSION['message'] = '<script type="text/javascript">
                setTimeout(function () { 
                  sweetAlert("Uploaded"," <b>Uploaded song successfully ' . $song_name . '</b>","success");
                }, 500);
              </script>';
        header("Location: admin_page.php");
        exit();
    } else {
        $_SESSION['message'] = '<script type="text/javascript">
                setTimeout(function () { 
                  sweetAlert("Error","Error while uploading. Please check your internet connection!","error");
                }, 500);
              </script>';
        header("Location: admin_page.php");
        exit();
    }
}
?>
<!DOCTYPE HTML>
<html>

<head>
    <title>My Musical World | Admin Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <link rel="icon" href="images/i1.png" />
    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
    <!-- Material Design Bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.14/css/mdb.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <!-- font-awesome icons -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@7.28.11/dist/sweetalert2.min.css">
    <style>
        th {
            background-color: #121212 !important;
            z-index: 2;
            top: 0;
            color: #fff;
        }

        td {
            background-color: #1f1f1f !important;
            color: #fff;
        }

        .table-wrapper {
            overflow-y: auto;
            max-height: 300px;
        }

        /* Khắc phục rung nhẹ do table-border */
        .table th,
        .table td {
            vertical-align: middle;
            white-space: nowrap;
        }
    </style>

</head>

<body>
    <?php
    if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
    ?>
    <!-- header -->
    <header>
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a class="navbar-brand" href="#">
                    My Musical World
                </a>
                <pre>                 </pre>
                <li class="nav-item">
                    <b style="font-size:20px;">
                        <p style="color:#fff !important; font-weight: bold"><?php echo "Logged in as " . $_SESSION['username']; ?></p>
                    </b>
                    <p style="color:#fff !important; font-weight: bold">| Admin Page |</p>
                </li>
                <li class="nav-item">
                    <a class="nav-link color-white" href="logout.php" style="font-size: 20px; "><b>Logout</b></a>
                </li>
            </nav>
        </div>
    </header>
    <!-- //header -->

    <br>

    <!-- List of all users -->
    <div class="alert alert-primary" role="alert">
        <center><b>List of all users</b></center>
    </div>
    <?php
    include('connection.php');
    $sql = "SELECT * FROM user ORDER BY user_id";
    $result = mysqli_query($conn, $sql);
    echo "
		<div class='card' style='margin: 0 10%;'>
			<h3 class='card-header text-center font-weight-bold text-uppercase py-4'>Users List</h3>
			<div class='card-body'>
				<div style='max-height:240px; overflow-y:auto; position:relative;'>
					<table class='table table-bordered text-center m-0' style='border-collapse: separate; border-spacing: 0;'>
						<thead>
							<tr>
								<th style='position: sticky; top: 0; background: #fff; z-index: 10;'>User id</th>
								<th style='position: sticky; top: 0; background: #fff; z-index: 10;'>User Name</th>
								<th style='position: sticky; top: 0; background: #fff; z-index: 10;'>Email Address</th>
								<th style='position: sticky; top: 0; background: #fff; z-index: 10;'>Mobile Number</th>
								<th style='position: sticky; top: 0; background: #fff; z-index: 10;'>Contributions</th>
								<th style='position: sticky; top: 0; background: #fff; z-index: 10;'>Confirm Status</th>
							</tr>
						</thead>
						<tbody>
		";
    while ($row = mysqli_fetch_array($result)) {
        echo "
							<tr>
								<td class='pt-3-half'>" . $row['user_id'] . "</td>
								<td class='pt-3-half'>" . $row['username'] . "</td>
								<td class='pt-3-half'>" . $row['email_address'] . "</td>
								<td class='pt-3-half'>" . $row['mobile_number'] . "</td>
								<td class='pt-3-half'>" . $row['contributions'] . "</td>
								<td class='pt-3-half'>" . $row['confirm_status'] . "</td>
							</tr>
			";
    }
    echo "
						</tbody>
					</table>
				</div>
			</div>
		</div><br>
		";
    ?>
    <!-- //List of all users -->
    <!-- Vietnam Songs Section -->
    <div class="alert alert-primary" role="alert">
        <center>
            <h4 class="text-center  text-blue p-2 rounded">Vietnam Songs</h4>
        </center>
    </div>
    <form method="post" action="admin_page.php" enctype="multipart/form-data">
        <div class="container mt-3">
            <div class="row align-items-center">
                <!-- Upload Audio File -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold color-white">Upload Audio File</label>
                        <div class="input-group">
                            <input type="file" class="form-control text-white" name="audio_file" required>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fa fa-music"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Song Name -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold color-white">Song Name</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="song_name" placeholder="Enter song name" required>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fa fa-headphones"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row align-items-center">
                <!-- Upload Cover Image -->
                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <label class="font-weight-bold color-white">Upload Cover Image</label>
                        <div class="input-group">
                            <input type="file" class="form-control text-white" name="song_image" required>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fa fa-image"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Singer Name -->
                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <label class="font-weight-bold color-white">Singer Name</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="singer_name" placeholder="Enter singer name" required>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fa fa-user"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Button -->
                <div class="col-md-4 mt-3 d-flex justify-content-center align-items-center">
                    <button type="submit" name="upload_vietnam" class="btn btn-success btn-lg px-5">
                        Upload <i class="fa fa-upload ml-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <?php
    include('connection.php');
    $sql = "SELECT * FROM songs WHERE cat_id=2 ORDER BY song_id";
    $result = mysqli_query($conn, $sql);
    echo "
        <div class='card'>
            <h3 class='card-header text-center font-weight-bold text-uppercase py-4'>Vietnam Songs Uploaded</h3>
            <div class='card-body p-0'>
                <div style='max-height: 450px; overflow-y: auto; border: 1px solid #ddd;'>
                    <table class='table table-bordered text-center m-0' style='border-collapse: separate; border-spacing: 0;'>
                        <thead>
                            <tr>
                                <th style='position: sticky; top: 0; background-color: white; z-index: 10; height: 50px;'>Song ID</th>
                                <th style='position: sticky; top: 0; background-color: white; z-index: 10; height: 50px;'>Song Name</th>
                                <th style='position: sticky; top: 0; background-color: white; z-index: 10; height: 50px;'>Singer Name</th>
                                <th style='position: sticky; top: 0; background-color: white; z-index: 10; height: 50px;'>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
        ";

    $count = 0;
    while ($row = mysqli_fetch_array($result)) {
        $count++;
        $song_id     = $row['song_id'];
        $song_name   = $row['song_name'];
        $singer_name = $row['singer_name'];

        echo "
                <tr>
                    <td class='pt-3-half'>" . $count . "</td>
                    <td class='pt-3-half'>" . $song_name . "</td>
                    <td class='pt-3-half'>" . $singer_name . "</td>
                    <td>
                        <button type='button' class='btn btn-info btn-sm' 
                            onclick=\"openUpdateModal('" . $song_id . "', '" . $song_name . "', '" . $singer_name . "', 'vietnam')\">
                            Update
                        </button>
                        <form method='post' action='admin_page.php' style='display:inline;' id='deleteFormVietnam" . $song_id . "'>
                            <input type='hidden' name='delete_song_id' value='" . $song_id . "'>
                            <input type='hidden' name='delete_category' value='vietnam'>
                            <button type='button' class='btn btn-danger btn-sm'
                                onclick=\"confirmDelete('deleteFormVietnam" . $song_id . "', '" . $song_name . "')\">
                                Delete <i class='fa fa-trash'></i>
                            </button>
                        </form>
                    </td>
                </tr>
            ";
    }

    echo "
                        </tbody>
                    </table>
                </div>
            </div>
        </div><br>
        ";
    ?>
    <!-- //Vietnam Songs Section -->

    <!-- English Songs Section -->
    <div class="alert alert-primary" role="alert">
        <center>
            <h4 class="text-center  text-blue p-2 rounded">English Songs</h4>
        </center>
    </div>
    <form method="post" action="admin_page.php" enctype="multipart/form-data">
        <div class="container mt-3">
            <div class="row align-items-center">
                <!-- Upload Audio File -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold color-white">Upload Audio File</label>
                        <div class="input-group">
                            <input type="file" class="form-control bg-primary text-white" name="audio_file" required>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fa fa-music"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Song Name -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold color-white">Song Name</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="song_name" placeholder="Enter song name" required>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fa fa-headphones"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row align-items-center">
                <!-- Upload Cover Image -->
                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <label class="font-weight-bold color-white">Upload Cover Image</label>
                        <div class="input-group">
                            <input type="file" class="form-control bg-primary text-white" name="song_image" required>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fa fa-image"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Singer Name -->
                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <label class="font-weight-bold color-white">Singer Name</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="singer_name" placeholder="Enter singer name" required>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fa fa-user"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Button -->
                <div class="col-md-4 mt-3 d-flex justify-content-center align-items-center">
                    <button type="submit" name="upload_english" class="btn btn-success btn-lg px-5">
                        Upload <i class="fa fa-upload ml-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
    <?php
    include('connection.php');
    $sql = "SELECT * FROM songs WHERE cat_id=3 ORDER BY song_id";
    $result = mysqli_query($conn, $sql);
    echo "
        <div class='card'>
            <h3 class='card-header text-center font-weight-bold text-uppercase py-4'>English Songs Uploaded</h3>
            <div class='card-body p-0'>
                <div style='max-height: 450px; overflow-y: auto; border: 1px solid #ddd;'>
                    <table class='table table-bordered text-center m-0' style='border-collapse: separate; border-spacing: 0;'>
                        <thead>
                            <tr>
                                <th style='position: sticky; top: 0; background-color: white; z-index: 10; height: 50px;'>Song ID</th>
                                <th style='position: sticky; top: 0; background-color: white; z-index: 10; height: 50px;'>Song Name</th>
                                <th style='position: sticky; top: 0; background-color: white; z-index: 10; height: 50px;'>Singer Name</th>
                                <th style='position: sticky; top: 0; background-color: white; z-index: 10; height: 50px;'>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
        ";
    $count = 0;
    while ($row = mysqli_fetch_array($result)) {
        $count++;
        $song_id     = $row['song_id'];
        $song_name   = $row['song_name'];
        $singer_name = $row['singer_name'];
        echo "
                <tr>
                    <td class='pt-3-half'>" . $count . "</td>
                    <td class='pt-3-half'>" . $song_name . "</td>
                    <td class='pt-3-half'>" . $singer_name . "</td>
                    <td>
                        <button type='button' class='btn btn-info btn-sm' 
                            onclick=\"openUpdateModal('" . $song_id . "', '" . $song_name . "', '" . $singer_name . "', 'english')\">
                            Update
                        </button>
                        <form method='post' action='admin_page.php' style='display:inline;' id='deleteFormEnglish" . $song_id . "'>
                            <input type='hidden' name='delete_song_id' value='" . $song_id . "'>
                            <input type='hidden' name='delete_category' value='english'>
                            <button type='button' class='btn btn-danger btn-sm'
                                onclick=\"confirmDelete('deleteFormEnglish" . $song_id . "', '" . $song_name . "')\">
                                Delete <i class='fa fa-trash'></i>
                            </button>
                        </form>
                    </td>
                </tr>
            ";
    }

    echo "
                        </tbody>
                    </table>
                </div>
            </div>
        </div><br>
        ";
    ?>
    <!-- //English Songs Section -->

    <!-- Uploaded Songs Section -->
    <br>

    <div class="alert alert-primary" role="alert">
        <center>
            <h4 class="text-center  text-blue p-2 rounded">Uploaded Songs</h4>
        </center>
    </div>
    <?php
    include('connection.php');
    $sql = "SELECT * FROM songs WHERE singer_id >= 100 ORDER BY song_id";
    $result = mysqli_query($conn, $sql);
    echo "
        <div class='card'>
            <h3 class='card-header text-center font-weight-bold text-uppercase py-4'>User's Songs Uploaded</h3>
            <div class='card-body p-0'>
                <div style='max-height: 630px; overflow-y: auto; border: 1px solid #ddd;'>
                    <table class='table table-bordered text-center m-0' style='border-collapse: separate; border-spacing: 0;'>
                        <thead>
                            <tr>
                                <th style='position: sticky; top: 0; background-color: white; z-index: 10; height: 50px;'>Song ID</th>
                                <th style='position: sticky; top: 0; background-color: white; z-index: 10; height: 50px;'>Song Name</th>
                                <th style='position: sticky; top: 0; background-color: white; z-index: 10; height: 50px;'>Singer Name</th>
                                <th style='position: sticky; top: 0; background-color: white; z-index: 10; height: 50px;'>Audio File</th>
                                <th style='position: sticky; top: 0; background-color: white; z-index: 10; height: 50px;'>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
        ";
    $count = 0;
    while ($row = mysqli_fetch_array($result)) {
        $count++;
        $song_id = $row['song_id'];
        $song_name = $row['song_name'];
        $singer_name = $row['singer_name'];
        $audio_file = $row['audio_file'];
        echo "
                <tr>
                    <td class='pt-3-half'>" . $count . "</td>
                    <td class='pt-3-half'>" . $song_name . "</td>
                    <td class='pt-3-half'>" . $singer_name . "</td>
                    <td class='pt-3-half'>
                        <audio controls preload='none' style='max-width: 250px;'>
                            <source src='songs/$audio_file' type='audio/mp3'>
                        </audio>
                    </td>
                    <td>
                        <form method='post' action='admin_page.php' style='display:inline;' id='deleteFormUploaded" . $song_id . "'>
                            <input type='hidden' name='delete_song_id' value='" . $song_id . "'>
                            <input type='hidden' name='delete_category' value='uploaded'>
                            <button type='button' class='btn btn-danger btn-rounded btn-sm'
                                onclick=\"confirmDelete('deleteFormUploaded" . $song_id . "', '" . $song_name . "')\">
                                Delete Song <i class='fa fa-trash'></i>
                            </button>
                        </form>
                    </td>
                </tr>
            ";
    }

    echo "
                        </tbody>
                    </table>
                </div>
            </div>
        </div><br>
        ";
    ?>
    <!-- //Uploaded Songs Section -->

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

    <!-- copyright -->
    <div class="cpy-right text-center">
        <p>© 2025 My Musical World. All rights reserved</p>
    </div>
    <!-- //copyright -->

    <!-- js-->
    <script src="js/jquery-2.2.3.min.js"></script>
    <!-- MDB core JavaScript -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.14/js/mdb.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.js"></script>
    <!-- SweetAlert JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.28.11/dist/sweetalert2.min.js"></script>


    <!-- Custom JS: Xác nhận xóa và mở modal update -->
    <script>
        function confirmDelete(formId, songName) {
            Swal.fire({
                title: "Are you sure?",
                text: "Are you sure you want to delete the song: " + songName + "?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: "Deleted!",
                        text: "The song '" + songName + "' has been deleted.",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });


                    setTimeout(() => {
                        document.getElementById(formId).submit();
                    }, 1600);
                }
            });
        }

        function openUpdateModal(song_id, song_name, singer_name, category) {
            document.getElementById("update_song_id").value = song_id;
            document.getElementById("update_song_name").value = song_name;
            document.getElementById("update_singer_name").value = singer_name;
            document.getElementById("update_category").value = category;
            $('#updateModal').modal('show');
        }
    </script>
    <!-- //Custom JS: Xác nhận xóa và mở modal update -->
</body>

</html>