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
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
    <!-- Material Design Bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.14/css/mdb.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <!-- font-awesome icons -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@7.28.11/dist/sweetalert2.min.css">
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Card details */
        @import url('https://fonts.googleapis.com/css?family=Raleway:400,400i,500,500i,600,600i,700,700i,800,800i,900,900i|Roboto+Condensed:400,400i,700,700i');
        section { padding: 100px 0; }
        .details-card {
			background: #1f1f1f;
		}

		.card-content {
			background: #ffffff;
			border: 4px;
			box-shadow: 0 2px 5px 0 rgba(0, 0, 0, .16), 0 2px 10px 0 rgba(0, 0, 0, .12);
		}

		.card-img {
			position: relative;
			overflow: hidden;
			border-radius: 0;
			z-index: 1;
		}

		.card-img img {
			width: 100%;
			height: auto;
			display: block;
		}

		.card-img img:hover {
			-webkit-transform: scale(1.1);
			transform: scale(1.1);
			-webkit-transition: all 0.5s;
			transition: all 0.5s;
		}

		.card-img img:not(:hover) {
			-webkit-transform: scale(1.0);
			transform: scale(1.0);
			-webkit-transition: all 0.5s;
			transition: all 0.5s;
		}

		.card-img span {
			position: absolute;
			top: 15%;
			left: 12%;
			background: #1ABC9C;
			padding: 6px;
			color: #fff;
			font-size: 12px;
			border-radius: 4px;
			-webkit-border-radius: 4px;
			-moz-border-radius: 4px;
			-ms-border-radius: 4px;
			-o-border-radius: 4px;
			transform: translate(-50%, -50%);
		}

		.card-img span h4 {
			font-size: 12px;
			margin: 0;
			padding: 10px 5px;
			line-height: 0;
		}

		.card-desc {
			padding-top: 15px;
		}

		.card-desc h3 {
			color: #fff;
			font-weight: 600;
			font-size: 1.0em;
			line-height: 1.3em;
			margin-top: 0;
			margin-bottom: 5px;
			padding: 0;
		}

		.card-desc p {
			color: #747373;
			font-size: 14px;
			font-weight: 400;
			font-size: 1em;
			line-height: 1.5;
			margin: 0px;
			margin-bottom: 20px;
			padding: 0;
			font-family: 'Raleway', sans-serif;
		}

		.btn-card {
			background-color: #b2b2b2;
			color: #fff;
			box-shadow: 0 2px 5px 0 rgba(0, 0, 0, .16), 0 2px 10px 0 rgba(0, 0, 0, .12);
			padding: .84rem 2.14rem;
			font-size: .81rem;
			-webkit-transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
			transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
			-o-transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
			transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
			transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
			margin: 0;
			border: 0;
			-webkit-border-radius: .125rem;
			border-radius: .125rem;
			cursor: pointer;
			text-transform: uppercase;
			white-space: normal;
			word-wrap: break-word;
			color: #fff;
		}

		.btn-card:hover {
			background: red;
		}

		a.btn-card {
			text-decoration: none;
			color: #fff;
		}

		.col-md-3 {
			padding-bottom: 30px;
			padding-left: 10px;
			margin-left: 20px;
			margin-right: 50px;
		}

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
                    <b style="font-size:20px;"><p style="color:#fff !important; font-weight: bold;"><?php echo "Logged in as " . $_SESSION['username']; ?></p></b>
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
    <!-- Hiển thị danh sách bài hát yêu thích -->
    <section >
	<div class="container mt-5" >		
		<div class="scroll-container p-2">
			<div class="row">
				<?php foreach ($songs as $song): ?>
					<div class="col-md-4 mb-4">
						<div class="card">
							<img src="songs/img/<?= htmlspecialchars($song['song_image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($song['song_name']) ?>">
							<div class="card-body">
								<h5 class="card-title" style="color: white; padding: 10px 0; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; line-height: 14px; height: 24px;" ><?= htmlspecialchars($song['song_name']) ?></h5>
								<p class="card-text" >By <?= htmlspecialchars($song['singer_name']) ?></p>
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
			</div>
		</div>
	</div>
	</section>

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
								<span ><box-icon name='envelope' type='solid'></box-icon></span>
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
								<span><box-icon name='home' type='solid' ></box-icon></span>
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
    <script src="js/move-top.js"></script>
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

    <script src="js/bootstrap.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.28.11/dist/sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<script>
		document.addEventListener("DOMContentLoaded", function () {
			const deleteButtons = document.querySelectorAll(".delete-btn");

			deleteButtons.forEach(button => {
				button.addEventListener("click", function () {
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
							.then(res => res.json())
							.then(data => {
								if (data.status === 'success') {
									Swal.fire("Removed!", data.message, "success")
										.then(() => {
												location.reload();
										});
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
</body>
</html>
