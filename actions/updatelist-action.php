<?php
	require_once '../vendor/autoload.php';
	require_once '../class/Spyc.class.php';
	$config = Spyc::YAMLLoad('../config/config.yml');

	if (isset($_POST['submit'])) {
		$dns = "mongodb://".$config['mongo']['user'].":".$config['mongo']['password']."@".$config['mongo']['host'].":".$config['mongo']['port']."/".$config['mongo']['database'];
		$bulk = new MongoDB\Driver\BulkWrite;
		$manager = new MongoDB\Driver\Manager($dns);
		$wc = new MongoDB\Driver\WriteConcern(
			MongoDB\Driver\WriteConcern::MAJORITY,
			1000
		);

		$filter = ["ID" => $_POST['id']];
		$update = ['$set' => ["Name" => urlencode($_POST['name']), "Updated_at" => new MongoDate()]];

		$options = ["limit" => 1, "upsert" => false];

		$bulk = new MongoDB\Driver\BulkWrite;
		$bulk->update($filter, $update, $options);
		$result = $manager->executeBulkWrite("blackdog.lists", $bulk, $wc);

		setcookie("alert_message", "List has been updated", time()+5,"/");
	} else {
		setcookie("error_message", "There was an error when updating your list. Please try again.", time()+5,"/");
	}
	header("Location: ../index.php");
?>