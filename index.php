<!DOCTYPE HTML>
<html>

<head>
	<title>My Musical World</title>
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
	<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
	<style>
		.swal2-popup {
			font-family: 'Ubuntu', sans-serif;
		}

		.scroll-container {
			max-height: 1000px;
			overflow-y: auto;
			overflow-x: hidden;
		}
	</style>
</head>

<body>
	<!-- header -->
	<header>
		<div class="container">
			<nav class="navbar navbar-expand-lg navbar-light">
				<a class="navbar-brand" href="index.php">
					My Musical World
				</a>
				<button class="navbar-toggler ml-lg-auto ml-sm-5" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
					aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<ul class="navbar-nav text-center ml-auto">
						<li class="nav-item  mr-3">
							<a class="nav-link scroll" href="#about">about</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="" data-target="#modalLRForm" data-toggle="modal">Login/Signup</a>
						</li>
						<li class="nav-item">
							<a class="nav-link scroll" href="#contact">contact</a>
						</li>
					</ul>
				</div>
			</nav>
		</div>
	</header>
	<!-- //header -->

	<!-- banner -->
	<div class="banner" id="home">
		<div class="container">
			<div class="banner-text">
				<div class="slider-info text-right">
					<h1 class="text-uppercase">listen to music anywhere.</h1>
					<p class="text-white">Are you a music lover?...Upload your music here and enjoy with others.</p>
					<a class="btn btn-agile  mt-4 scroll" href="#about" role="button">read more</a>
				</div>
			</div>
		</div>
	</div>
	<!-- //banner -->

	<!-- about-->
	<section class="wthree-row" id="about">
		<div class="row justify-content-center align-items-center no-gutters abbot-main">
			<div class="col-lg-6 p-0">
				<img src="images/about.jpg" class="img-fluid" alt="" />
			</div>
			<div class="col-lg-6 abbot-right px-md-5  py-lg-0 py-3">
				<div class="card">
					<div class="card-body px-lg-5">
						<h3 class="stat-title card-title align-self-center mb-sm-5 mb-3">my musical world
							<br> get addicted to music
						</h3>
						<span class="w3-line"></span>
						<p class="card-text align-self-center my-4 text-white">
							Are you passionate about music and want to share your favorite songs with the world? Join our music community, where you can upload your tracks for everyone to listen to and rate.


						</p>
						<p class="card-text align-self-center mb-5 text-white">Explore the world of music in your own way – share, enjoy, and shine!.</p>
						<a href="#more_info" class="btn btn-agile abt_card_btn scroll">Know More</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- //about -->

	<?php
	include('connection.php');
	// Lấy bài hát yêu thích
	$sql = "SELECT *
			FROM songs
			ORDER BY song_id ASC";
	$stmt = mysqli_prepare($conn, $sql);
	//mysqli_stmt_bind_param($stmt, "i", $user_id);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$songs = mysqli_fetch_all($result, MYSQLI_ASSOC);
	mysqli_stmt_close($stmt);
	?>
	<!-- Danh sách nhạc -->
	<<div class="alert alert-primary" role="alert">
		<center>
			<h4 class="text-center  text-blue p-2 rounded">My Music List</h4>
		</center>
		</div>
		<div class="container mt-5">
			<div class="scroll-container p-2">
				<div class="row">
					<?php foreach ($songs as $song): ?>
						<div class="col-md-4 mb-4">
							<div class="card">
								<img src="songs/img/<?= htmlspecialchars($song['song_image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($song['song_name']) ?>">
								<div class="card-body">
									<h5 class="card-title" style="color: white; padding: 5px 0; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; line-height: 20px; height: 24px;"><?= htmlspecialchars($song['song_name']) ?></h5>
									<p class="card-text">By <?= htmlspecialchars($song['singer_name']) ?></p>
									<audio controls style="width: 100%;">
										<source src="songs/<?= htmlspecialchars($song['audio_file']) ?>" type="audio/mp3">
										Your browser does not support the audio element.
									</audio>
								</div>
							</div>
						</div>

					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<!-- //Danh sách nhạc -->

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

		<!--Modal: Login / Register Form-->
		<div class="modal fade" id="modalLRForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog cascading-modal" role="document">
				<!--Content-->
				<div class="modal-content">

					<!--Modal cascading tabs-->
					<div class="modal-c-tabs">

						<!-- Nav tabs -->
						<ul class="nav nav-tabs md-tabs tabs-2 light-gray darken-3" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" data-toggle="tab" href="#panel7" role="tab"><i class="fa fa-user mr-1"></i>
									Login</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-toggle="tab" href="#panel8" role="tab"><i class="fa fa-user-plus mr-1"></i>
									Register</a>
							</li>
						</ul>

						<!-- Tab panels -->
						<div class="tab-content">
							<!--Panel 7-->
							<div class="tab-pane fade in show active" id="panel7" role="tabpanel">

								<!--Body-->
								<form action="validate.php" method="post">
									<div class="modal-body mb-1">
										<div class="md-form form-sm mb-5">
											<i class="fa fa-envelope prefix"></i>
											<input type="email" id="modalLRInput10" class="form-control form-control-sm validate" name="email_address" required>
											<label data-error="wrong" data-success="right" for="modalLRInput10">Your email</label>
										</div>

										<div class="md-form form-sm mb-4">
											<i class="fa fa-lock prefix"></i>
											<input type="password" id="modalLRInput11" class="form-control form-control-sm validate" name="password" required>
											<label data-error="wrong" data-success="right" for="modalLRInput11">Your password</label>
										</div>
										<div class="text-center mt-2">
											<button class="btn btn-info" type="submit" name="login">Log in <i class="fa fa-sign-in ml-1"></i></button>
										</div>
									</div>
								</form>
								<!--Footer-->
								<div class="modal-footer">
									<div class="options text-center text-md-right mt-1">
										<p>Forgot <a class="blue-text" id="forgot" data-target="#ForgotPasswordModal" data-toggle="modal">Password?</a></p>
									</div>
									<button type="button" class="btn btn-outline-info waves-effect ml-auto" data-dismiss="modal">Close</button>
								</div>

							</div>
							<!--/.Panel 7-->

							<!--Panel 8-->
							<div class="tab-pane fade" id="panel8" role="tabpanel">

								<!--Body-->
								<form action="validate.php" method="post">
									<div class="modal-body">
										<div class="md-form form-sm mb-5">
											<i class="fa fa-user prefix"></i>
											<input type="text" id="modalLRInput111" class="form-control form-control-sm validate" name="username" required>
											<label data-error="wrong" data-success="right" for="modalLRInput111">User Name</label>
										</div>

										<div class="md-form form-sm mb-5">
											<i class="fa fa-mobile prefix"></i>
											<input type="text" id="modalLRInput15" class="form-control form-control-sm validate" name="mobile_number" required>
											<label data-error="wrong" data-success="right" for="modalLRInput15">Mobile Number</label>
										</div>

										<div class="md-form form-sm mb-5">
											<i class="fa fa-envelope prefix"></i>
											<input type="email" id="modalLRInput12" class="form-control form-control-sm validate" name="email_address" required>
											<label data-error="wrong" data-success="right" for="modalLRInput12">Your email</label>
										</div>

										<div class="md-form form-sm mb-5">
											<i class="fa fa-lock prefix"></i>
											<input type="password" id="modalLRInput13" class="form-control form-control-sm validate" name="password" required>
											<label data-error="wrong" data-success="right" for="modalLRInput13">Your password</label>
										</div>

										<div class="md-form form-sm mb-4">
											<i class="fa fa-lock prefix"></i>
											<input type="password" id="modalLRInput14" class="form-control form-control-sm validate" name="confirm_password" required>
											<label data-error="wrong" data-success="right" for="modalLRInput14">Repeat password</label>
										</div>

										<div class="text-center form-sm mt-2">
											<button class="btn btn-info" type="submit" name="register">Sign up <i class="fa fa-sign-in ml-1"></i></button>
										</div>
									</div>
								</form>
								<!--Footer-->
								<div class="modal-footer">
									<button type="button" class="btn btn-outline-info waves-effect ml-auto" data-dismiss="modal">Close</button>
								</div>
							</div>
							<!--/.Panel 8-->
						</div>

					</div>
				</div>
				<!--/.Content-->
			</div>
		</div>
		<!--Modal: Login / Register Form-->

		<!-- Modal for Forgot Password -->
		<div class="modal fade" id="ForgotPasswordModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header text-center">
						<h4 class="modal-title w-100 font-weight-bold color-white"><b>Forgot Your Password</b></h4>
						<button type="button" class="close color-white" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<form action="validate.php" method="post">
						<div class="modal-body mx-3">
							<div class="md-form mb-5">
								<i class="fa fa-envelope prefix "></i>
								<input type="email" id="defaultForm-email" class="form-control validate" required name="email_address">
								<label data-error="wrong" data-success="right" for="defaultForm-email">Your email</label>
							</div>
						</div>
						<div class="modal-footer d-flex justify-content-center">
							<button class="btn btn-default" name="forgot">Send Password <i class="fa fa-sign-in"></i></button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- //Modal for Forgot Password -->

		<!-- js-->
		<script src="js/jquery-2.2.3.min.js"></script>
		<!-- js-->
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
					$('#ForgotPasswordModal').modal('show');
				});
			});
		</script>


		<!-- Bootstrap Core JavaScript -->
		<script src="js/bootstrap.js">
		</script>
		<!-- //Bootstrap Core JavaScript -->
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
</body>

</html>