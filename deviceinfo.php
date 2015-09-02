<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "password.php"; // this file just contains: <?php $PASSWORD = "your-password";

header("content-type: text/plain; charset=utf-8");

$infos = [ "b"/*battery*/ ];
$pattern = [
	"b" => "~^(0\.\d+|1|0)$~" // double 0 - 1
];
$format = [
	"b" => function($v) {
		return round($v * 100);
	}
];

if (!is_dir("cache")) mkdir("cache");

// get
if (isset($_GET["g"])) {
	$get = $_GET["g"];
	
	if (in_array($get, $infos)) {
		$filename = "cache/info-" . $get;
		if (is_file($filename) && is_readable($filename)) {
			$info = file_get_contents($filename);
			echo $info;
		} else die("info doesn't exist or isn't readable");
	} else die("unknown info request");

// set
} elseif (isset($_GET["s"], $_GET["v"])) {
	$set = $_GET["s"];
	$value = $_GET["v"];
	$charging = isset($_GET["c"]) ? "c" : "";
	
	// password
	if (!isset($_GET["p"]) || $_GET["p"] != $PASSWORD) die("invalid password");
	
	if (in_array($set, $infos)) {
		if (!isset($pattern[$set]) || preg_match($pattern[$set], $value)) {
			// call format function if exist
			if (isset($format[$set])) $value = $format[$set]($value);

			file_put_contents("cache/info-" . $set, $value . $charging);
			
			// special functions
			switch ($set) {
				case "b":
					// save the battery history
					$curHistory = @json_decode(@file_get_contents("cache/history-b"), true);
					$newHistory = [];
					$old = time() - 3600 * 24 * 14; // save for 14 days
					foreach ($curHistory as $arr) {
						if ($arr[0] > $old) $newHistory[] = $arr;
					}
					$newHistory[] = [ time(), $value, (boolean)$charging ];
					file_put_contents("cache/history-b", json_encode($newHistory));
					break;
			}
			
			echo "ok";
		} else die("the value isn't valid");
	} else die("unknown info request");
} else die("unknown requet");