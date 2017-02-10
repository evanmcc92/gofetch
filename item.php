<?php
	$http = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ?  "https://" :  "http://";
	$url = $http.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	setlocale(LC_MONETARY, 'en_US');
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);	// Turn on all errors, warnings and notices for easier debugging
	require_once 'class/getItemPrice.class.php';
	require_once 'vendor/autoload.php';
	require_once 'class/Spyc.class.php';
	$config = Spyc::YAMLLoad('config/config.yml');

	if (isset($_GET['AmazonASIN'])) {
		$itemDetails = new getItemPrice($config);
		$result = $itemDetails->getItemDetails($_GET['AmazonASIN']);
	} else {
		header("Location: index.php");
	}

	$similaritems = "<ul>";
	foreach ($result['SimilarItems'] as $key => $value) {
		$similaritems .= "<li><a href='item.php?AmazonASIN=".$value['ASIN']."'>".$value['Title']."</a></li>";
	}
	$similaritems .= "</ul>";

	$table = "<table>
	<tr>
		<th>View on Website</th>
		<th>Condition</th>
		<th>Price</th>
	</tr>";
	if ($result['ListPrice']) {
		$table.= '
		<tr>
			<td class="title"><a href="'.$result["DetailPageURL"].'" rel="nofollow" target="_blank">Amazon</a></td>
			<td class="title">List Price</td>
			<td class="price">$'.money_format('%i', $result['ListPriceAmount']*0.01).'</td>
		</tr>
		';
	}
	if ($result['LowestPriceNew']) {
		$table.= '
		<tr>
			<td class="title"><a href="'.$result["DetailPageURL"].'" rel="nofollow" target="_blank">Amazon</a></td>
			<td class="title">Lowest New Price</td>
			<td class="price">$'.money_format('%i', $result['LowestPriceNewAmount']*0.01).'</td>
		</tr>
		';
	}
	if ($result['LowestPriceUsed']) {
		$table.= '
		<tr>
			<td class="title"><a href="'.$result["DetailPageURL"].'" rel="nofollow" target="_blank">Amazon</a></td>
			<td class="title">Lowest Used Price</td>
			<td class="price">$'.money_format('%i', $result['LowestPriceUsedAmount']*0.01).'</td>
		</tr>
		';
	}
	if ($result['LowestPriceRefurbished']) {
		$table.= '
		<tr>
			<td class="title"><a href="'.$result["DetailPageURL"].'" rel="nofollow" target="_blank">Amazon</a></td>
			<td class="title">Lowest Refurbished Price</td>
			<td class="price">$'.money_format('%i', $result['LowestPriceRefurbishedAmount']*0.01).'</td>
		</tr>
		';
	}
	if ($result['LowestPriceCollectible']) {
		$table.= '
		<tr>
			<td class="title"><a href="'.$result["DetailPageURL"].'" rel="nofollow" target="_blank">Amazon</a></td>
			<td class="title">Lowest Collectible Price</td>
			<td class="price">$'.money_format('%i', $result['LowestPriceCollectibleAmount']*0.01).'</td>
		</tr>
		';
	}
	if (count($result['eBayResults']) > 0) {
		foreach ($result['eBayResults'] as $key) {
			if (!empty($key["Condition"]) && !empty($key['Price'])) {
				$table.= '
				<tr>
					<td class="title"><a href="'.$key["URL"].'" rel="nofollow" target="_blank">(eBay) '.$key['Name'].'</a></td>
					<td class="title">'.$key["Condition"].'</td>
					<td class="price">$'.money_format('%i', $key['Price']).'</td>
				</tr>
				';
			}
		}
	}
	$table .= "</table>";

	$avg = money_format('%i', $result['WeightedPrice']);

	if (isset($_COOKIE['user_id'])) {
		$pricetable = '
		<ul class="pricing-table">
			<li class="title">Valued At</li>
			<li class="price">$'.$avg.'</li>
			<li class="cta-button"><a class="button" href="#" data-reveal-id="myModal">Add to List</a></li>
			'.(($result['Description'] !== "") ? "<li class='description'>".$result['Description']."</li>" : '').'
			<li class="description"><p>Similar Items</p>'.$similaritems.'</li>
		</ul>';
	} else {
		$pricetable = '
		<ul class="pricing-table">
			<li class="title">Valued At</li>
			<li class="price">$'.$avg.'</li>
			'.(($result['Description'] !== "") ? "<li class='description'>".$result['Description']."</li>" : '').'
			<li class="description"><p>Similar Items</p>'.$similaritems.'</li>
		</ul>';
	}

	if (isset($result['ImageLarge'])) {
		$headerimg = $result['ImageLarge'];
	} elseif (isset($result['ImageMedium'])) {
		$headerimg = $result['ImageMedium'];
	}elseif (isset($result['ImageSmall'])) {
		$headerimg = $result['ImageSmall'];
	}

	if (isset($headerimg)) {
		$header = '
		<div id="item-head" class="row">
			<div id="item-img" class="large-3 columns">
				<a href="'.$result["DetailPageURL"].'" target="_blank" rel="nofollow"><img src="'.$headerimg.'"></a>
			</div>
			<div id="item-header" class="large-9 columns">
				<h2><a href="'.$result["DetailPageURL"].'" target="_blank">'.$result['Title'].'</a></h2>
				<h4>'.$result['Artist'].'</h4>
				'.(!empty($result['Brand']) ? '<h4><small>Brand: </small>'.$result['Brand'].'</h4>' : "").'
				'.(!empty($result['ItemManufacturer']) ? '<h4><small>Manufacturer: </small>'.$result['ItemManufacturer'].'</h4>' : "").'
				'.(!empty($result['Binding']) ? '<h4><small>Category: </small>'.$result['Category'].' > '.$result['Binding'].'</h4>' : "").'
			</div>
		</div>
		<div class="row" style="margin-top:10px;" >
			'.$pricetable.'
		</div>
		';
	} else {
		$header = '
		<div id="item-head" class="row">
			<div id="item-header" class="small-11 small-centered columns">
				<h2><a href="'.$result["DetailPageURL"].'" target="_blank">'.$result['Title'].'</a></h2>
				<h4>'.$result['Artist'].'</h4>
				'.($result['Brand'] !== "" ? '<h4><small>Brand: </small>'.$result['Brand'].'</h4>' : "").'
				'.($result['ItemManufacturer'] !== "" ? '<h4><small>Manufacturer: </small>'.$result['ItemManufacturer'].'</h4>' : "").'
				'.($result['Binding'] !== "" ? '<h4><small>Category: </small>'.$result['Category'].' > '.$result['Binding'].'</h4>' : "").'
				'.$pricetable.'
			</div>
		</div>';
	}
	$description = !empty($result['Description']) ? addslashes(strip_tags(str_replace("<p>", " ", str_replace("</p>", " ", str_replace("<br>", " ", $result['Description']))))) : $result['Title'];

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
	<title><?php echo $result['Title']; ?> | Fetch</title>
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<link rel="stylesheet" href="css/foundation.css">
	<meta rel="canonical" content="item.php">
	<meta rel="description" content="<?php echo $description; ?>">
	<meta property="og:title" content="<?php echo $result['Title']; ?> | Fetch" />
	<meta property="og:type" content="website" />
	<meta property="og:image" content="<?php echo isset($headerimg) ? $headerimg : "https://gofetch.tk/img/dog-badge-large.png";?>" />
	<meta property="og:url" content="<?php echo $url; ?>" />
	<meta property="og:site_name" content="Fetch" />
	<meta property="og:description" content="<?php echo $description; ?>" />
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:site" content="@Go_Fetch_App">
	<meta name="twitter:creator" content="@evan_mcc92">
	<meta name="twitter:title" content="<?php echo $result['Title']; ?> | Fetch">
	<meta name="twitter:description" content="<?php echo $description; ?>">
	<meta name="twitter:image" content="<?php echo isset($headerimg) ? $headerimg : "https://gofetch.tk/img/dog-badge-large.png";?>">
	<script src="js/vendor/modernizr.js"></script>

