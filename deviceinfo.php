<?php
include "password.php"; // this file just contains: <?php $PASSWORD = "your-password";

header("content-type: text/plain; charset=utf-8");

$infos = [ "b"/*battery*/ ];
$pattern = [
	"b" => "~^(\d\.\d+|1|0)$~"
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
		if (is_file("cache/info-" . $get)) {
			$info = file_get_contents("cache/info-" . $get);
			list($value, $charging) = explode(",", $info);
			
			// call format function if exist
			if (isset($format[$get])) $value = $format[$get]($value);
			
			echo $value . $charging;
		} else die("info doesn't exist");
	} else die("unknown info request");

// set
} elseif (isset($_GET["s"], $_GET["v"])) {
	$set = $_GET["s"];
	$value = $_GET["v"];
	$charging = isset($_GET["c"]) ? "c" : "";
	
	// password
	if (!isset($_GET["p"]) || $_GET["p"] != $PASSWORD) die("invalid param p");
	
	if (in_array($set, $infos)) {
		if (!isset($pattern[$set]) || preg_match($pattern[$set, $value)) {
			file_put_contents("cache/info-" . $set, $value . "," . $charging;
			
			// special functions
			switch ($set) {
				case "b":
					// save the battery history
					$curHistory = @json_decode(@file_get_contents("cache/history-b"), 1);
					$newHistory = [];
					$old = time() - 3600 * 24 * 14; // save for 14 days
					foreach ($curHistory as $arr) {
						if ($arr[0] > $old) $newHistory[] = $arr;
					}
					$newHistory[] = [ time(), isset($format[$_GET["s"]]) ? $format[$set]($value) : $value, (boolean)$charging ];
					file_put_contents("cache/history-b", json_encode($newHistory));
					break;
			}
			
			echo "ok";
		} else die("the value isn't valid");
	} else die("unknown info request");
} else die("unknown requet");