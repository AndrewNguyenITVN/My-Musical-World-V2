<?php
session_start();
if (!isset($_SESSION['email_address']) || !isset($_SESSION['user_id'])) {
	header('location:index.php');
	exit;
}

include('connection.php');

// Lấy thông tin người dùng hiện tại
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_song_id'])) {
	$song_id = (int)$_POST['delete_song_id'];

	// Xóa bài hát yêu thích
	$delete_sql = "DELETE FROM favorite_songs WHERE song_id = ? AND user_id = ?";
	$stmt = mysqli_prepare($conn, $delete_sql);
	mysqli_stmt_bind_param($stmt, "ii", $song_id, $user_id);
	mysqli_stmt_execute($stmt);

	// Lấy lại tổng số bài hát sau khi xóa
	$count_sql = "SELECT COUNT(*) AS total FROM favorite_songs WHERE user_id = ?";
	$stmt_count = mysqli_prepare($conn, $count_sql);
	mysqli_stmt_bind_param($stmt_count, "i", $user_id);
	mysqli_stmt_execute($stmt_count);
	$count_result = mysqli_stmt_get_result($stmt_count);
	$total = mysqli_fetch_assoc($count_result)['total'];
	mysqli_stmt_close($stmt_count);

	if (mysqli_stmt_affected_rows($stmt) > 0) {
		echo json_encode([
			'status' => 'success',
			'message' => 'Song removed from favorites',
			'total' => $total,
			'reload' => $total === 0 ? true : false
		]);
	} else {
		echo json_encode(['status' => 'error', 'message' => 'Song could not be removed']);
	}

	mysqli_stmt_close($stmt);
	exit;
}


// Lấy bài hát yêu thích
$sql = "SELECT s.song_id, s.song_name, s.singer_name, s.song_image, s.audio_file
        FROM favorite_songs f
        JOIN songs s ON f.song_id = s.song_id
        WHERE f.user_id = ?
        ORDER BY s.song_id ASC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$songs = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE HTML>
<html>

<head>
	<title>My Musical World | Favorite Songs</title>
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
	<link rel="stylesheet" href="css/card.css">
	<style>
		/* Card details */
		@import url('https://fonts.googleapis.com/css?family=Raleway:400,400i,500,500i,600,600i,700,700i,800,800i,900,900i|Roboto+Condensed:400,400i,700,700i');

		/* Riêng page này */
		.scroll-container {
			max-height: 1000px;
			overflow-y: auto;
			overflow-x: hidden;
		}
	</style>
</head>

<body>
	<!-- Header -->
	<header>
		<div class="container">
			<nav class="navbar navbar-expand-lg navbar-light">
				<a class="navbar-brand" href="profile.php">My Musical World</a>
				<pre>                 </pre>
				<li class="nav-item">
					<b style="font-size:20px;">
						<p style="color:#fff !important; font-weight: bold;"><?php echo "Logged in as " . $_SESSION['username']; ?></p>
					</b>
					<p style="color:#fff !important; font-weight: bold;">| Favorite Songs |</p>
				</li>
				<button class="navbar-toggler ml-lg-auto ml-sm-5" type="button" data-toggle="collapse"
					data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
					aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<ul class="navbar-nav text-center ml-auto">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu1"
								data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Track</button>
							<div class="dropdown-menu dropdown-primary">
								<a class="dropdown-item" href="vietnam_songs.php"><b>Vietnam Songs</b></a>
								<a class="dropdown-item" href="english_songs.php"><b>English Songs</b></a>
								<a class="dropdown-item" href="uploaded_songs.php"><b>Uploaded Songs</b></a>
							</div>
						</div>
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
	<!-- //Header -->

	<!-- Hiển thị danh sách bài hát yêu thích -->
	<section class="details-card">
		<div class="container mt-5">
			<div class="scroll-container p-2">
				<div class="row">
					<?php if (!empty($songs) && is_array($songs)): ?>
						<?php foreach ($songs as $song): ?>
							<div class="col-md-4 mb-4">
								<div class="card-song">
									<img src="songs/img/<?= htmlspecialchars($song['song_image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($song['song_name']) ?>">
									<div class="card-body">
										<h5 class="card-title" style="color: white; padding: 5px 0; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; line-height: 20px; height: 24px;">
											<?= htmlspecialchars($song['song_name']) ?>
										</h5>
										<p class="card-text">By <?= htmlspecialchars($song['singer_name']) ?></p>
										<audio controls style="width: 100%;">
											<source src="songs/<?= htmlspecialchars($song['audio_file']) ?>" type="audio/mp3">
											Your browser does not support the audio element.
										</audio>
										<button class="btn btn-danger btn-block mt-2 delete-btn" data-songid="<?= $song['song_id'] ?>">
											<i class="fa fa-trash"></i> Remove
										</button>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					<?php else: ?>
						<div class="col-12 text-center">
							<p class="text-white">No favorite songs added yet.</p>
						</div>
					<?php endif; ?>

				</div>
			</div>
		</div>
	</section>
	<!-- //Hiển thị danh sách bài hát yêu thích -->

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
									<a href="mailto:mymusicworld.2025@gmail" class="text-gray">mymusicworld.2025@gmail.com</a>
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

	<!-- Footer -->
	<div class="cpy-right text-center">
		<p>© 2025 My Musical World. All rights reserved</p>
	</div>
	<!-- JS Scripts -->
	<script src="js/jquery-2.2.3.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.14/js/mdb.min.js"></script>
	<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="js/move-top.js"></script>
	<script src="js/easing.js "></script>
	<script src="js/contact.js"></script>
	<script src="js/songs.js"></script>
	<script src="js/bootstrap.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.28.11/dist/sweetalert2.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<!-- Xóa bài hát yêu thích -->
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			const deleteButtons = document.querySelectorAll(".delete-btn");

			deleteButtons.forEach(button => {
				button.addEventListener("click", function() {
					const songId = this.dataset.songid;

					Swal.fire({
						title: "Are you sure?",
						text: "Do you really want to remove this song from favorites?",
						icon: "warning",
						showCancelButton: true,
						confirmButtonColor: "#d33",
						cancelButtonColor: "#3085d6",
						confirmButtonText: "Yes, remove it!"
					}).then((result) => {
						if (result.isConfirmed) {
							fetch(window.location.href, {
									method: "POST",
									headers: {
										"Content-Type": "application/x-www-form-urlencoded"
									},
									body: "delete_song_id=" + encodeURIComponent(songId)
								})
								.then(res => res.json())  // Chuyển phản hồi thành JSON
								.then(data => {           // Dữ liệu JSON sẵn sàng để sử dụng
									if (data.status === 'success') {
										Swal.fire({
											title: "Removed!",
											text: data.message,
											icon: "success",
											showConfirmButton: false,
											timer: 1000
										});

										// Xóa card khỏi giao diện
										const card = button.closest(".col-md-4");
										card.remove();

										// Nếu không còn bài hát nào => reload (nếu cần)
										if (data.reload) {
											location.reload();
										}
									} else {
										Swal.fire("Oops!", data.message, "error");
									}
								})
								.catch(() => {
									Swal.fire("Error", "Could not connect to server", "error");
								});
						}
					});
				});
			});
		});
	</script>
	<!-- //Xóa bài hát yêu thích -->
</body>

</html>