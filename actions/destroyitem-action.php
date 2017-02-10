<?php
	require_once '../class/Spyc.class.php';
	require_once '../vendor/autoload.php';
	$config = Spyc::YAMLLoad('../config/config.yml');
	echo "<pre>";
	if (isset($_GET['id'])) {
		$dns = "mongodb://".$config['mongo']['user'].":".$config['mongo']['password']."@".$config['mongo']['host'].":".$config['mongo']['port']."/".$config['mongo']['database'];
		$bulk = new MongoDB\Driver\BulkWrite;
		$manager = new MongoDB\Driver\Manager($dns);
		// Construct a write concern
		$wc = new MongoDB\Driver\WriteConcern(
			// Guarantee that writes are acknowledged by a majority of our nodes
			MongoDB\Driver\WriteConcern::MAJORITY,
			// But only wait 1000ms because we have an application to run!
			1000
		);

		$options = array("limit" => 1);
		$params = array('ID'=>$_GET['id'], "ListID"=>$_GET['list_id']);
		$bulk->delete($params, $options);
		$result = $manager->executeBulkWrite("blackdog.items", $bulk, $wc);

		setcookie("alert_message", "Item Removed", time()+5,"/");
		header("Location: ".(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "../index.php"));
	} else {
		header("Location: ../index.php");
	}

?>