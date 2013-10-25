<?php
include_once 'db_config.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (isset($_POST['task'])) {
    $task = $_POST['task'];
} else {
    $task = "nothing";
}

if ($task == "getUniqueFieldValues") {
    $table = $_POST['table'];
    $field = $_POST['field'];
    $strSQL = "SELECT DISTINCT(`$field`) FROM $table;";
    $result = execute($strSQL);
    $name = "$table~$field~";
    ?>
    <ul class='options' >
        <?php foreach ($result as $value): ?>
            <li name="<?php echo $name . $value[$field] ?>"><?php echo $value[$field]; ?></li>
        <?php endforeach; ?>
    </ul>  
    <?php
}else if ($task == "getJoinFields") {
    $table = $_POST['tables'];
    $sql = "select distinct(column_name) from information_schema.columns where table_schema = '$DBName' AND table_name in ('" . implode($table, "','") . "') order by table_name,ordinal_position";
    $result = execute($sql);
    $default = "checked";
    foreach ($result as $row) {
        ?>
        <input type="radio" name="joinFields" <?php echo $default; ?> onclick="updateJoinField(this)" value="<?php echo $row['column_name']; ?>" /><?php echo $row['column_name']; ?><br/>
        <?php
        $default = "";
    }
} else if ($task = "generateStatistics") {
    $names = explode("|", $_POST["name"]);
    $joinField = $_POST["joinField"];
	$chooseField = $_POST['chooseField'];
	$chooseType = $_POST['chooseType'];
    $prevTable = "";
    $queryFrom = "";
    $queryWhere = "";
    foreach ($names as $index => $name) {
        $tmp = explode("~", $name);
        $table = $tmp[0];
        $field = $tmp[1];
        $value = $tmp[2];
        $strSQL = "SELECT count(0) as count FROM $table WHERE `$field` = '$value';";
        $result = execute($strSQL);
        echo "<div id='indvCount'>";
        foreach ($result as $row) {
            echo "<div><b>$field</b> $value N = " . $row["count"] . "</div>";
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
    
    $mainQuery = "SELECT temp.$chooseField, COUNT(0) as count FROM ($joinQuery) AS temp GROUP BY temp.$chooseField";
//    echo $mainQuery;
    $result = execute($mainQuery);
    $count = 0;
    $data = "";
    
    if ($chooseType=="Bar Chart") {
		foreach ($result as $index => $row) {
			$count = $count + $row["count"];
			$data .= '{\'data\':\'' . $row[$chooseField] . '\',\'value\':' . $row["count"] . '}';
			if ($index != count($result) - 1)
				$data .= ',';
		}
		$data = '[' . $data . ']';
// 		echo "chooseField=".$chooseField;
// 		echo "chooseType=".$chooseType;
// 		echo $data;
		echo "<div id='matchCount'><div><b>Matched</b> N = $count </div></div>";
		include_once 'bar.php';
	}
	if ($chooseType=="Pie Chart") {
		foreach ($result as $index => $row) {
			$count = $count + $row["count"];
			$data .= '{"label":"' . $row[$chooseField] . '","value":"' . $row["count"] . '"}';
			if ($index != count($result) - 1)
			$data .= ',';
		}
		$data = '[' . $data . ']';
// 		echo "chooseField=".$chooseField;
// 		echo "chooseType=".$chooseType;
// 		echo $data;
		echo "<div id='matchCount'><div><b>Matched</b> N = $count </div></div>";
		include_once 'pie.php';
	}
	if ($chooseType=="Map") {
		foreach ($result as $index => $row) {
			$count = $count + $row["count"];
			$data .= '{"label":"' . $row[$chooseField] . '","value":"' . $row["count"] . '"}';
			if ($index != count($result) - 1)
			$data .= ',';
		}
		$data = '[' . $data . ']';
// 		echo "chooseField=".$chooseField;
// 		echo "chooseType=".$chooseType;
// 		echo $data;
		echo "<div id='matchCount'><div><b>Matched</b> N = $count </div></div>";
		include_once 'map.php';
	}
	?>

    
<!--     <style>
			.bars div {
			font: 10px sans-serif;
			background-color: steelblue;
			text-align: right;
			padding: 3px;
			margin: 1px;
			color: white;
			}
    </style>
 -->    
    <?php
}
?>
