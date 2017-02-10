<?php
	require_once '../class/Spyc.class.php';
	require_once '../vendor/autoload.php';
	use Ramsey\Uuid\Uuid;
	use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

	$config = Spyc::YAMLLoad('../config/config.yml');

	if (isset($_POST['submit'])) {
		if (isset($_POST['list_id']) && isset($_POST['asin'])) {
			$dns = "mongodb://".$config['mongo']['user'].":".$config['mongo']['password']."@".$config['mongo']['host'].":".$config['mongo']['port']."/".$config['mongo']['database'];
			$client = new MongoDB\Client($dns);
			$collection = $client->blackdog->items;
			$uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, $_POST['asin'])->toString();
			$itemarray = array(
				'ID' => $uuid,
				'UserID' => $_POST['user_id'],
				'ListID' => $_POST['list_id'],
				'ASIN' => $_POST['asin'],
				'Name' => urlencode($_POST['title']),
				'Image' => addslashes($_POST['imgurl']),
				'Created_at' => new MongoDate(), 
				'Updated_at' => new MongoDate(),
			);
			$collection->insertOne($itemarray);

			$collection = $client->blackdog->lists;
			$row = $collection->find(['UserID'=>$_COOKIE['user_id'], "ID"=>$_POST['list_id']])->toArray();
			
			setcookie("alert_message", "'".$_POST['title']."' was added to '".urldecode($row[0]['Name'])."'", time()+5,"/");
			header("Location: ../list.php?id=".$_POST['list_id']);
		} else {
			setcookie("error_message", "There was an error adding '".$_POST['title']."' to '".urldecode($row['Name'])."'", time()+5,"/");
			header("Location: ../item.php?AmazonASIN=".$_POST['asin']);
		}
	} else {
		header("Location: ../index.php");
	}
?>