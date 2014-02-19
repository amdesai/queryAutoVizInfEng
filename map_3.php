<div id="chart"></div>
<div id="bars"></div>

<style type="text/css">
#chart {
  width: 50%;
  height: 50%;
  margin: 0;
  padding: 0;
}

svg {
  position: absolute;
}

svg {
  width: 60px;
  height: 20px;
  padding-right: 100px;
  font: 10px sans-serif;
}

</style>

<script>

var path, vis, xy;

xy = d3.geo.mercator().scale(8500).translate([0, -1200]);
 
path = d3.geo.path().projection(xy);
 
vis = d3.select("#chart").append("svg:svg").attr("width", 960).attr("height", 600);
 
d3.json("us-centroids.json", function(json) {
  return vis.append("svg:g")
  			.attr("class", "tracts")
  			.selectAll("path")
  			.data(json.features)
  			.enter()
  			.append("svg:path")
  			.attr("d", path)
  			.attr("fill-opacity", 0.5)
  			.attr("fill", "#85C3C0")
  			.attr("stroke", "#222");
});

</script>

