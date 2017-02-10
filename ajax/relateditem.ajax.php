<?php

	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);	// Turn on all errors, warnings and notices for easier debugging
	require_once '../vendor/autoload.php';
	require_once '../class/getItemPrice.class.php';
	require_once '../class/Spyc.class.php';
	$config = Spyc::YAMLLoad('../config/config.yml');

	if (isset($_GET['iid'])) {
		echo "<h6>Based off of '<i><a href=\"item.php?AmazonASIN=".$_GET['iid']."\">".urldecode($_GET['iname'])."</a></i>'</h6>";
		$dns = "mongodb://".$config['mongo']['user'].":".$config['mongo']['password']."@".$config['mongo']['host'].":".$config['mongo']['port']."/".$config['mongo']['database'];
		$client = new MongoDB\Client($dns);
		$itemcollection = $client->blackdog->items;

		$itemDetails = new getItemPrice($config);
		$result = $itemDetails->getItemDetails($_GET['iid']);
		echo "<ul>";
		foreach ($result['SimilarItems'] as $key => $value) {
			echo "<li><a href='item.php?AmazonASIN=".$value['ASIN']."'>".$value['Title']."</a></li>";
		}
		echo "</ul>";
	}
?>