
<div id="chart"></div>
<div id="bars"></div>

<style>
svg {
  position:absolute; border: 1px dashed #CCCCCC; padding:0px; margin: 0px;  right: 140px;
}
#chart {
  font: 10px sans-serif;
}
#counties path {
  stroke: green;
  stroke-width: 1px;
}
#states path {
  fill: blue;
  stroke: #fff;
  stroke-width: 1.5px;
}
.box { font: 10px sans-serif; }
.box line, .box rect, .box circle { stroke: #000; stroke-width: 1.5px; fill: #fff; }
.box .center { stroke-dasharray: 3 3; }
.box .outlier { stroke: #ccc; fill: none; }
#heatmap circle {
  fill-opacity: .7;
}
.hidden {
  visibility: hidden;
  max-height: 0px;
  max-width: 0px;
  overflow: hidden;
}
</style>

<script>

var data; // loaded asynchronously
var counties_features;
var counties_data;
var width = 450,
    height = 300;

//The radius scale for the centroids.
var r = d3.scale.sqrt()
    .domain([0, 1e6])
    .range([0, 10]);

var projection = d3.geo.albersUsa()
    .scale([4800])
    .translate([1490,1120]);

var path = d3.geo.path()
            .projection(projection);

var title = d3.select("#chart").append("div")
  .attr("style", "text-align:center");

var svg = d3.select("#chart")
  .append("svg")
  .attr("width", width)
  .attr("height", height);

var counties = svg.append("g")
    .attr("id", "counties")
    .attr("class", "Bl_custom")
    .attr("transform", "rotate(-14 100 100)");
    
var county_labels = svg.append("g")
    .attr("id", "county_labels")
    .attr("transform", "rotate(-14 100 100)");

// Get county data
d3.json("json/us-counties.json", function(json) {
  var centroids = new Array();
  counties_features = json.features;
  counties.selectAll("path")
      .data(json.features)
    .enter().append("path")
      .attr("class", data ? quantize : null)
      .attr("d", function(d, i) {centroids[i] = path.centroid(d); return path(d);});
      
  county_labels.selectAll("text")
      .data(json.features)
    .enter().append("text")
      .attr("transform", function(d, i) { var pos_x = centroids[i][0] - 10; var pos_y = centroids[i][1]; return "translate(" + pos_x + "," + pos_y + ")rotate(14)"; });
});
</script>

