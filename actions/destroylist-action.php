<?php
	require_once '../vendor/autoload.php';
	require_once '../class/Spyc.class.php';
	$config = Spyc::YAMLLoad('../config/config.yml');

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
		$listparams = array('ID'=>$_GET['id']);
		$itemparams = array('ListID'=>$_GET['id']);

		$bulk->delete($listparams, $options);
		$result = $manager->executeBulkWrite("blackdog.lists", $bulk, $wc);

		$bulk->delete($itemparams, $options);
		$result = $manager->executeBulkWrite("blackdog.items", $bulk, $wc);

		setcookie("alert_message", "Your list was deleted", time()+5,"/");
		header("Location: ".(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "../index.php"));
	} else {
		header("Location: ../index.php");
	}

?>