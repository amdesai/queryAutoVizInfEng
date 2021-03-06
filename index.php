<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
include_once 'db_config.php';
?>
<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<title>Generic Query Builder</title>
<link rel="shortcut icon" href="../favicon.ico" />
<link rel="stylesheet" type="text/css"
	href="css/jquery-ui-1.9.2.custom.min.css" />
<link rel="stylesheet" type="text/css" href="css/mhs.css" />
<!-- <link rel="stylesheet" type="text/css"
	href="bower_components/simple-map-d3/dist/simple-map-d3.css"> -->

<script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.9.2.custom.min.js"></script>
<script type="text/javascript" src="js/mhs.js"></script>
<script type="text/javascript" src="js/d3.v3.min.js"></script>
<!-- <script type="text/javascript"
	src="http://maps.google.com/maps/api/js?sensor=true"></script> -->
<!-- <script type="text/javascript"
	src="http://mbostock.github.com/d3/d3.js?1.29.1"></script> -->

<!-- <script src="http://d3js.org/topojson.v1.min.js"></script>
<script src="http://d3js.org/queue.v1.min.js"></script> -->
<script src="bower_components/simple-map-d3/dist/simple-map-d3.js"
	charset="utf-8"></script>


</head>

<body height="100%">
	<div>
		<span style="font-size: 120%"> <!-- <img src="img/logo_multicare.jpg"/> -->QUERY
			BUILDER | EDUCATIONAL DATA
		</span> &nbsp; <span style="position: absolute; right: 10px"><img
			src="img/Ctr.Web.Data.Sci_uw_Inst.Tech.png" /> </span>
	</div>
	<div style="width: 100%; text-align: right">
		<a href="#" class="systemLinks">My Account</a> <a href="#"
			class="systemLinks">Log Out</a>
	</div>
	<br />
	<div style="height: 100%">
		<div id="leftColumn">
			<div id="fieldInfo">
				<span>Visualization Options:</span>
				<p>Field:</p>
				<input id="fieldTextBox" type="text" disabled="disabled" value=""></input>
				<p>Type:</p>
				<input id="typeTextBox" type="text" disabled="disabled" value=""></input>
				<br></br>
				<button id="editField" style="font-size: 110%">Change</button>
				<br></br>
			</div>
			<div id="fieldList">
				<?php
				$tablesSchema = execute("select * from information_schema.columns where table_schema = '$DBName' order by table_name,ordinal_position");
				?>
				<ul>

					<?php
					$currentTable = "";
					$oldTable = "";
					$title = "";
					$ui = "";
					?>
					<?php foreach ($tablesSchema as $row): ?>
					<?php
					//                            if ($row['ORDINAL_POSITION'] == 1) {
					//                                continue;
					//                            }
					$oldTable = $currentTable;
					$currentTable = $row['TABLE_NAME'];
					$title = "<div>" . $oldTable . "</div>";
					if ($currentTable != $oldTable && $oldTable != "") {
                                //HTML Will print
                                echo "<li>$title<ul>$ui</ul><li>";
                                $ui = "<li name='" . $row['TABLE_NAME'] . "~" . $row['COLUMN_NAME'] . "'>" . $row['COLUMN_NAME'] . "</li>";
                            } else {
                                $ui.= "<li name='" . $row['TABLE_NAME'] . "~" . $row['COLUMN_NAME'] . "'>" . $row['COLUMN_NAME'] . "</li>";
                            }
                            ?>
					<?php endforeach; ?>
					<?php echo "<li>$title<ul>$ui</ul><li>"; ?>
				</ul>
			</div>
			<hr class="panelbreak" />

		</div>
		<div id="rightContent">

			<div id="tabs">
				<ul>
					<li><a href="#tabs-1">Visualization Area</a></li>
				</ul>

				<div id="tabs-1">
					<div id="dropContainer" style="width: 35%" class="dashed container">
						<div class="label">Drag "Where Clause" Fields Here</div>
						<ul id="drop">
						</ul>
					</div>

					<div id="stats" style="width: 60%" class="dashed stats">
						<div style="clear: both"></div>
					</div>
				</div>
			</div>
		</div>
		<div style="clear: both"></div>
	</div>
	<div id="dialog" title="Select Join Field">
		<div id="possibleJoinFields"></div>
	</div>
</body>
<!-- <div class="mark">
	<img src="img/settings.png" />
</div> -->
<div id="options">
	<div>
		<img onclick="toggleOptions()" style="cursor: pointer;"
			src="img/exit-icon.png" />
	</div>
	<table class="options_table">
		<tr>
			<td>Visualization Field:</td>
		</tr>
		<tr>
			<td><select id="chooseField" name="chooseField"
				onchange="updateVisualizationField(this)">
					<?php
					$strSQL = "SELECT DISTINCT(COLUMN_NAME) FROM INFORMATION_SCHEMA.`COLUMNS` WHERE table_schema='$DBName';";
					$result = execute($strSQL);
					sort($result);
					?>
					<option selected="selected">Select Visualization Field</option>
					<?php foreach ($result as $row):
						if ($row["COLUMN_NAME"] == "Met AYP" || $row["COLUMN_NAME"] == "Met") {
							$tmp = $row["COLUMN_NAME"];
						}
					?>
					<option value=<?php echo $row["COLUMN_NAME"]; ?>><?php echo $row["COLUMN_NAME"]; ?></option>
					<?php endforeach; ?>
			</select>
			</td>
		</tr>
		<!-- 		<tr>
			<td>Visualization Type:</td>
		</tr>
		<tr>
			<td><select id="chooseType" name="chooseType"
				onchange="updateVisualizationType(this)">
					<option>Pie Chart</option>
					<option selected>Bar Chart</option>
					<option>Map</option>
			</select>
			</td>
		</tr> -->

	</table>
</div>
</html>
