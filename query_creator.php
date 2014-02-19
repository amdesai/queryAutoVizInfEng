<?php

foreach ($names as $index => $name) {
	$tmp = explode("~", $name);
	$table = $tmp[0];
	$field = $tmp[1];
	$value = $tmp[2];

	$strSQL = "SELECT count(0) as value FROM $table WHERE `$field` = '$value';";
	$result = execute($strSQL);
	echo "<div id='indvCount'>";
	
	foreach ($result as $row) {
		echo "<div><b>Where Clause Field: \"$field\"</b> -> Selected Value: \"$value\"" .
		"<br/>Total Records found: N = " . $row["value"] . "</div>";
	}
	
	echo "</div>";

	if ($index == 0) {
		$queryFrom .= $table;
	} else if ($prevTable == $table) {
		$queryWhere.= " AND ";
	} else if ($prevTable != $table) {
		$queryFrom .= " INNER JOIN $table ON $prevTable.$joinField = $table.$joinField";
		$queryWhere.= " AND ";
	}
	$queryWhere.= "$table.`$field` = '$value' ";
	$prevTable = $table;
}
	$joinQuery = "SELECT $prevTable.$chooseField FROM $queryFrom WHERE $queryWhere";
?>