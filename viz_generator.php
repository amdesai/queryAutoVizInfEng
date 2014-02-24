<?php

$count = sizeof($result);
$data = "";
$aggregator_field = null;

$isGeographical = true;

if ($groupByMode) {
	$aggregator_field = $groupByField;
} else {
	$aggregator_field = $chooseField;
}

if ($isCategorical && $count <= 15) {
	$chooseType = "Pie Chart";
} else if ($isGeographical){
	$chooseType = "Map";
} else {
	$chooseType = "Bar Chart";
}

if ($chooseType=="Bar Chart") {
		foreach ($result as $index => $row) {
			$data .= '{\'data\':\'' . $row[$aggregator_field] . '\',\'value\':' . $row["value"] . '}';
			if ($index != count($result) - 1)
					$data .= ',';
		}
		$data = '[' . $data . ']';
		echo "<div id='matchCount'><div><b>Number of Results</b> N = $count </div></div>";
		echo "<div><p>Inference Engine selected <b>Bar Chart</b> Visualization for this Query!</p></div>";
		include_once 'bar.php';
}
if ($chooseType=="Pie Chart") {
	foreach ($result as $index => $row) {
		$data .= '{"label":"' . $row[$aggregator_field] . '","value":"' . $row["value"] . '"}';
		if ($index != count($result) - 1)
			$data .= ',';
	}
	$data = '[' . $data . ']';
	echo "<div id='matchCount'><div><b>Number of Results</b> N = $count </div></div>";
	echo "<div><p>Inference Engine selected <b>Pie Chart</b> Visualization for this Query!</p></div>";
	include_once 'pie.php';
}
if ($chooseType=="Map") {
	foreach ($result as $index => $row) {
		$data .= '{"label":"' . $row[$aggregator_field] . '","value":"' . $row["value"] . '"}';
		if ($index != count($result) - 1)
		$data .= ',';
	}
	echo "<div id='matchCount'><div><b>Number of Results</b> N = $count </div></div>";
	echo "<div><p>Inference Engine selected <b>Map</b> Visualization for this Query!</p></div>";
	echo "<p>Map Viz Goes Here!</p>";
	include_once 'map.php';
}
?>