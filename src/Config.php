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
 * Class Config
 *
 * @since v0.0.1 29/11/2017 20:21
 */

class Config {
	
	const PATH_TO_SQLITE_FILE = "bin/tweets.db";
	const PATH_TO_TWEETS_JSON_FILE = "bin/tweets.json";
	const PATH_TO_LEXICON_FILE = "bin/arabic-lexicons.csv";
	const PATH_TO_CATEGORIES_FILE = "bin/categories.json";

	const TWEET_CONSUMER_KEY = "";
	const TWEET_CONSUMER_SECRET = "";
	const TWEET_TOKEN = "";
	const TWEET_TOKEN_SECRET = "";

	const WORD_BUFFER_SIZE = 400;
	const WORD_OFFSET_SIZE = 200;

	const PUNCTUATIONS = ["؟", ".", ",", "\"", "'", "#", "/", ":", ";", "%", "$", "(", ")"];

	public static function cleanText(string $text){
		foreach (SELF::PUNCTUATIONS as $dirt) {
			$text = str_replace($dirt, " ", $text);
		}

		return $text;
	}
}