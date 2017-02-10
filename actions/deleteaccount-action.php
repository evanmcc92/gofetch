<?php
	require_once '../vendor/autoload.php';
	require_once '../class/Spyc.class.php';
	$config = Spyc::YAMLLoad('../config/config.yml');

	if (isset($_POST['id'])) {

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
		$listparams = array('UserID'=>$_POST['id']);
		$itemparams = array('UserID'=>$_POST['id']);
		$userparams = array('ID'=>$_POST['id']);

		$bulk->delete($listparams, $options);
		$result = $manager->executeBulkWrite("blackdog.lists", $bulk, $wc);
		$bulk->delete($itemparams, $options);
		$result = $manager->executeBulkWrite("blackdog.items", $bulk, $wc);
		$bulk->delete($userparams, $options);
		$result = $manager->executeBulkWrite("blackdog.users", $bulk, $wc);

		setcookie("alert_message", "Your account has been successfully deleted", time()+5,"/");
		setcookie("user", false, time()-(60*60*24*7),"/");
		setcookie("user_email", "", time()-60*60*24*7,"/");
		setcookie("user_id", "", time()-60*60*24*7,"/");
		header("Location: ".(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "../index.php"));
	} else {
		header("Location: ../index.php");
	}

?>