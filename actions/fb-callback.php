<?php
	ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once "../class/Facebook/autoload.php";
require_once '../class/Spyc.class.php';
require_once '../vendor/autoload.php';
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

$config = Spyc::YAMLLoad('../config/config.yml');
$fb = new Facebook\Facebook(array(
	'app_id' => $config['facebook']['api-id'],
	'app_secret' => $config['facebook']['api-secret'],
	'default_graph_version' => $config['facebook']['api-version'],
));

$helper = $fb->getRedirectLoginHelper();

try {
	$accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
	// When Graph returns an error
	$str = 'Graph returned an error: ' . $e->getMessage();
			setcookie("error_message", $str, time()+5,"/");
		header('Location: /');
	exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
	// When validation fails or other local issues
	$str = 'Facebook SDK returned an error: ' . $e->getMessage();
			setcookie("error_message", $str, time()+5,"/");
		header('Location: /');
	exit;
}

if (! isset($accessToken)) {
	if ($helper->getError()) {
		header('HTTP/1.0 401 Unauthorized');
		$str = "";
		$str .= "Error: " . $helper->getError() . "\n";
		$str .= "Error Code: " . $helper->getErrorCode() . "\n";
		$str .= "Error Reason: " . $helper->getErrorReason() . "\n";
		$str .= "Error Description: " . $helper->getErrorDescription() . "\n";
		setcookie("error_message", $str, time()+5,"/");
		header('Location: /');
	} else {
		header('HTTP/1.0 400 Bad Request');
		echo 'Bad request';
	}
	exit;
}

// Logged in
// echo '<h3>Access Token</h3>';
// print_r($accessToken->getValue());

// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();

// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken);
// echo '<h3>Metadata</h3>';

// print_r($tokenMetadata);
$expires_at = strtotime($tokenMetadata->getProperty('expires_at')->date);
$fbuserid = $tokenMetadata->getProperty('user_id');
// Validation (these will throw FacebookSDKException's when they fail)
$tokenMetadata->validateAppId($config['facebook']['api-id']);
// If you know the user ID this access token belongs to, you can validate it here
//$tokenMetadata->validateUserId('123');
$tokenMetadata->validateExpiration();

if (! $accessToken->isLongLived()) {
	// Exchanges a short-lived access token for a long-lived one
	try {
		$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
	} catch (Facebook\Exceptions\FacebookSDKException $e) {
		$str = "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
		setcookie("error_message", $str, time()+5,"/");
		header('Location: /');
		exit;
	}

	// echo '<h3>Long-lived</h3>';
	// var_dump($accessToken->getValue());
}
$_SESSION['fb_access_token'] = (string) $accessToken;

try {
	// Returns a `Facebook\FacebookResponse` object
	$response = $fb->get("/$fbuserid?fields=id,email", $_SESSION['fb_access_token']);
} catch(Facebook\Exceptions\FacebookResponseException $e) {
	$str = 'Facebook Graph returned an error: ' . $e->getMessage();
	setcookie("error_message", $str, time()+5,"/");
	header('Location: /');
	exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
	$str = 'Facebook SDK returned an error: ' . $e->getMessage();
	setcookie("error_message", $str, time()+5,"/");
	header('Location: /');
	exit;
}

$user = $response->getGraphUser();

$email = $user['email'];

$dns = "mongodb://".$config['mongo']['user'].":".$config['mongo']['password']."@".$config['mongo']['host'].":".$config['mongo']['port']."/".$config['mongo']['database'];
$client = new MongoDB\Client($dns);
$collection = $client->blackdog->users;
$result = $collection->find(['Email'=>$email])->toArray();
if (count($result) == 0) {
	$uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, $email)->toString();
	$post = array(
		'ID' => $uuid,
		'Email' => $email,
		'PasswordHash' => md5($email),
		'Source' => "facebook",
		'Created_at' => new MongoDate(), 
		'Updated_at' => new MongoDate(),
	);
	$collection->insertOne($post);
} else {
	$uuid = $result[0]['ID'];
}

setcookie("alert_message", $str, time()+5,"/");
setcookie("user", true, $expires_at,"/");
setcookie("user_email", $email, $expires_at,"/");
setcookie("user_id", $uuid, $expires_at,"/");
// echo "$str";

// User is logged in with a long-lived access token.
// You can redirect them to a members-only page.
header('Location: /');