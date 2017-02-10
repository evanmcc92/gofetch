<?php
	setlocale(LC_MONETARY, 'en_US');
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


	require_once 'class/Spyc.class.php';
	require_once 'vendor/autoload.php';
	require_once 'class/getItemPrice.class.php';
	$config = Spyc::YAMLLoad('config/config.yml');
	
	if (!isset($_COOKIE['user_id']) && !isset($_GET['id'])) {
		header("Location: index.php");
	} else {
		$dns = "mongodb://".$config['mongo']['user'].":".$config['mongo']['password']."@".$config['mongo']['host'].":".$config['mongo']['port']."/".$config['mongo']['database'];
		$client = new MongoDB\Client($dns);
		$lists = $client->blackdog->lists;
		$items = $client->blackdog->items;

		$row = $lists->find(['UserID'=>$_COOKIE['user_id'], 'ID'=>$_GET['id']])->toArray();
		if (count($row) > 0) {
			$row = $row[0];
			$listitems = $items->find(['UserID'=>$_COOKIE['user_id'], 'ListID'=>$_GET['id']])->toArray();
			if (count($listitems) > 0) {
			    foreach($listitems as $listrow) {
					$listrows[] = $listrow;
					$asins[$listrow['ASIN']] = urldecode($listrow['Name']);
				}
			}
		} else {
			header("Location: index.php");
		}
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
	<meta name="description" content="<?php echo urldecode($row['Name']);?> is a list of some really cool stuff on Fetch">
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
	<title>List '<?php echo urldecode($row['Name']);?>' | Fetch</title>
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<link rel="stylesheet" href="css/foundation.css">
	<script src="js/vendor/modernizr.js"></script>
	<meta name="robots" content="NOINDEX, NOFOLLOW">
	<script src="js/vendor/jquery.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$.get(
				"ajax/list.ajax.php",
				{user_id: '<?php echo $_COOKIE['user_id']; ?>', list_id: '<?php echo $_GET['id']; ?>'},
				function( data ) {
					var obj = $.parseJSON(data);
					for (var prop in obj) {
						$('#'+prop+'').html(obj[prop]);
					}
				}
			).fail(function() {
				for (var i = json.length; i <= 0; i++) {
					$('#'+json[i]+'').html('Unable to get a value =(');
				};
			});
		});
	</script>
</head>
<body>
	<div class="off-canvas-wrap" data-offcanvas>
		<div class="inner-wrap">
			<?php require_once 'includes/nav.php'; ?>

			<section class="main-section">
				<div class="row" id="body">
					<div class="large-12 columns">
						<h2>List '<?php echo urldecode($row['Name']);?>'</h2>
						<p>Total Worth: <span id='Total'>Loading...</span></p>
						<p><a href='index.php' class='button'>All Lists</a></p>
						<p><a href='#' data-reveal-id='updateList-<?php echo $row['ID']; ?>' class='button'>Rename List</a> <a href='actions/destroylist-action.php?id=<?php echo $row['ID']; ?>' class='button alert'>Delete List</a></p>
						<hr>
						<div class="large-12 columns">
							<?php
								if (count($listrows) > 0) {
									$count = 1;
									foreach ($listrows as $key => $listrow) {
										echo "<div class='large-4 columns listitems' id='".$listrow['ID']."'>
											<a href='item.php?AmazonASIN=".$listrow['ASIN']."'>
												<div class='listitemimg'>
													<img src='".$listrow['Image']."'>
												</div>
												<hr>
												<p>".urldecode($listrow['Name'])."</p>
											</a>
											<p>
												<span id='".$listrow['ASIN']."' class='values'>Getting Values...</span>
												<br>
												<a href='actions/destroyitem-action.php?id=".$listrow['ID']."&list_id=".$row['ID']."'>Remove</a>
											</p>
										</div>
										";
										if ($count % 3 == 0) {
											echo "</div><div class=\"large-12 columns\">";
										}
										$count++;
									}
								} else {
									echo  "
									<div class='large-3 columns listitems''>
										<p>Sorry but there are no items here</p>
									</div>";
								}
							?>
						</div>
						<div class="row" style="margin-bottom:5%">
						</div>
						<div id="updateList-<?php echo $row['ID']; ?>" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
							<h2 id="modalTitle">Update List</h2>
							<form action="actions/updatelist-action.php" method="post">
								<input name="id" type="hidden" value="<?php echo $row['ID']; ?>">
								<p>
									<label>List Name</label>
									<input name="name" type="text" value="<?php echo urldecode($row['Name']); ?>" required>
								</p>
								<p>
									<input name="submit" type="submit" value="Update List!" class="button success">
								</p>
							</form>
							<a class="close-reveal-modal" aria-label="Close">&#215;</a>
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
	<script src="js/foundation.min.js"></script>
	<script>
		$(document).foundation();
		
	</script>
</body>
</html>
