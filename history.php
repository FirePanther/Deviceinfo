<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<script src="amcharts/amcharts.js"></script>
<script src="amcharts/serial.js"></script>
<script src="amcharts/themes/light.js"></script>
<style>
body {
	font-family: verdana;
}
#chartdiv {
	width: 100%;
	height: 500px;
}
.all {
	font-size: 8px;
	color: #05f;
	opacity: .7;
}
</style>
<div id="chartdiv"></div>
<?php
// show all/last days (toggle)
echo '<a href="?'.(isset($_GET["all"]) ? '' : 'all').'" class="all">show '.(isset($_GET["all"]) ? 'last' : 'all').' days</a>';

// colors
$cCharge = "27ae60";
$cStandby = "7f8c8d";

$cHighDrain = "e74c3c";
$cMediumDrain = "f39c12";
$cLowDrain = "3498db";

$history = @json_decode(@file_get_contents("cache/history-b"), true);
$num = count($course);

if (!$num) die("No entries");

echo "<script>
var time = new Date(0), chartData = [];
// data: " . $num;
$prev = 0;
for ($i = 0; $i < $num; $i++) {
	$v = $course[$i];
	if (!isset($_GET["all"]) && $v[0] < time()-3600*24*3) continue;
	
	$nextV = $i < $num - 1 ? $history[$i + 1] : [ null, null, null ];
	
	// charging next (the next step increases, with this variable it looks just better)
	$chargingLater = $nextV[2] === true && $nextV[1] > $v[1];
	
	// charging? (or charging next)
	if ($v[2] || $chargingLater) $color = $cCharge;
	
	// diff
	if (!$v[2] && !$chargingLater && $nextV[0] !== null) {
		$dT = $nextV[0] ? $nextV[0] - $v[0] : 0;
		$dB = $nextV[1] ? $v[1] - $nextV[1] : 0;
		$drainPerMinute = $dB / $dT * 60;
		if ($drainPerMinute > 1) $color = $cHighDrain;
		elseif ($drainPerMinute > .5) $color = $cMediumDrain;
		elseif ($drainPerMinute > 0.001) $color = $cLowDrain;
		else $color = $cStandby;
	} else $drainPerMinute = 0;
	
	echo '
chartData.push({
	lineColor: "#'.$color.'",
	drain: "'.round($drainPerMinute, 2).'%/min",
	time: '.($v[0] * 1000).', // '.date("d.m. H:i", $v[0]).'
	battery: '.$v[1].'
});';
}
?>
var chart = AmCharts.makeChart("chartdiv", {
	type: "serial",
	theme: "light",
	marginRight: 80,
	autoMarginOffset: 20,
	marginTop: 7,
	dataProvider: chartData,
	valueAxes: [{
		maximum: 100,
		axisAlpha: 0.2,
		dashLength: 1,
		position: "left"
	}],
	mouseWheelZoomEnabled: true,
	graphs: [{
		id: "g1",
		balloonText: "[[category]]<br/><b style='font-size:14px;'>battery: [[value]]%</b> ([[drain]])",
		bullet: "round",
		bulletBorderAlpha: 1,
		bulletBorderThickness: 1,
		fillAlphas: 0.3,
		fillColorsField: "lineColor",
		lineColorField: "lineColor",
		lineThickness: 3,
		title: "FirePhone Battery History",
		valueField: "battery",
		useLineColorForBulletBorder: true
	}],
	chartScrollbar: {
		autoGridCount: true,
		graph: "g1",
		scrollbarHeight: 60
	},
	chartCursor: {
		categoryBalloonDateFormat: "DD. MMM, JJ:NN",
		cursorPosition: "mouse"
	},
	categoryField: "time",
	categoryAxis: {
		minPeriod: "mm",
		parseDates: true,
		axisColor: "#DADADA",
		dashLength: 1,
		minorGridEnabled: true
	},
	export: {
		enabled: true
	}
});

chart.addListener("rendered", function(){ chart.zoomToIndexes(chartData.length - 40, chartData.length - 1) });
</script>