<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
include_once 'db_config.php';
?>
<html>
    <head>
        <meta charset="utf-8" />
        <title>MHS Query Builder</title>
        <link rel="shortcut icon" href="../favicon.ico" />
        <link rel="stylesheet" type="text/css" href="css/jquery-ui-1.9.2.custom.min.css"/>
        <link rel="stylesheet" type="text/css" href="css/mhs.css"/>
        <script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.9.2.custom.min.js"></script>
        <script type="text/javascript" src="js/mhs.js"></script>
        <script type="text/javascript" src="js/d3.v3.min.js"></script>
		<script>
		$(function() {
			$("#dialog").dialog({
				autoOpen: false,
				
			})
			$("#openDB").click(function() {
				$("#dialog").dialog("open");
			});
			$("#pickAField").click(function() { 
				 $("#pickField").dialog("open"); 
			 });
			<!-- $("#editField").click(function() { -->
			<!--	$("#pickField").dialog("open");-->
			<!-- }); -->
		});
		</script>

    </head>

    <body height="100%">
        <div><span><img src="img/logo_multicare.jpg"/></span>&nbsp;<span style="position: absolute;right: 10px"><img src="img/Ctr.Web.Data.Sci_uw_Inst.Tech.png"/></span></div>
        <div  style="width: 100%;text-align: right"><a href="#" class="systemLinks">My Account</a> <a href="#" class="systemLinks">Log Out</a></div>
        <br/>
        <div style="height:100%">
            <div id="leftColumn">
				<?php
					$strSQL = "SELECT DISTINCT(COLUMN_NAME) FROM INFORMATION_SCHEMA.`COLUMNS` WHERE table_schema='medical';";
					$result = execute($strSQL);
				?>
				<div id="fieldInfo">
					<p>Field:</p>
					<input id="fieldTextBox" type="text" disabled="disabled" value="Gender"></input>
					<a id="editField">edit</a>
					<!--<button id="editField">edit</button>-->
				</div>  
					<!-- <div><strong>Pick a Field</strong>
						<select id="chooseField" name="chooseField" >
							<option value = "RiskCategoryCD">RiskCategoryCD</option>
							<option value = "RiskCat">RiskCat</option>
							<option value = "PatientTypeCD">PatientTypeCD</option>
							<option value = "DischargeFollowupCategoryCD">DischargeFollowupCategoryCD</option>
							<option value = "AverageInpatientBPCategoryCD">AverageInpatientBPCategoryCD</option>
							<option value = "EjectionFractionCategoryCD">EjectionFractionCategoryCD</option>
							<option value = "EjectionFractionVAL">EjectionFractionVAL</option>
							<option value = "Gender" selected="selected">Gender</option>
							<option value = "Age">Age</option>
						</select> 
					</div> -->
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
                            if ($row['ORDINAL_POSITION'] == 1) {
                                continue;
                            }
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
				<button id="openDB">Pick Database Connection</button>
				<div id="dialog" title="Enter Database Information">
					<p class="validateTips">All form fields are required.</p>
					<form>
						<fieldset>
						<label for="name">Database Name:</label> </br>
						<input type="text" name="DBName" id="DBName" class="text ui-widget-content ui-corner-all" /> </br>
						<label for="username">Database Username:</label> </br>
						<input type="text" name="DBUser" id="DBUser" value="" class="text ui-widget-content ui-corner-all" /> </br>
						<label for="password">Database Password:</label> </br>
						<input type="password" name="DBPassword" id="DBPassword" value="" class="text ui-widget-content ui-corner-all" /> </br>
						</fieldset>
					</form>
				</div>
                <hr class="panelbreak" />
                <div id="segments">
                    <h3>My Segments</h3>
                    <ul>
                        <li>Test 1</li>
                        <li>Test 2</li>
                        <li>Test 3</li>
                    </ul>

                    <h3>Shared Segments</h3>
                    <ul>
                        <li>Test 1</li>
                        <li>Test 2</li>
                        <li>Test 3</li>
                    </ul>
                </div>




            </div>
            <div id="rightContent">

                <div id="tabs">
                    <ul>
                        <li><a href="#tabs-1">Segment Generation</a></li>
                        <li><a href="#tabs-2">Explore Models</a></li>
                        <li><a href="#tabs-3">Test Models</a></li>
                        <li><a href="#tabs-3">Display Models</a></li>
                        <li><a href="#tabs-3">Visualize</a></li>
                    </ul>
                    <div id="tabs-1">
                        <div id="dropContainer" style="width: 40%" class="dashed container">
                            <div class="label">Drag Fields Here</div>
                            <ul id="drop">

                            </ul>
                        </div> 
                        <div id="stats" style="width: 50%" class="dashed stats">

                        </div class="slide">
						<div id="pickFieldBox" title="Pick Field Box">
							<p>Pick a Field from the Database: <span><img id="exitIcon" src="img/exit-icon.png"/></span></p>
							<span><img id="expand" src="img/expand-icon.png"/></span>
							<select id="chooseFieldBox" name="chooseFieldBox" >
								<?php foreach ($result as $row): ?>
									<?php 
										if ($row["COLUMN_NAME"] == 'Gender') { 
									?>
									<option value = <?= $row["COLUMN_NAME"] ?> selected="selected"> <?= $row["COLUMN_NAME"] ?> </option>
									<?php 
										} else if ($row["COLUMN_NAME"] != 'seq') { 
									?>
									<option value = <?= $row["COLUMN_NAME"] ?>> <?= $row["COLUMN_NAME"] ?> </option>
									<?php
										}
									?>
								<?php endforeach; ?>
							</select>
						</div>
                        <div style="clear: both"></div>
						
                    </div>
					<!--
					<div id="pickField" title="Pick Field">
						<p class="validateTips">Pick a Field from the Database:</p>
						<select id="chooseField" name="chooseField" >
							<?php foreach ($result as $row): ?>
								<?php 
									if ($row["COLUMN_NAME"] == 'Gender') { 
								?>
								<option value = <?= $row["COLUMN_NAME"] ?> selected="selected"> <?= $row["COLUMN_NAME"] ?> </option>
								<?php 
									} else if ($row["COLUMN_NAME"] != 'seq') { 
								?>
								<option value = <?= $row["COLUMN_NAME"] ?>> <?= $row["COLUMN_NAME"] ?> </option>
								<?php
									}
								?>
							<?php endforeach; ?>
						</select>
					</div>
					-->
                    <div id="tabs-2">
					
                    </div>
                    <div id="tabs-3">
						
                    </div>
                </div>
            </div>
            <div style="clear:both"></div>
        </div>
        <div id="dialog" title="Select Join Field">
            <div id="possibleJoinFields">
                
            </div>
        </div>

    </body>

</html>