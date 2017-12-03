<?php
require_once "vendor/autoload.php";
include_once("./functions.php");

error_reporting(0);

ini_set("memory_limit", "5024M");

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Cache-Control, X-Requested-With, Authorization');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Content-Type: application/json');

$task = $_POST["task"] ?? 'perform-analysis';

switch (strtolower($task)) {
	case 'perform-analysis':
		$text = $_POST["text"] ?? "";
		$result = performAnalysis($text);
		break;

	case 'get-users':
		$result = getTweetUsers();
		break;

	case 'analyze-tweets':
		$user = $_POST["user"] ?? "";
		$dates = $_POST["dates"];

		$result = analyzeTweets($user, $dates);
		break;

	case 'process-live-tweets':
		$user = $_POST["user"] ?? "";
		$count = $_POST["count"];

		$result = downloadTweets($user, $count);
		break;
	
	default:
		$result = [];
		break;
}

$result = json_encode($result);
echo $result;