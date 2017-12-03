<?php
require_once "vendor/autoload.php";

use SentimentAnalysis\Config;
use SentimentAnalysis\EmotionDetector;
use SentimentAnalysis\Tweets;

function performAnalysis($text, $bufferSize = Config::WORD_BUFFER_SIZE, $wordOffset = Config::WORD_OFFSET_SIZE){
	$e = new EmotionDetector([
		"lexicon"=>Config::PATH_TO_LEXICON_FILE,
		"categories"=>Config::PATH_TO_CATEGORIES_FILE
	]);

	$text = Config::cleanText($text);

	$result = $e->run($text, $bufferSize, $wordOffset);

	return $result;
}

function loadTweets($user, $dates){
	$result = Tweets::load($user, $dates);

	return $result;
}

function getTweetUsers(){
	return Tweets::getUsers();
}

function processTweets($tweets){
	$analysis = [];

	$final = [];

	foreach ($tweets as $tweet) {
		$result = performAnalysis($tweet["text"]);
		$result = $result["emotion"][0];
		foreach ($result as $r=>$v){
			if (!isset($analysis[$r])){
				$analysis[$r] = [];
			}

			$analysis[$r][] = $v;
		}
	}

	foreach ($analysis as $key => $value) {
		$avg = (array_sum($value) / count($value)) * 100;
		$final[$key] = round($avg, 2);
	}

	return $final;
}

function analyzeTweets($user, $dates){
	$tweets = loadTweets($user, $dates);

	return processTweets($tweets);
}

function downloadTweets($user, $count = 20){
	$tweets = Tweets::downloadTweets($count, $user);
	
	return processTweets($tweets);
}