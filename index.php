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

        <script type="text/javascript">

        </script>
    </head>

    <body height="100%">
        <div><span><img src="img/logo_multicare.jpg"/></span>&nbsp;<span style="position: absolute;right: 10px"><img src="img/Ctr.Web.Data.Sci_uw_Inst.Tech.png"/></span></div>
        <div  style="width: 100%;text-align: right"><a href="#" class="systemLinks">My Account</a> <a href="#" class="systemLinks">Log Out</a></div>
        <br/>
        <div style="height:100%">
            <div id="leftColumn">
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

                        </div>
                        <div style="clear: both"></div>
                    </div>
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