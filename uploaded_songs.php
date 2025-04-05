<?php
session_start();
if (!isset($_SESSION['user_id'])) {
	header('location:index.php');
	exit;
}

include('connection.php');

$user_id = $_SESSION['user_id'];

if (isset($_POST['uploaded_songs'])) {
	// Kiểm tra file upload
	if (!isset($_FILES['audio_file']) || $_FILES['audio_file']['error'] !== UPLOAD_ERR_OK) {
		$_SESSION['message'] = '<script type="text/javascript">
                setTimeout(function () { 
                    Swal.fire("Error","Error while uploading. Please check your file!","error");
                }, 500);
            </script>';
		header("Location: uploaded_songs.php");
		exit();
	}

	if (!isset($_FILES['song_image']) || $_FILES['song_image']['error'] !== UPLOAD_ERR_OK) {
		$_SESSION['message'] = '<script type="text/javascript">
                setTimeout(function () { 
                    Swal.fire("Error","Error while uploading. Please check your cover image!","error");
                }, 500);
            </script>';
		header("Location: uploaded_songs.php");
		exit();
	}
	$audio_file  = $_FILES['audio_file']['name'];
	$song_name   = mysqli_real_escape_string($conn, $_POST['song_name']);
	$song_image  = $_FILES['song_image']['name'];
	$singer_name = mysqli_real_escape_string($conn, $_POST['singer_name']);

	$singer_id = $user_id;
	$cat_id    = 2;
	$language = $_POST['language'] ?? '';

	switch ($language) {
		case 'vietnamese':
			$cat_id = 2;
			break;
		case 'english':
			$cat_id = 3;
			break;
		default:
			$_SESSION['message'] = '<script>
				setTimeout(function () { 
					Swal.fire("Error","Invalid language selected!","error");
				}, 500);
			</script>';
			header("Location: uploaded_songs.php");
			exit();
	}
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
                  Swal.fire("Uploaded"," <b>Uploaded song successfully ' . $song_name . '</b>","success");
                }, 500);
              </script>';
		header("Location: uploaded_songs.php");
		exit();
	} else {
		$_SESSION['message'] = '<script type="text/javascript">
                setTimeout(function () { 
                  Swal.fire("Error","Error while uploading. Please check your internet connection!","error");
                }, 500);
              </script>';
		header("Location: uploaded_songs.php");
		exit();
	}
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
		header("Location: uploaded_songs.php");
		exit();
	} else {
		$_SESSION['message'] = '<script type="text/javascript">
                setTimeout(function () { 
                  sweetAlert("Error","Error while deleting. Please check your internet connection!","error");
                }, 500);
              </script>';
		header("Location: uploaded_songs.php");
		exit();
	}
}



// Lấy danh sách bài hát từ bảng `songs` (cat_id=2 là nhạc Việt Nam)
$sql_songs = "SELECT * FROM songs WHERE singer_id = '$user_id' ORDER BY song_id ASC";
$res_songs = mysqli_query($conn, $sql_songs);

while ($song = mysqli_fetch_array($res_songs)) {
	$song_id = $song['song_id'];

	// Nếu tồn tại POST[$song_id], nghĩa là form của bài hát này được submit
	if (isset($_POST[$song_id])) {
		// Kiểm tra đã có trong bảng favorite_songs chưa
		$check_sql = "SELECT * FROM favorite_songs WHERE song_id = '$song_id' AND user_id = '$user_id'";
		$check_res = mysqli_query($conn, $check_sql);

		if (mysqli_num_rows($check_res) > 0) {
			// Đã có sẵn => Hiển thị cảnh báo
			echo '<script>
                setTimeout(function(){
                    Swal.fire("Warning", "<b>You have already added this song to your favorite list!</b>", "error");
                }, 500);
            </script>';
		} else {
			// Chưa có => Thêm vào favorite_songs
			$insert_sql = "INSERT INTO favorite_songs (user_id, song_id) VALUES ('$user_id', '$song_id')";
			$insert_res = mysqli_query($conn, $insert_sql);

			if ($insert_res) {
				$song_name = $song['song_name']; // để hiển thị cho người dùng biết bài nào đã thêm
				echo '<script>
                    setTimeout(function(){
                        Swal.fire("Added", "<b>Song ' . $song_name . ' is successfully added to your favorite songs</b>", "success");
                    }, 500);
                </script>';
			} else {
				echo '<script>
                    setTimeout(function(){
                        Swal.fire("Oops...", "<b>Error while adding. Please check your internet connection!</b>", "error");
                    }, 500);
                </script>';
			}
		}
	}
}



mysqli_data_seek($res_songs, 0);
?>


<!DOCTYPE HTML>
<html>

