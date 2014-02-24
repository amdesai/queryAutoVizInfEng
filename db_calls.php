<?php
include_once 'db_config.php';

$aggregator_field = null;
$groupByMode = false;
$isCategorical = false;
$isGeographical = false;
$isUnknownDataType = false;
$isNumerical = false;
$groupByCandidateFields = array();

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

<ul class='options'>
	<?php foreach ($result as $value): ?>
	<li name="<?php echo $name . $value[$field] ?>"><?php echo $value[$field]; ?>
	</li>
	<?php endforeach; ?>
</ul>
<?php
} else if ($task == "getJoinFields") {
    $table = $_POST['tables'];
    $sql = "select distinct(column_name) from information_schema.columns where table_schema = '$DBName' AND table_name in ('" . implode($table, "','") . "') order by table_name,ordinal_position";
    $result = execute($sql);
    $default = "checked";
    foreach ($result as $row) {?>
		<input type="radio" name="joinFields" <?php echo $default; ?> onclick="updateJoinField(this)" value="<?php echo $row['column_name']; ?>" />
		<?php echo $row['column_name']; ?>
		<br />
		<?php
		$default = "";
    }
// } else if ($task == "checkAggregationGenerateStats") {
// 	include_once 'query_creator.php';
// 	$result = execute($mainQuery);
// 	// check query if we need to do aggregation
// 	if (sizeof($result) > 15) {
// 		echo 	"<p>Result has too many rows.</p>" .
// 				"<p>Please pick a group by field:</p>";
// 		$groupByMode = true;
// 		include_once 'groupbyselector.php';
// 	} else {
// 		$groupByMode = false;
// 		include_once 'viz_generator.php';
// 	}
} else if ($task == "generateStatistics") {
	
	$names = explode("|", $_POST["name"]);
	$table;
	$field;
	$value;
	$joinField = $_POST["joinField"];
	$chooseField = $_POST['chooseField'];
	$chooseType = $_POST['chooseType'];
	$prevTable = "";
	$queryFrom = "";
	$queryWhere = "";
	$groupByField = $_POST['groupByField'];
	$joinQuery;
	$mainQuery;
	$table;
	$field;
	$value;
	
	include_once 'query_creator.php';
	
	//////////////////////////////////////
	// Basic Rule-Based Inference Algo: //
	//////////////////////////////////////

// 		if Visualization Field is Categorical:
// 			if field is geo-spatial
// 					generate a map
// 			else
// 				if number of results after group by chooseField <= 15
// 					generate a pie chart
// 				else
// 					generate a bar chart
			
// 		else // Visualization Field is Numeric
// 			infer select the optimal method of aggregating data:
// 			if a categorical field with the least missing values (i.e. most populated field) is identified by inference engine
// 				group-by field is auto selected and used to generate visualization
// 			else
// 				discretize the data in the visualization field into ten intervals while allowing the user to change the number of intervals, and use intervals for aggregating data
		
// 			if group-by field is not "acceptable" to user
// 				allow user to select a different categorical "group by" field
				 
// 			Use auto-selected or user-selected aggregation field or discretization intervals to compute the averages of numeric data in each group and use following rules to select the appropriate visualization type:
// 			if group-by field is geographic
// 				generate a map
// 			else
// 				if number of results after group by chooseField <= 15
// 					generate a pie chart
// 				else
// 					generate a bar chart
			
// 		The algorithm can be extended with more types of visualizations at this point based on a more rules designed to handle a wider range of scenarios for which to choose appropriate visualizations types.
	
	
	if (isNumerical($table, $queryFrom, $queryWhere, $chooseField)) {
		echo "<p>Infered Visualization Field Type: <b>Numerical</b></p>";
		$isCategorical = false;
		$isNumerical = true;
		$groupByMode = true;
// 		global $groupByField;
		$val_bool = is_null($groupByField);
		if (is_null($groupByField)) {
			$groupByField = selectGroupByField($table, $chooseField, $queryFrom, $queryWhere, $field, $value);
		}
// 		unset($groupByField);
// 		$groupByMode = false;
	} else {
		echo "<p>Infered Visualization Field Type: <b>Categorical</b></p>";
		$isCategorical = true;
		$isNumerical = false;
		$groupByMode = false;
	}

	$mainQuery = 	"SELECT temp." . $chooseField. ", COUNT(0) as value " .
					"FROM (" . $joinQuery .") AS temp ".
					"GROUP BY temp." . $chooseField . " ORDER BY value DESC";
	
	
	if ($groupByMode) {
		$aggregateQuery = "SELECT temp.$groupByField, avg(temp.$chooseField) as value " .
			"FROM (SELECT * FROM $queryFrom WHERE $queryWhere) AS temp " .
			"GROUP BY temp.$groupByField";
			echo "<p>AGGR QUERY: <br />".$aggregateQuery."</p>"; // for debugging
		$result = execute($aggregateQuery);
		$groupByMode = true;
	} else {
		echo "<p>MAIN QUERY: <br />".$mainQuery."</p>"; // for debugging
		$result = execute($mainQuery);
		$groupByMode = false;
	}
	include_once 'viz_generator.php';
	include_once 'groupbyselector.php';
	include_once 'viz_type_changer.php';
}

