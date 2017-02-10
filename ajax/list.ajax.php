<?php
	ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	require_once '../vendor/autoload.php';
	require_once '../class/Spyc.class.php';
	require_once '../class/getItemPrice.class.php';
	if (isset($_GET['user_id']) && isset($_GET['list_id'])) {
		$config = Spyc::YAMLLoad('../config/config.yml');
		$dns = "mongodb://".$config['mongo']['user'].":".$config['mongo']['password']."@".$config['mongo']['host'].":".$config['mongo']['port']."/".$config['mongo']['database'];
		$itemDetails = new getItemPrice($config);
		$client = new MongoDB\Client($dns);
		$collection = $client->blackdog->items;
		$select = $collection->find(['UserID'=>$_COOKIE['user_id'], 'ListID'=>$_GET['list_id']])->toArray();
		$total = 0;
		if (count($select) > 0) {
			foreach ($select as $listrow) {
				$rowDetails = $itemDetails->getItemDetails($listrow['ASIN']);
				$total += $rowDetails['WeightedPrice'];
				$return[$listrow['ASIN']] = '$' . number_format($rowDetails['WeightedPrice'], 2);
			}
		}
		$return['Total'] = '$' . number_format($total, 2);
		echo json_encode($return);
	}
?>