<head>
	<title>My Musical World | Uploaded Songs</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
	<script>
		addEventListener("load", function() {
			setTimeout(hideURLbar, 0);
		}, false);

		function hideURLbar() {
			window.scrollTo(0, 1);
		}
	</script>
	<link rel="icon" href="images/i1.png" />
	<!-- Bootstrap Core CSS -->
	<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
	<!-- Material Design Bootstrap -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.14/css/mdb.min.css" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="css/style.css" rel='stylesheet' type='text/css' />
	<!-- font-awesome icons -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<!-- //Custom Theme files -->
	<!--webfonts-->
	<link href="//fonts.googleapis.com/css?family=Ubuntu:300,300i,400,400i,500,500i,700,700i" rel="stylesheet">
	<!--//webfonts-->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@7.28.11/dist/sweetalert2.min.css">
	<!-- js-->
	<script src="js/jquery-2.2.3.min.js"></script>
	<!-- js-->
	<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

	<link rel="stylesheet" href="css/card.css">

	<style>
		/* card details start  */
		@import url('https://fonts.googleapis.com/css?family=Raleway:400,400i,500,500i,600,600i,700,700i,800,800i,900,900i|Roboto+Condensed:400,400i,700,700i');
		/* End card section */
	</style>

</head>

<body>
	<!-- header -->
	<header>
		<div class="container">
			<nav class="navbar navbar-expand-lg navbar-light">
				<a class="navbar-brand" href="profile.php">
					My Musical World
				</a>
				<pre>                 </pre>
				<li class="nav-item">
					<b style="font-size:20px;">
						<p style="color:#fff !important; font-weight: bold;"><?php echo "Logged in as " . $_SESSION['username']; ?></p>
					</b>
					<p style="color:#fff !important; font-weight: bold;">| Uploaded Songs |</p>
				</li>
				<button class="navbar-toggler ml-lg-auto ml-sm-5" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
					aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<ul class="navbar-nav text-center ml-auto">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown"
								aria-haspopup="true" aria-expanded="false">Track</button>
							<div class="dropdown-menu dropdown-primary">
								<a class="dropdown-item" href="vietnam_songs.php"><b>Vietnam Songs</b></a>
								<a class="dropdown-item" href="english_songs.php"><b>English Songs</b></a>
								<a class="dropdown-item" href="uploaded_songs.php"><b>Uploaded Songs</b></a>
							</div>
						</div>
						<li class="nav-item">
							<a class="nav-link" href="favorite_list.php"><i class='fa fa-heart' style='font-size:40px;color:red'></i></a>
						</li>
						<li class="nav-item">
							<a class="nav-link scroll" href="#contact">contact</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="logout.php">logout</a>
						</li>
					</ul>
				</div>
			</nav>
		</div>
	</header>
	<!-- //header -->

	<center><br><b>
			<div class="alert alert-primary" role="alert">
				You want to upload your songs?...<a href="#" class="alert-link">Upload below</a>. And get featured.
			</div><b>
	</center>

	<form method="post" action="uploaded_songs.php" enctype="multipart/form-data">
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
					<button type="submit" name="uploaded_songs" class="btn btn-success btn-lg px-5">
						Upload <i class="fa fa-upload ml-2"></i>
					</button>
				</div>
			</div>

			<div class="row align-items-center">
				<div class="col-md-4 mt-3">
					<div class="form-group">
						<label class="font-weight-bold color-white"></label>
						<div class="input-group">

							<div class="input-group-append">

							</div>
						</div>
					</div>
				</div>

				<!-- Language -->
				<div class="col-md-4 mt-3">
					<div class="form-group">
						<label class="font-weight-bold color-white">Language</label>
						<div class="input-group">
							<select class="form-control" name="language" required>
								<option value="">-- Select Language --</option>
								<option value="vietnamese">Vietnamese</option>
								<option value="english">English</option>
							</select>
							<div class="input-group-append">
								<span class="input-group-text"><i class="fa fa-globe"></i></span>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-4 mt-3">

				</div>
			</div>
		</div>
	</form>

	<center><b>
			<div class="alert alert-primary" role="alert">
				Most featured songs...<a href="#" class="alert-link color-white">Listen to music </a>. And have fun.
			</div><b>
	</center>

	<?php
	include('connection.php');
	$user_id = $_SESSION['user_id'];
	$sql = "SELECT * FROM songs WHERE singer_id = $user_id ORDER BY song_id";
	$result = mysqli_query($conn, $sql);
	echo "
        <div class='card'>
            <h3 class='card-header text-center font-weight-bold text-uppercase py-4'>Uploaded Songs</h3>
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
                        <form method='post' action='uploaded_songs.php' style='display:inline;' id='deleteFormUploaded" . $song_id . "'>
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


	<!-- contact top -->
	<div class="contact-top text-center" id="more_info">
		<div class="content-contact-top">
			<h3 class="stat-title text-white">for more information</h3>
			<h2 class="stat-title text-white">stay in touch</h2>
			<p class="text-white w-75 mx-auto">My Musical World a unique platform for music lovers.
			</p>
		</div>
	</div>
	<!-- //contact top -->
	<!-- contact -->
	<div class="w3-contact py-5" id="contact">
		<div class="container">
			<div class="row contact-form pt-md-5">
				<!-- contact details -->
				<div class="col-lg-6 contact-bottom d-flex flex-column contact-right-w3ls">
					<h5>get in touch</h5>
					<div class="fv3-contact">
						<div class="row">
							<div class="col-2">
								<span><box-icon name='envelope' type='solid'></box-icon></span>
							</div>
							<div class="col-10">
								<h6>email</h6>
								<p>
									<a href="mailto:mymusicworld.2025@gmail.com" class="text-gray">mymusicworld.2025@gmail.com</a>
								</p>
							</div>
						</div>
					</div>
					<div class="fv3-contact my-4">
						<div class="row">
							<div class="col-2">
								<span><box-icon type='solid' name='phone'></box-icon></span>
							</div>
							<div class="col-10">
								<h6>phone</h6>
								<p>+84 012345678</p>
							</div>
						</div>
					</div>
					<div class="fv3-contact">
						<div class="row">
							<div class="col-2">
								<span><box-icon name='home' type='solid'></box-icon></span>
							</div>
							<div class="col-10">
								<h6>address</h6>
								<p>College of information and technology - CTU</p>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 wthree-form-left my-lg-0 mt-5">
					<h5>send us a mail</h5>
					<!-- contact form grid -->
					<div class="contact-top1">
						<form id="contactForm" class="contact-wthree">
							<div class="form-group d-flex">
								<label class="color-white">
									Name
								</label>
								<input class="form-control" type="text" placeholder="Name" name="name" required>
							</div>
							<div class="form-group d-flex">
								<label class="color-white">
									Email
								</label>
								<input class="form-control" type="email" placeholder="Email" name="email" required>
							</div>
							<div class="form-group d-flex">
								<label class="color-white">
									Phone
								</label>
								<input class="form-control" type="text" placeholder="Phone number" name="phone" required>
							</div>
							<div class="form-group d-flex">
								<label class="color-white">
									Message
								</label>
								<textarea class="form-control" rows="5" name="message" placeholder="Your message" required></textarea>
							</div>
							<div class="d-flex justify-content-end">
								<button type="submit" class="btn btn-agile btn-block w-50">Submit</button>
							</div>
						</form>
					</div>
					<!--  //contact form grid ends here -->
				</div>

			</div>
			<!-- //contact details container -->
		</div>
	</div>
	<!-- //contact -->
	<!-- copyright -->
	<div class="cpy-right text-center">
		<p>© 2025 My Musical World. All rights reserved</p>
	</div>
	<!-- //copyright -->
	<!-- MDB core JavaScript -->
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.14/js/mdb.min.js"></script>
	<!-- start-smooth-scrolling -->
	<script src="js/move-top.js "></script>
	<script src="js/easing.js "></script>
	<script>
		jQuery(document).ready(function($) {
			$(".scroll ").click(function(event) {
				event.preventDefault();

				$('html,body').animate({
					scrollTop: $(this.hash).offset().top
				}, 1000);
			});
			$('#forgot').click(function() {
				$('#modalLRForm').modal('hide');
				$('ForgotPasswordModal').modal('show');
			});
		});
	</script>

	<!-- Bootstrap Core JavaScript -->
	<script src="js/bootstrap.js"></script>
	<!-- //Bootstrap Core JavaScript -->
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/sweetalert2@7.28.11/dist/sweetalert2.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<!-- send us a mail section -->
	<script>
		$(document).ready(function() {
			$('#contactForm').on('submit', function(e) {
				e.preventDefault();

				$.ajax({
					type: 'POST',
					url: 'send_mail.php',
					data: $(this).serialize(),
					dataType: 'json',
					success: function(response) {
						if (response.status === 'success') {
							Swal.fire({
								icon: 'success',
								title: 'Success!',
								text: response.message,
								showConfirmButton: false,
								timer: 1500,
								customClass: {
									popup: 'swal2-spotify'
								}
							});
							$('#contactForm')[0].reset();
						} else {
							Swal.fire({
								icon: 'error',
								title: 'Error!',
								text: response.message,
								customClass: {
									popup: 'swal2-spotify'
								}
							});
						}
					},
					error: function() {
						Swal.fire({
							icon: 'error',
							title: 'Error!',
							text: 'An error occurred. Please try again later.',
							customClass: {
								popup: 'swal2-spotify'
							}
						});
					}
				});
			});
		});
	</script>
	<!-- //send us a mail section -->
	<!-- confirm delete song -->
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
	</script>
	<!-- //confirm delete song -->

</body>

</html>