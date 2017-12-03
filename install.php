<?php
require_once "vendor/autoload.php";

use SentimentAnalysis\SQLiteConnection as DB;
use SentimentAnalysis\Tweets;
use SentimentAnalysis\Config;

$tableQuery = "CREATE TABLE tweets (`id` INTEGER PRIMARY KEY AUTOINCREMENT, `user` TEXT NOT NULL, `text` TEXT NOT NULL, `time` TEXT NOT NULL)";

(DB::getConnection())->exec($tableQuery);

$fi = json_decode(file_get_contents(Config::PATH_TO_TWEETS_JSON_FILE), true);

$tweets = [];
$tweetGroup = [];
echo "------------- <br/>Processing Tweets <br/>--------------------<br/>";

foreach ($fi as $key => $value) {
	if (count($tweets) > 200){
		$tweetGroup[] = $tweets;
		$tweets = [];
	}
	$time = (new DateTime($value["time"]))->format('Y-m-d h:i:s');
	$tweets[] = [
		"user"=>$value["user"],
		"time"=>$time,
		"text"=>$value["tweet"]
	];
}

$results = [];

foreach ($tweetGroup as $count=>$group) {
	$result[] = Tweets::save($group);

	echo "DONE WITH INDEX ".($count+1)." OF ".count($tweetGroup)."<br/>";
}
echo "------------<br/>DONE PROCESSING ".count($fi)." tweets";