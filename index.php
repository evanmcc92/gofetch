<?php
// ini_set('display_errors', 1);
// 	ini_set('display_startup_errors', 1);
// 	error_reporting(E_ALL);	// Turn on all errors, warnings and notices for easier debugging
	
	require_once 'class/AmazonECS.class.php';
	require_once 'class/Spyc.class.php';
	require_once 'vendor/autoload.php';
	$config = Spyc::YAMLLoad('config/config.yml');
	
	if (isset($_COOKIE['user_email'])) { 
		
		$similarItems = array();
		$myitemtitle = true;
		$dns = "mongodb://".$config['mongo']['user'].":".$config['mongo']['password']."@".$config['mongo']['host'].":".$config['mongo']['port']."/".$config['mongo']['database'];
		$client = new MongoDB\Client($dns);
		$usercollection = $client->blackdog->users;
		$listcollection = $client->blackdog->lists;
		$itemcollection = $client->blackdog->items;

		$myitem = $itemcollection->find(['UserID'=>$_COOKIE['user_id']], ['limit' => 1])->toArray();
		if (count($myitem) == 0) {
			$myitem = $itemcollection->find([], ['limit' => 1])->toArray();
			$myitemtitle = false;
		} else {
			$myitemtitle = true;
		}
		$myitem = json_decode(json_encode($myitem),1);
		$myitem = $myitem[0];

		$select = $listcollection->find(['UserID'=>$_COOKIE['user_id']])->toArray();
		if (count($select) > 0) {
			// echo "<pre>";
		    foreach($select as $row) {
		    	$decodedrow = json_decode(json_encode($row),1);
				$lists[$decodedrow['ID']] = $decodedrow;
				$itemcount = $itemcollection->count(['UserID'=>$_COOKIE['user_id'], "ListID"=>$decodedrow['ID']]);
				$lists[$decodedrow['ID']]['cnt'] = $itemcount;
			}
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
	<meta name="description" content="Fetch takes a look at prices from around the web for your item and comes up with what we think is the best market value.">
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
	<title>Fetch</title>
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<link rel="stylesheet" href="css/foundation.css">
	<script src="js/vendor/jquery.js"></script>
	<script src="js/vendor/modernizr.js"></script>

	<meta property="og:title" content="Fetch" />
	<meta property="og:type" content="website" />
	<meta property="og:image" content="https://gofetch.tk/img/dog-badge-large.png" />
	<meta property="og:url" content="https://gofetch.tk/" />
	<meta property="og:site_name" content="Fetch" />
	<meta property="og:description" content="Fetch takes a look at prices from around the web for your item and comes up with what we think is the best market value" />
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:site" content="@Go_Fetch_App">
	<meta name="twitter:creator" content="@evan_mcc92">
	<meta name="twitter:title" content="Fetch">
	<meta name="twitter:description" content="Fetch takes a look at prices from around the web for your item and comes up with what we think is the best market value">
	<meta name="twitter:image" content="https://gofetch.tk/img/dog-badge-large.png">
</head>
<body>

	<div class="off-canvas-wrap" data-offcanvas>
		<div class="inner-wrap">
			<?php require_once 'includes/nav.php'; ?>

			<section class="main-section">
				<?php if (isset($_COOKIE['user_email'])) { // if logged in ?>
					<div class="row" id="body">
						<div class="large-12 columns">
							<h2>My Lists</h2>
							<p><a href="#" data-reveal-id="createList">Create a new list</a></p>
							<?php
								if (count($lists) > 0) {
									$counter = 0;
									foreach ($lists as $id => $row) {
										$imgitems = $itemcollection->find(['UserID'=>$_COOKIE['user_id'],'ListID'=>$id], ['limit' => 4])->toArray();
										$img = "";
										if (count($imgitems) > 0) {
											$counter = 1;
											$img .= "<tr>";
										    foreach($imgitems as $imgrow) {
										    	$imgrow = json_decode(json_encode($imgrow),1);
												$img .= "
													<td style='background:url(".$imgrow['Image'].") no-repeat center;height: 200px;background-size: 100%;'></td>
												";
												if ($counter % 2 == 0) {
													$img .= "</tr><tr>";
												}
												if ($counter == 4) {
													break;
												}
												$counter++;
											}
											$img .= "</tr>";
										} else {
											$img = "
											<tr>
												<td></td>
											</tr>
											";
										}
										echo "
										<div class='large-4 columns listbox' id='".$id."'>
											<a href='list.php?id=".$id."'>
												<div style='min-height:100px;width:100%'>
													<table>
														$img
													</table>
												</div>
												<hr>
												<h3>".urldecode($row['Name'])."</h3>
											</a>
											<p>".$row['cnt']." Items</p>
											<!-- <p>Value: <span id='list".$id."'>Calculating List Value...</span></p> -->
										</div>";
									}
								} else {
									echo "<div class='large-12 columns listbox' id='NoLists'>
										<h3>You have no lists</h3>
										<p><a href='#' data-reveal-id='createList'>Create a new list here</a></p>
									</div>";
								}
							?>
							<div class="row">
								<div class="large-12 columns">
								<div class="large-6 columns indexbottomitems">
									<h4><?php echo ($myitemtitle === true) ? "Items You Might Like" : "Items Other Users Like"; ?></h4>
									<div id="relatedItems"><span id="loading"></span></div>
									<script type="text/javascript">
										$(document).ready(function(){
											$.get(
												"ajax/relateditem.ajax.php",
												{iid: '<?php echo $myitem['ASIN']; ?>', iname:'<?php echo $myitem['Name']; ?>'},
												function( data ) {
													$('#relatedItems').html(data);
												}
											).fail(function() {
												$('#relatedItems').html("<p>There were no related items...</p>");
											});
										});
									</script>
								</div>
								<div class="large-6 columns indexbottomitems">
									<h4>Popular Items</h4>
									<div id="popularItems"><span id="loading"></span></div>
									<script type="text/javascript">
										$(document).ready(function(){
											$.get(
												"ajax/popularitem.ajax.php",
												function( data ) {
													$('#popularItems').html(data);
												}
											).fail(function() {
												$('#popularItems').html("<p>There were no popular items...</p>");
											});
										});
									</script>
								</div>
								</div>
							</div>
							<div id="adsense-searchbar" style="margin: 0 auto;height:100px;">
								<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
								<!-- search-bar -->
								<ins class="adsbygoogle"
									style="display:block;width:100%;height:100%;"
									data-ad-client="ca-pub-7935302944941905"
									data-ad-slot="6562185071"
									data-ad-format="auto"></ins>
								<script>
									(adsbygoogle = window.adsbygoogle || []).push({});
								</script>
							</div>
						</div>
					</div>
				<?php } else { ?>
					<div class="row">
						<div class="large-12 columns">
							<div class="indexdiv">
								<!-- <h1>Fetch</h1> -->
								<h1><img src="img/dog.png" alt="Fetch's logo" title="Fetch" style="width:250px;height:auto"></h1>
							</div>
						</div>
					</div>
					<div class="indexdiv orange">
						<h2>Fetch Values Your Stuff</h2>
						<p>
							Fetch takes a look at prices from around the web for your item and comes up with what we think is the best market value. 
							You can use this number to make sure your not getting ripped off, see how valuable your stuff is, or really anything else.
						</p>
					</div>
					<div class="indexdiv">
						<h2>Fetch Organizes Your Stuff</h2>
						<p>
							By <a href='signup.php'>signing up</a> with Fetch and using our lists, you can keep track of all your stuff with ease. 
							Fetch will tell you how much your list and all the items in it are worth.
						</p>
					</div>
					<div class="indexdiv orange">
						<h2>Searching for Stuff on Fetch is a Breeze</h2>
						<p>
							If you are having trouble finding something, be sure play around with Fetch's advanced search options to narrow down your search by country and category. 
							Fetch's search results are powered by Amazon and eBay so we have access to all of their items.
						</p>
					</div>
				<?php } ?>
				<?php include 'includes/footer.php';?>
			</section>
			<!-- close the off-canvas menu -->
			<a class="exit-off-canvas"></a>
		</div>
	</div>
	<div id="createList" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
		<h2 id="modalTitle">Create a List</h2>
		<form action="actions/newlist-action.php" method="post">
			<p>
				<label>List Name</label>
				<input name="name" type="text" required>
			</p>
			<p>
				<input name="submit" type="submit" value="Create List!" class="button success">
			</p>
		</form>
		<a class="close-reveal-modal" aria-label="Close">&#215;</a>
	</div>
	<!-- foundation javascript -->
	<script src="js/foundation.min.js"></script>
	<script>
		$(document).foundation({
			orbit: {
				pause_on_hover: false, // Pauses on the current slide while hovering
				resume_on_mouseout: false, // If pause on hover is set to true, this setting resumes playback after mousing out of slide
				animation_speed:500,
				slide_number:false,
				navigation_arrows:true
			}
		});
	</script>
</body>
</html>
