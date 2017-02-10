<?php
	require_once '../class/Spyc.class.php';
	require_once '../vendor/autoload.php';
	use Ramsey\Uuid\Uuid;
	use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
	$config = Spyc::YAMLLoad('../config/config.yml');

	if (isset($_POST['submit'])) {
		$dns = "mongodb://".$config['mongo']['user'].":".$config['mongo']['password']."@".$config['mongo']['host'].":".$config['mongo']['port']."/".$config['mongo']['database'];
		$client = new MongoDB\Client($dns);
		$collection = $client->blackdog->users;
		$result = $collection->find(['Email'=>$_POST['email'], 'PasswordHash'=>md5($_POST['password'])])->toArray();
		if (count($result) > 0) {
			$id = $result[0]['ID'];
			$str = "Welcome ".$_POST['email']."!";
			setcookie("alert_message", $str, time()+5,"/");
			setcookie("user", true, time()+60*60*24*7,"/");
			setcookie("user_email", $_POST['email'], time()+60*60*24*7,"/");
			setcookie("user_id", $id, time()+60*60*24*7,"/");
			echo "$str";
			header("Location: ../index.php");
		} else {
			$str = "Error: Problem with email or password";
			setcookie("error_message", $str, time()+5,"/");
			echo "$str";
			header("Location: ../login.php");
		}
	} else {
		header("Location: ../login.php");
	}
?>