<?php
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);

	session_start();
	require_once "class/Facebook/autoload.php";
	require_once 'class/Spyc.class.php';
	$config = Spyc::YAMLLoad('config/config.yml');
	$fb = new Facebook\Facebook([
		'app_id' => $config['facebook']['api-id'],
		'app_secret' => $config['facebook']['api-secret'],
		'default_graph_version' => $config['facebook']['api-version'],
	]);

	$helper = $fb->getRedirectLoginHelper();

	$permissions = ['email']; // Optional permissions
	$url = $_SERVER['HTTP_X_FORWARDED_PROTO']."://gofetch.tk";

	$loginUrl = $helper->getLoginUrl($url.'/actions/fb-callback.php', $permissions);


?>

<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en" >

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="mobile-web-app-capable" content="yes">
	<link rel="apple-touch-icon" sizes="57x57" href="img/favicon/apple-icon-57x57.png">
	<meta name="description" content="Fetch takes a look at prices from around the web for your item and comes up with what we think is the best market value. Log In Now">
	<link rel="apple-touch-icon" sizes="60x60" href="img/favicon/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="img/favicon/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="img/favicon/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="img/favicon/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="img/favicon/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="img/favicon/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="img/favicon/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="img/favicon/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="img/favicon/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="img/favicon/favicon-16x16.png">
	<link rel="manifest" href="img/favicon/manifest.json">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="img/favicon/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">
	<title>Log In | Fetch</title>
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<link rel="stylesheet" href="css/foundation.css">
	<script src="js/vendor/modernizr.js"></script>
	<meta property="og:title" content="Log In | Fetch" />
	<meta property="og:type" content="website" />
	<meta property="og:image" content="https://gofetch.tk/img/dog-badge-large.png" />
	<meta property="og:url" content="https://gofetch.tk/signup.php" />
	<meta property="og:site_name" content="Fetch" />
	<meta property="og:description" content="Fetch takes a look at prices from around the web for your item and comes up with what we think is the best market value. Log In Now" />
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:site" content="@Go_Fetch_App">
	<meta name="twitter:creator" content="@evan_mcc92">
	<meta name="twitter:title" content="Log In | Fetch">
	<meta name="twitter:description" content="Fetch takes a look at prices from around the web for your item and comes up with what we think is the best market value. Log In Now">
	<meta name="twitter:image" content="https://gofetch.tk/img/dog-badge-large.png">
</head>
<body>
	<div class="off-canvas-wrap" data-offcanvas>
		<div class="inner-wrap">

			<?php require_once 'includes/nav.php'; ?>

			<section class="main-section">
				<div class="row" id="body">
					<div class="large-12 columns">
						<h2>Log Me In</h2>
						<a href="<?php echo htmlspecialchars($loginUrl); ?>" class="facebook button split"> <span></span>log me in with facebook</a>
						<!-- <a href="#" class="twitter button split"> <span></span>log me in with twitter</a>
						<a href="#" class="google button split"> <span></span>log me in with google+</a> -->
						<p>
							<div class="strike">
								<span>OR</span>
							</div>
						</p>
						<form action="actions/login-action.php" method="post">
							<p>
								<label>Email</label>
								<input name="email" type="email" placeholder="email@example.com" required>
							</p>
							<p>
								<label>Password</label>
								<input name="password" type="password" required>
							</p>
							<p><a href="signup.php">New? Sign up Here!</a></p>
							<p>
								<input name="submit" type="submit" value="Log Me In!" class="button success">
							</p>
						</form>
					</div>
				</div>
				<?php include 'includes/footer.php';?>
			</section>
			<!-- close the off-canvas menu -->
			<a class="exit-off-canvas"></a>

		</div>
	</div>
	<!-- foundation javascript -->
	<script src="js/vendor/jquery.js"></script>
	<script src="js/foundation.min.js"></script>
	<script>
		$(document).foundation();
	</script>
</body>
</html>
