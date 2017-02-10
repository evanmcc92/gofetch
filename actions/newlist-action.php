<?php

require_once '../class/Spyc.class.php';
require_once '../vendor/autoload.php';
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

$config = Spyc::YAMLLoad('../config/config.yml');

if (isset($_POST['submit'])) {
	$dns = "mongodb://".$config['mongo']['user'].":".$config['mongo']['password']."@".$config['mongo']['host'].":".$config['mongo']['port']."/".$config['mongo']['database'];
	$client = new MongoDB\Client($dns);
	$collection = $client->blackdog->lists;
	$result = $collection->find(['email'=>$_POST['email'], 'password'=>md5($_POST['password'])])->toArray();
	$uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, $_POST['name'])->toString();
	$list = array(
		'ID' => $uuid,
		'UserID' => $_COOKIE['user_id'],
		'Name' => urlencode($_POST['name']),
		'Created_at' => new MongoDate(), 
		'Updated_at' => new MongoDate(),
	);
	$collection->insertOne($list);
	setcookie("alert_message", "'".$_POST['name']."' was created", time()+5,"/");
}
header("Location: ../index.php");

?>