function isNumerical($tab, $from, $where, $selectedField) {
	$sql = "SELECT " . $tab . "." . $selectedField .
			" FROM " . $from .
			" WHERE " . $where .
			" ORDER BY $tab.$selectedField DESC";
	$queryResult = execute($sql);
	$total = sizeof($queryResult);
	$i = 0;
	$skips = floor($total / 11);
	foreach ($queryResult as $index => $val) {
		if ($index == $i) {
			$trm_val = trim($val[0]);
			if (!(isNotApplicableValue($trm_val)) // the value is not N/A, NA or Null or NotApplicable, empty etc
					&& !is_numeric($trm_val)) { // its not numeric
// 					echo "<p>OUCH NON-NULL NON-NUMERIC VALUE FOUND!<br/>This is not a Numeric Field!</p>";
// 					echo "<p>value = $val[0]</p>";
					return false;
				}
			$i += $skips;
		}
	}
	return true;
}

function isNotApplicableValue($val) {
	$val = strtolower($val);
	if (is_null($val) // null
		|| strlen($val) == 0 // empty string
		|| $val == "na"
		|| $val == "n/a"
		|| $val == "null"
		|| $val == "not applicable") {
		return true;
	}
	return false;
}

// function isCategorical($selectedField) {
// 	$catFields = array("ethnicity","gender", "county", "subgroup");

// 	foreach ($catFields as $cf) {
// 		$lc = strtolower($selectedField);

// 		if (strpos(strtolower($selectedField), $cf) !== false) {
// 			return true;
// 		}
// 	}
// 	return false;
// }

function isGeographical($selectedField) {
	$geoFields = array("city","county","district","state","country");
	foreach ($geoFields as $gf) {
		$lcAndTrim = strtolower(trim($selectedField));
		if ($lcAndTrim == $gf) {
			return true;
		}
	}
	return false;
}

/*
 * @params: $t is table and $cf is chosen field.
 */
function selectGroupByField($t, $cf, $from, $where, $where_field, $where_value) {
	// get all columns in the table
	$getColSql = "SELECT column_name FROM information_schema.columns WHERE table_name = '$t'";
	$cols = execute($getColSql);
	// for only non-numerical columns get the column with the lowest stddev
	// i.e. the one that best "distributes" the data for the chosen field
	$minStdDev = PHP_INT_MAX;
	global $groupByCandidateFields;
	foreach ($cols as $i => $col) {
		if ($where_field != $col[0] && !isNumerical($t, $from, $where, $col[0])) {
			$stdDevSql = "SELECT STDDEV(ct.c) as stddev FROM ".
							"(SELECT count(distinct $cf) AS c FROM $t".
							" WHERE $where_field = '$where_value' GROUP BY $col[0]) AS ct;";
			$stdDevLoc = execute($stdDevSql);
			$tmp = $stdDevLoc[0];
			$groupByCandidateFields[] = $col[0];
			if ($tmp[0] < $minStdDev) {
				$minStdDev = $tmp[0];
				$gbf = $col[0];
			}
		}
	}
	echo "<p>Inference Engine Auto-selected Group By Field: <b>$gbf</b></p>";
// 	foreach ($groupByCandidateFields as $i => $gbcf) {
// 		echo "<p>field $i: <b>$gbcf</b><br/></p>";
// 	}
	return $gbf;
}

?>
