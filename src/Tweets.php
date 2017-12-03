<?php declare (strict_types=1);

/**
 * @license MIT
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 *
 * This file is part of the Tweet Emotion Detector project, please read the associated
 * license document to learn more
 */

namespace SentimentAnalysis;

/**
 * Class Tweets
 *
 * @since v0.0.1 29/11/2017 20:21
 */

class Tweets {

	private static $cb = null;

	public static function getTwitterInstance(){
		if (is_null(self::$cb)){
			Codebird::setConsumerKey(Config::TWEET_CONSUMER_KEY, Config::TWEET_CONSUMER_SECRET);

			self::$cb = Codebird::getInstance();

			self::$cb->setToken(Config::TWEET_TOKEN, Config::TWEET_TOKEN_SECRET);
		}

		return self::$cb;
	}

	public static function save(array $tweets){
		$query = [];
		foreach ($tweets as $tweet){
			$user = $tweet["user"];
			$text = Config::cleanText($tweet["text"]);
			$time = $tweet["time"];

			$query[] = "('$user', '$text', '$time')";
		}

		$query = "INSERT INTO tweets (user, text, time) VALUES ".implode(",", $query);

		$conn = SQLiteConnection::getConnection();

		return $conn->exec($query);
	}

	public static function load(string $user, array $dates, int $totalTweets = 100){
		$date0 = (new \DateTime($dates[0]))->format('Y-m-d');
		$date1 = (new \DateTime($dates[1]))->format('Y-m-d');

		$query = "SELECT * FROM tweets WHERE user = '$user' AND time BETWEEN datetime('$date0') AND datetime('$date1') ORDER BY id ASC LIMIT $totalTweets";

		$conn = SQLiteConnection::getConnection();

		$result = $conn->query($query)->fetchAll(\PDO::FETCH_ASSOC);

		return $result;
	}

	public static function getUsers(){
		$query = "SELECT DISTINCT user FROM TWEETS";

		$conn = SQLiteConnection::getConnection();

		$result = $conn->query($query)->fetchAll(\PDO::FETCH_ASSOC);

		return $result;
	}

	public static function downloadTweets($count, $user){
		$cb = self::getTwitterInstance();
		$params = [
		  'screen_name'=> $user,
		  'language' => 'ar',
		  'count' => $count
		];

		$reply = (array) $cb->statuses_userTimeline($params);

		$tweets = [];

		foreach ($reply as $key => $value) {
			if (property_exists($value, "text")){
			    $tweets[] = ["text"=>$value->text];
			}
		}
		
		return $tweets;
	}
}