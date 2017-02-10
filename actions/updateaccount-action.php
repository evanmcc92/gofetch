<?php
	require_once '../vendor/autoload.php';
	require_once '../class/Spyc.class.php';
	$config = Spyc::YAMLLoad('../config/config.yml');

	if (isset($_POST['submit'])) {
		$dns = "mongodb://".$config['mongo']['user'].":".$config['mongo']['password']."@".$config['mongo']['host'].":".$config['mongo']['port']."/".$config['mongo']['database'];
		
		$client = new MongoDB\Client($dns);
		$collection = $client->blackdog->users;
		$user = $collection->find(['ID'=>$_POST['id'], 'PasswordHash'=>md5($_POST['current_password'])])->toArray();

		if (count($user) > 0) {
			$param['Updated_at'] = new MongoDate();
			if (($_POST['new_password'] === $_POST['new_password_confirm']) && $_POST['new_password'] !== "") {
				$param["PasswordHash"] = md5($_POST['new_password']);
			}
			if ($_POST['email'] !== "") {
				$param['Email'] = $_POST['email'];
			}
			if (count($param) > 1) {
				$bulk = new MongoDB\Driver\BulkWrite;
				$manager = new MongoDB\Driver\Manager($dns);
				$wc = new MongoDB\Driver\WriteConcern(
					MongoDB\Driver\WriteConcern::MAJORITY,
					1000
				);
				
				$filter = ["ID" => $_POST['id']];
				$update = ['$set' => $param];

				$options = ["limit" => 1, "upsert" => false];

				$bulk = new MongoDB\Driver\BulkWrite;
				$bulk->update($filter, $update, $options);
				$result = $manager->executeBulkWrite("blackdog.users", $bulk, $wc);
				setcookie("alert_message", "Account has been updated", time()+5,"/");
			}
		} else {
			setcookie("error_message", "There was an error when updating your account. Please try again.", time()+5,"/");
		}
	} else {
		setcookie("error_message", "There was an error when updating your account. Please try again.", time()+5,"/");
	}
	header("Location: ../account.php");
?>