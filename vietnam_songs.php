<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:index.php');
    exit;
}

include('connection.php');

$user_id = $_SESSION['user_id'];

// Lấy danh sách bài hát từ bảng `songs` (cat_id=2 là nhạc Việt Nam)
$sql_songs = "SELECT * FROM songs WHERE cat_id = 2 ORDER BY song_id ASC";
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
	<title>My Musical World | Vietnam Songs</title>
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
	<style>
		@import url('https://fonts.googleapis.com/css?family=Raleway:400,400i,500,500i,600,600i,700,700i,800,800i,900,900i|Roboto+Condensed:400,400i,700,700i');

		section {
			padding: 100px 0;
		}

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

		/* End card section */
	</style>


	<script>
		$(document).ready(function () {
			// Map file => cat_id
			const albumInfo = {
				"vietnam_songs.php": 2,
				"english_songs.php": 3,
				"uploaded_songs.php": 4
			};

			// Xác định category ID dựa vào URL
			let currentPage = window.location.href;
			let catId = 2; // default

			for (let page in albumInfo) {
				if (currentPage.includes(page)) {
					catId = albumInfo[page];
					break;
				}
			}


			// Hàm load bài hát từ server
			function loadSongs(page) {
				$.ajax({
					url: "fetch_songs.php",
					type: "POST",
					data: {
						page_no: page,
						cat_id: catId
					},
					success: function (response) {
						try {
							let data = typeof response === 'object' ? response : JSON.parse(response);
							$("#song-list").html(data.songs);
							$("#pagination").html(data.pagination);
						} catch (e) {
							console.error("JSON parse error:", e);
							$("#song-list").html("<div class='alert alert-danger'>Cannot load song list</div>");
						}
					},
					error: function (xhr, status, error) {
						console.error("AJAX Error:", error);
						$("#song-list").html("<div class='alert alert-danger'>Server connection error</div>");
					}
				});
			}

			// Tải trang đầu tiên
			loadSongs(1);

			// Phân trang
			$(document).on("click", ".pagination a", function (e) {
				e.preventDefault();
				const page = $(this).data("page");
				loadSongs(page);
			});

			// Thêm vào danh sách yêu thích
			$(document).on("click", ".add-to-fav", function () {
				const songId = $(this).data("songid");
				const heartIcon = $(this).find("i.fa-heart");

				$.ajax({
					url: "add_favorite.php",
					type: "POST",
					contentType: "application/json",
					data: JSON.stringify({
						song_id: songId
					}),
					success: function (response) {
						try {
							let data = typeof response === 'object' ? response : JSON.parse(response);
							if (data.status === 'success') {
								heartIcon.addClass('text-danger');
								Swal.fire('Success', data.message, 'success');
							} else {
								Swal.fire(
									data.status === 'warning' ? 'Warning' : 'Error',
									data.message,
									data.status
								);
							}
						} catch (e) {
							console.error("JSON parse error:", e);
							Swal.fire('Error', 'Invalid response from server', 'error');
						}
					},
					error: function (xhr, status, error) {
						console.error("Favorite AJAX Error:", error);
						try {
							let response = JSON.parse(xhr.responseText);
							Swal.fire('Error', response.message, 'error');
						} catch (e) {
							Swal.fire('Error', 'Failed to connect to server', 'error');
						}
					}
				});
			});
		});
		</script>
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
					<p style="color:#fff !important; font-weight: bold;">| Vietnam Songs |</p>
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
								<!-- <a class="dropdown-item" href="kannada_songs.php"><b>Kannada Songs</b></a> -->
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

	<!-- Danh sách bài hát -->
	<section class='details-card'>
		<div class='container'>
			<div class='row justify-content-center' id="song-list"></div>
		</div>
	</section>

	<!-- Pagination -->
	<div class="pagination text-center justify-content-center" id="pagination"></div>



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
								<span ><box-icon name='gmail' type='logo' ></box-icon></span>
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
	<!-- copyright -->
	<div class="cpy-right text-center">
		<p>© 2025 My Musical World. All rights reserved</p>
	</div>
	<!-- //copyright -->
	<!-- js-->

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
				$('ForgotPasswordModal').modal('show');
			});
		});
	</script>
	<!-- //end-smooth-scrolling -->
	<!-- smooth-scrolling-of-move-up -->
	<script>
		$(document).ready(function() {
			$().UItoTop({
				easingType: 'easeOutQuart'
			});
		});
	</script>
	<script src="js/SmoothScroll.min.js "></script>
	<!-- //smooth-scrolling-of-move-up -->
	<!-- Bootstrap Core JavaScript -->
	<script src="js/bootstrap.js"></script>
	<!-- //Bootstrap Core JavaScript -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
</body>

</html>