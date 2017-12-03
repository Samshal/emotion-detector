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
 * Class SQLiteConnection
 *
 * @since v0.0.1 29/11/2017 20:21
 */
class SQLiteConnection {
    /**
     * PDO instance
     * @var type 
     */
    private static $pdo;
 
    /**
     * return an instance of the PDO object that connects to the SQLite database
     * @return \PDO
     */
    public static function getConnection() {
        if (self::$pdo == null) {
            self::$pdo = new \PDO("sqlite:" . Config::PATH_TO_SQLITE_FILE);
        }

        return self::$pdo;
    }
}