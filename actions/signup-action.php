<?php
	require_once '../vendor/autoload.php';
	require_once '../class/Spyc.class.php';
	use Ramsey\Uuid\Uuid;
	use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
	$config = Spyc::YAMLLoad('../config/config.yml');

	if (isset($_POST['submit'])) {
		if (strlen($_POST['password']) >= 8 && $_POST['password'] === $_POST['confirm_password']) {
			$dns = "mongodb://".$config['mongo']['user'].":".$config['mongo']['password']."@".$config['mongo']['host'].":".$config['mongo']['port']."/".$config['mongo']['database'];
			$client = new MongoDB\Client($dns);
			$collection = $client->blackdog->users;
			$result = $collection->find(['email'=>$_POST['email'], 'password'=>md5($_POST['password'])])->toArray();

			if (count($result) > 0) {
				$str = "Error: Email '".$_POST['email']."' is already being used";
				setcookie("error_message", $str, time()+60,"/");
				echo "$str";
				header("Location: ../signup.php");
			} else {
				$str = "Welcome ".$_POST['email']."!";
				$uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, $_POST['email'])->toString();
				$userarray = array(
					'ID' => $uuid,
					'Email' => $_POST['email'],
					'PasswordHash' => md5($_POST['password']),
					'Source' => "email",
					'Created_at' => new MongoDate(), 
					'Updated_at' => new MongoDate(),
				);
				$collection->insertOne($userarray);
				setcookie("alert_message", $str, time()+5,"/");
				setcookie("user", true, time()+60*60*24*7,"/");
				setcookie("user_email", $_POST['email'], time()+60*60*24*7,"/");
				setcookie("user_id", $uuid, time()+60*60*24*7,"/");
				echo "$str";
				header("Location: ../index.php");
			}
		} else {
			$str = (strlen($_POST['password']) < 8) ? "Error: Password is not 8 or more characters" : "Error: Password does not match";
			setcookie("error_message", $str, time()+5,"/");
			echo "$str";
			header("Location: ../signup.php");
		}
	} else {
		header("Location: ../signup.php");
	}
?>