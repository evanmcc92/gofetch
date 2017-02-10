<?php
	require_once 'vendor/autoload.php';
	require_once 'class/Spyc.class.php';
	if (isset($_COOKIE['user_email'])) {
		$config = Spyc::YAMLLoad('config/config.yml');
		$dns = "mongodb://".$config['mongo']['user'].":".$config['mongo']['password']."@".$config['mongo']['host'].":".$config['mongo']['port']."/".$config['mongo']['database'];
		
		$client = new MongoDB\Client($dns);
		$collection = $client->blackdog->users;
		$row = $collection->find(['ID'=>$_COOKIE['user_id']])->toArray();

		if (count($row) == 0) {
			header("Location: index.php");
		}
		$row = $row[0];
	} else {
		header("Location: index.php");
	}	

?>
<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en" >

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="mobile-web-app-capable" content="yes">
	<title>My Account | Fetch</title>
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<link rel="stylesheet" href="css/foundation.css">
	<meta name="robots" content="NOINDEX, NOFOLLOW">
	<meta name="description" content="Change your Fetch account information here">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="js/vendor/modernizr.js"></script>
	<link rel="apple-touch-icon" sizes="57x57" href="img/favicon/apple-icon-57x57.png">
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
</head>
<body>
	<div class="off-canvas-wrap" data-offcanvas>
		<div class="inner-wrap">
			<?php require_once 'includes/nav.php'; ?>

			<section class="main-section">
				<div class="row" id="body">
					<div class="large-12 columns">
						<h2>My Account</h2>
						<form action="actions/updateaccount-action.php" method="post">
							<input name="id" type="hidden" value="<?php echo $row['ID'];?>">
							<p>
								<label>Email</label>
								<input name="email" type="email" value="<?php echo $row['Email'];?>" required>
							</p>
							<p>
								<label>New Password</label>
								<input name="new_password" type="password">
							</p>
							<p>
								<label>Confirm New Password</label>
								<input name="new_password_confirm" type="password">
							</p>
							<p>
								<label>Current Password <small>This is needed for all account changes</small></label>
								<input name="current_password" type="password" required>
							</p>
							<p>
								<input name="submit" type="submit" value="Update" class="button success">
							</p>
						</form>
						<hr>
						<form action="actions/deleteaccount-action.php" method="post">
							<h3>Dangerous Options</h3>
							<input name="id" type="hidden" value="<?php echo $row['ID'];?>">
							<input name="submit" type="submit" value="Delete My Account" id='deleteaccount' class="button alert">
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
		$('#deleteaccount').on('click',function(){
			// confirm that the user wants to delete account and that the action cannot be undone
			var r = confirm('Are you sure you want to delete your account? This cannot be undone');
			if (r == true) {
				var Nextlink = $(this).attr("href");
				window.location.href = Nextlink;
			} else {
				return false;
			}
		})
		$(document).foundation();
	</script>
</body>
</html>