</head>
<body>
	<div class="off-canvas-wrap" data-offcanvas>
		<div class="inner-wrap">
			<?php require_once 'includes/nav.php'; ?>

			<section class="main-section">
				<div class="row" id="body">
					<div class="large-12 columns">
						<?php echo $header; ?>
						<?php echo $table; ?>


					</div>
				</div>
				<?php include 'includes/footer.php';?>
				
			</section>
			<!-- close the off-canvas menu -->
			<a class="exit-off-canvas"></a>

		</div>
	</div>
	<!-- foundation javascript -->
	<div id="myModal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
		<h2 id="modalTitle">Select a List</h2>
		<p class="lead">Add '<?php echo $result['Title']; ?>' to one of your lists</p>
		<form method="post" action="actions/addtolist-action.php">
			<input type="hidden" name="user_id" value="<?php echo $_COOKIE['user_id'];?>">
			<input type="hidden" name="title" value="<?php echo $result['Title'];?>">
			<input type="hidden" name="imgurl" value="<?php echo $result['ImageLarge'];?>">
			<input type="hidden" name="asin" value="<?php echo $result['ASIN'];?>">
			<select name="list_id">
				<option selected="true" disabled="disabled">Select a List</option>
				<?php
					$dns = "mongodb://".$config['mongo']['user'].":".$config['mongo']['password']."@".$config['mongo']['host'].":".$config['mongo']['port']."/".$config['mongo']['database'];
					$client = new MongoDB\Client($dns);
					$listcollection = $client->blackdog->lists;
					$select = $listcollection->find(['UserID'=>$_COOKIE['user_id']])->toArray();
					if (count($select) > 0) {
					    foreach($select as $row) {
							echo "<option value='".$row['ID']."'>".urldecode($row['Name'])."</option>";
						}
					}
				?>
			</select>
			<input type="submit" name="submit" class="button" value="Add to the list!">
		</form>
		<a class="close-reveal-modal" aria-label="Close">&#215;</a>
	</div>
	<script src="js/vendor/jquery.js"></script>
	<script src="js/foundation.min.js"></script>
	<script>
		$(document).foundation();
	</script>
	<!-- Go to www.addthis.com/dashboard to customize your tools -->
	<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-54fe4e8f139031f1" async="async"></script>

</body>
</html>
