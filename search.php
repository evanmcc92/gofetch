<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en" >

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="mobile-web-app-capable" content="yes">
	<meta rel="canonical" content="search.php">
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
	<title>Search for '<?php echo $_GET['search']; ?>' | Fetch</title>
	<meta name="description" content="Searching for '<?php echo $_GET['search']; ?>' on Fetch">

	<link rel="stylesheet" type="text/css" href="css/main.css">
	<link rel="stylesheet" href="css/foundation.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="js/vendor/modernizr.js"></script>
</head>
<body>
	<div class="off-canvas-wrap" data-offcanvas>
		<div class="inner-wrap">
			<?php require_once 'includes/nav.php'; ?>

			<section class="main-section">
				<div class="row" id="body">
					<div class="large-12 columns">
						<h2>Search for '<?php echo $_GET['search']; ?>'</h2>
						<div class="row" data-equalizer>
							<div class="large-10 column">
								<div id="search-results"><p>Results loading...</p></div>
								<script type="text/javascript">
									$.get("ajax/search.ajax.php", <?php echo json_encode($_GET);?>, function(data) {$("#search-results").html(data)})
									.fail(function() {
										$("#search-results").html("<p>Error loading search results.</p>");
									});
								</script>
							</div>
							<div class="large-2 column"  id="adsense-searchpage" data-equalizer-watch>
								<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
								<!-- search-page-left -->
								<ins class="adsbygoogle"
									style="display:block;height:100%;width:100%;"
									data-ad-client="ca-pub-7935302944941905"
									data-ad-slot="8038918274"
									data-ad-format="auto"></ins>
								<script>
									(adsbygoogle = window.adsbygoogle || []).push({});
								</script>
							</div>
						</div>
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
