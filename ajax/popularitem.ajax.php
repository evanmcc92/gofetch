<?php

	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);	// Turn on all errors, warnings and notices for easier debugging
	require_once '../vendor/autoload.php';
	require_once '../class/Spyc.class.php';
	$config = Spyc::YAMLLoad('../config/config.yml');

	$dns = "mongodb://".$config['mongo']['user'].":".$config['mongo']['password']."@".$config['mongo']['host'].":".$config['mongo']['port']."/".$config['mongo']['database'];
	$client = new MongoDB\Client($dns);
	$itemcollection = $client->blackdog->items;

	$count = array('$group'=>array('_id'=>'$ASIN', 'count'=>array('$sum'=>1))); //count distinct 
	$sort = array('$sort'=>array('count'=>-1,'Name'=>-1));
	$limit = array('$limit'=>5);   
	$result = $itemcollection->aggregate(array($count,$sort,$limit));
	echo "<ol>";
	foreach ($result->toArray() as $key => $value) {
		$item = $itemcollection->find(['ASIN'=> $value['_id']], ['limit' => 1])->toArray();
		echo "<li><a href='item.php?AmazonASIN=".$value['_id']."'>".urldecode($item[0]['Name'])."</a></li>";
	}
	echo "</ol>";
?>