<?php
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
	if (in_array($_GET["g"], $infos)) {
		if (is_file("cache/info-".$_GET["g"])) {
			$f = file_get_contents("cache/info-".$_GET["g"]);
			list($v, $c) = explode(",", $f);
			if (isset($format[$_GET["g"]])) $v = $format[$_GET["g"]]($v);
			
			if (isset($_GET["html"])) {
				echo "<html>";
				echo "<img src=\"http://suat.be/api/ios/img/8/".(round($v/10)*10).($c == "c" ? "c" : "").".png\">";
				echo "</html>";
			} else {
				echo $v.($c == "c" ? "c" : "");
			}
		} else die("info doesn't exist");
	} else die("unknown info request");
// set
} elseif (isset($_GET["s"], $_GET["v"])) {
	if (!isset($_GET["p"]) || $_GET["p"] != "2918") die("invalid param p");
	
	if (in_array($_GET["s"], $infos)) {
		if (!isset($pattern[$_GET["s"]]) || preg_match($pattern[$_GET["s"]], $_GET["v"])) {
			file_put_contents("cache/info-".$_GET["s"], $_GET["v"].",".(isset($_GET["c"]) ? "c" : "u"));
			
			switch ($_GET["s"]) {
				case "b":
					$curCourse = @json_decode(@file_get_contents("course"), 1);
					$newCourse = [];
					$old = time() - 3600 * 24 * 14;
					foreach ($curCourse as $arr) {
						if ($arr[0] > $old) $newCourse[] = $arr;
					}
					$newCourse[] = [ time(), isset($format[$_GET["s"]]) ? $format[$_GET["s"]]($_GET["v"]) : $_GET["v"], isset($_GET["c"]) ];
					file_put_contents("course", json_encode($newCourse));
					break;
			}
			echo "ok";
		} else die("the value isn't valid");
	} else die("unknown info request");
} else die("unknown requet");