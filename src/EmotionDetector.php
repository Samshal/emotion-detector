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
 * Class EmotionDetector
 *
 * @since v0.0.1 29/11/2017 20:21
 */

class EmotionDetector {

	private $lexiconFile = null;
	private $categoriesFile = null;


	private $categoryHeaders = [];
	private $categories = [];
	private $vocabulary = [];
	private $words = [];
	private $data = [];
	private $analyzedData = [];
	private $maxValues = [];
	private $wordBufferSize = 0;
	private $wordOffsetSize = 0;

	private function __initParams(array $params){
		$this->lexiconFile = $params["lexicon"] ?? null;
		$this->categoriesFile = $params["categories"] ?? null;

		return;
	}

	private function __initReader(){
		if (!is_null($this->categoriesFile)){
			$this->categories = json_decode(file_get_contents($this->categoriesFile), true);
			$this->categoryHeaders = array_keys($this->categories);
		}

		if (!is_null($this->lexiconFile)){
			$fh = fopen($this->lexiconFile, "rb");

			$headers = [];
			while (($row = fgetcsv($fh, 0, ",")) !== FALSE){
				if ($row[1] == "emotion"){
					$headers = $row;
					continue;
				}

				$entry = [];
				foreach ($row as $key => $value) {
					if (isset($headers[$key])){
						$entry[$headers[$key]] = $value;
					}
				}

				array_push($this->vocabulary, $entry);
			}

			foreach($this->vocabulary as $v){
				$this->words[] = $v["word"];
			}
		}

		return;
	}

	private function addToDataList($word){
		$match = -1;

		foreach ($this->words as $key => $value) {
			if ($value == $word){
				$match = $key;
				break;
			}
		}

		if ($match >= 0){
			$entry = $this->vocabulary[$match];
			$row = [];
			foreach ($this->categoryHeaders as $category) {
				if ($entry[$category]){
					$index = array_search($entry[$category], $this->categories[$category]);
					array_push($row, $index);
				}
				else {
					array_push($row, -1);
				}
			}
			$this->data[] = $row;
		}

		return;
	}

	private function doAnalysis(array $wordList){
	    $entry = [];

	    foreach ($this->categories as $c => $value) {
	    	$entry[$c] = [];
	    	foreach ($this->categories[$c] as $cc) {
	    		$entry[$c][] = 0;
	    	}
	    }

	    foreach ($wordList as $w) {
	    	foreach ($this->categories as $c => $v) {
	    		if ($w[$c] >= 0){
	    			$entry[$c][$w[$c]] += 1;
	    			if ($entry[$c][$w[$c]] > $this->maxValues[$c]){
	                    $this->maxValues[$c] = $entry[$c][$w[$c]];
	    			}
	    		}
	    	}
	    }

	    $this->analyzedData[] = $entry;
	}

	private function getData(string $text){
		$words = explode(" ", $text);

		foreach ($words as $word) {
			self::addToDataList($word);
		}

		return $this->data;
	}

	private function analyzeData(int $bufferSize, int $offsetSize){
		foreach ($this->categories as $c => $v) {
			$this->maxValues[$c] = 0;
		}

		/*
		* $this->data mutation
		*/
		$inputData = [];
		foreach ($this->data as $row) {
			$entry = [];
			foreach ($row as $key => $value) {
				$entry[$this->categoryHeaders[$key]] = $value;
			}
			$inputData[] = $entry;
		}

		$this->data = $inputData;

		$wordBuffer = [];
		foreach ($this->data as $entry) {
			if (count($wordBuffer) >= $bufferSize) {
				self::doAnalysis($wordBuffer);
				$spliceStart = $bufferSize - $offset;
				$wordBuffer[] = $wordBuffer[$spliceStart];
			}
			else {
				$wordBuffer[] = $entry;
			}
		}

		self::doAnalysis($wordBuffer);

		foreach ($this->analyzedData as $i => $entry) {
			foreach ($this->categories as $c => $_c) {
				foreach ($entry[$c] as $j => $v) {
					if ($this->maxValues[$c] !== 0){
						$this->analyzedData[$i][$c][$j] = round(1.0 * $v / $this->maxValues[$c], 3);
					}
				}
			}
		}

		return $this->analyzedData;
	}

	private function formatAnalysis(){
		$data = [];
		foreach ($this->categories as $c=>$_c) {
			$headers = $this->categories[$c];
			$rows = [];
			foreach ($this->analyzedData as $entry) {
				$row = [];
				foreach ($entry[$c] as $key => $value) {
					$row[$headers[$key]] = $value;
				}
				$rows[] = $row;
			};

			$data[$c] = $rows;
		}

		return $data;
	}

	public function __construct(array $params){
		self::__initParams($params);
		self::__initReader();
	}

	public function run(string $text, int $bufferSize, int $offsetSize){
		self::getData($text);
		self::analyzeData($bufferSize, $offsetSize);

		$analysis = self::formatAnalysis();

		return $analysis;
	}
}