<?php include('auth.php'); ?>
<!DOCTYPE HTML>
<html>

<head>
	<title>My Musical World | Vietnam Songs</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
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
	<link rel="stylesheet" href="css/card.css">
	<style>
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
	
	<!-- //Show songs -->
	<div id="show-songs">
		
	</div>

	<!-- //End Show songs -->
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
	<!-- copyright -->
	<div class="cpy-right text-center">
		<p>© 2025 My Musical World. All rights reserved</p>
	</div>
	<!-- //copyright -->
	<!-- js-->
	<script src="js/jquery-2.2.3.min.js"></script>
	<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
	<!-- js-->
	<!-- MDB core JavaScript -->
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.14/js/mdb.min.js"></script>
	<!-- start-smooth-scrolling -->
	<script src="js/move-top.js "></script>
	<script src="js/easing.js "></script>
	<!-- Bootstrap Core JavaScript -->
	<script src="js/bootstrap.js"></script>
	<!-- //Bootstrap Core JavaScript -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<!-- send us a mail section -->
	<script src="js/contact.js"></script>
	<script src="js/songs.js"></script>
</body>

</html>