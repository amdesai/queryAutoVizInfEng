<div id="chart"></div>
<div id="bars"></div>

 <style>
	.bar_chart rect {
		stroke: white;
		fill: steelBlue;
	}
	.bar_chart text {
		color: white;
		font: 10px sans-serif;
	}
	#module_2 {
		float: right;
	}
</style>

 
 
<script>

	bar_chart("220","400",data,"1")();


	function bar_chart(height,width,data,mod) {
	 var height = 220,
	 width = 400;
	  
	 function my() {
	 var bar_chart = d3.select("body").append("svg")
	 .attr("id", "mod-"+mod+"-bar-chart")
	 .attr("class", "bar_chart")
	 .attr("width", width)
	 .attr("height", height)
	 .append("g")
	 .attr("transform", "translate(10,15)")
	  
	 var x = d3.scale.linear()
	 .domain([0, d3.max("x", function(d) { return d.value; } )])
	 .range([0, 420]);
	 console.log("x: " + x[0]);
	 var y = d3.scale.ordinal()
	 .domain([9])
	 .rangeBands([0, 120]);
	  
	 bar_chart.selectAll("line")
	 .data(x.ticks(10))
	 .enter().append("line")
	 .attr("x1", x)
	 .attr("x2", x)
	 .attr("y1", 0)
	 .attr("y2", 120)
	 .style("stroke", "#ccc");
	 bar_chart.selectAll("rect")
	 .data(data)
	 .enter().append("rect")
	 .attr("y", y)
	 .attr("width", x)
	 .attr("height", y.rangeBand());
	 /*
	 bar_chart.selectAll("text")
	 .data(function(d) { metric1(ret)
	 .enter().append("text")
	 .attr("x", x)
	 .attr("y", function(d) { return y(d) + y.rangeBand() / 2; })
	 .attr("dx", -3) // padding-right
	 .attr("dy", ".35em") // vertical-align: middle
	 .attr("text-anchor", "end") // text-align: right
	 .attr("fill","white")
	 .text(String);
	 */
	 bar_chart.append("line")
	 .attr("y1", 0)
	 .attr("y2", 120)
	 .style("stroke", "#000");
	  
	 bar_chart.selectAll(".rule")
	 .data(x.ticks(10))
	 .enter().append("text")
	 .attr("class", "rule")
	 .attr("x", x)
	 .attr("y", 0)
	 .attr("dy", -3)
	 .attr("text-anchor", "middle")
	 .text(String);
	 }
	  
	 my.width = function(value) {
	 if (!arguments.length) return width;
	 width = value;
	 return my;
	 };
	  
	 my.height = function(value) {
	 if (!arguments.length) return height;
	 height = value;
	 return my;
	 };
	  
	 my.data = function(value) {
	 if (!arguments.length) return data;
	 data = value;
	 return my;
	 }
	  
	 return my;
	 }
 </script>
