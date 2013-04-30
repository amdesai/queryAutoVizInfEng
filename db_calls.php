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
    $joinQuery = "SELECT $prevTable.Gender FROM $queryFrom WHERE $queryWhere";
    
    $mainQuery = "SELECT temp.Gender, COUNT(0) as count FROM ($joinQuery) AS temp GROUP BY temp.Gender";
//    echo $mainQuery;
    $result = execute($mainQuery);
    $count = 0;
    $data = "";
    foreach ($result as $index => $row) {
        $count = $count + $row["count"];
        $data .= '{"label":"' . $row["Gender"] . '","value":"' . $row["count"] . '"}';
        if ($index != count($result) - 1)
            $data .= ',';
    }
    $data = '[' . $data . ']';
    // echo $data;
    echo "<div id='matchCount'><div><b>Matched</b> N = $count </div></div>";
    ?>
    <div id="chart"></div>
    <script type="text/javascript">
        var w = 300,                        //width
        h = 300,                            //height
        r = 100,                            //radius
        color = d3.scale.category20c();     //builtin range of colors
        var data  = <?php echo $data; ?>;
        /*data = [{
            "label":"one", 
            "value":20
        }, 
        {
            "label":"two", 
            "value":50
        }, 
        {
            "label":"three", 
            "value":30
        }];
        console.log(data);*/
        var vis = d3.select("#chart")
        .append("svg:svg")              //create the SVG element inside the <body>
        .data([data])                   //associate our data with the document
        .attr("width", w)           //set the width and height of our visualization (these will be attributes of the <svg> tag
        .attr("height", h)
        .append("svg:g")                //make a group to hold our pie chart
        .attr("transform", "translate(" + r + "," + r + ")")    //move the center of the pie chart from 0, 0 to radius, radius
         
        var arc = d3.svg.arc()              //this will create <path> elements for us using arc data
        .outerRadius(r);
         
        var pie = d3.layout.pie()           //this will create arc data for us given a list of values
        .value(function(d) {
            return d.value;
        });    //we must tell it out to access the value of each element in our data array
         
        var arcs = vis.selectAll("g.slice")     //this selects all <g> elements with class slice (there aren't any yet)
        .data(pie)                          //associate the generated pie data (an array of arcs, each having startAngle, endAngle and value properties) 
        .enter()                            //this will create <g> elements for every "extra" data element that should be associated with a selection. The result is creating a <g> for every object in the data array
        .append("svg:g")                //create a group to hold each slice (we will have a <path> and a <text> element associated with each slice)
        .attr("class", "slice")
        .classed("labelSmall", true);    //allow us to style things in the slices (like text)
         
        arcs.append("svg:path")
        .attr("fill", function(d, i) {
            return color(i);
        } ) //set the color for each slice to be chosen from the color function defined above
        .attr("d", arc);                                    //this creates the actual SVG path using the associated data (pie) with the arc drawing function
         
        arcs.append("svg:text")                                     //add a label to each slice
        .attr("transform", function(d) {                    //set the label's origin to the center of the arc
            //we have to make sure to set these before calling arc.centroid
            d.innerRadius = 0;
            d.outerRadius = r;
            return "translate(" + arc.centroid(d) + ")";        //this gives us a pair of coordinates like [50, 50]
        })
        .attr("text-anchor", "middle")                          //center the text on it's origin
        .text(function(d, i) {
            return data[i].label;
        }); 
    </script>
    <?php
}
?>
