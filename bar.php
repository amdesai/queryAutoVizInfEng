<div id="chart"></div>
<style>
    .fig {
      font-family:Arial;
      font-size:9pt;
      color:darkgray;
    }
</style>
<script type="text/javascript">
	
	var data  = <?php echo $data; ?>;

	var count = <?php echo $count; ?>;
	
    var mySVG = d3.select("#chart")
      .append("svg")
//      .attr("viewBox", "0 0 500 500");
      .attr("width", 30 * count) 
      .attr("height", 650)
      .style('position','center')
      .style('top',50)
      .style('left',0)
      .attr('class','fig');

    var heightScale = d3.scale.linear()
      .domain([0, d3.max(data,function(d) { return d.value;})])
      .range([0, 400]);

    mySVG.selectAll(".xLabel")
      .data(data)
      .enter().append("svg:text")
      .attr("x", function(d,i) {return 113 + (i * 22);})
      .attr("y", 435)
      .attr("text-anchor", "end") 
      .text(function(d,i) {return d.data;})
      .attr('transform',function(d,i) {return 'rotate(-90,' + (113 + (i * 22)) + ',435)';}); 

    mySVG.selectAll(".yLabel")
      .data(heightScale.ticks(10))
      .enter().append("svg:text")
      .attr('x',80)
      .attr('y',function(d) {return 400 - heightScale(d);})
      .attr("text-anchor", "end") 
      .text(function(d) {return d;}); 

    mySVG.selectAll(".yTicks")
      .data(heightScale.ticks(10))
      .enter().append("svg:line")
      .attr('x1','90')
      .attr('y1',function(d) {return 400 - heightScale(d);})
      .attr('x2',((28 * count) - 60))
      .attr('y2',function(d) {return 400 - heightScale(d);})
      .style('stroke','lightgray'); 

    var myBars = mySVG.selectAll('rect')
      .data(data)
      .enter()
      .append('svg:rect')
      .attr('width',20)
      .attr('height',function(d,i) {return heightScale(d.value);})
      .attr('x',function(d,i) {return (i * 22) + 100;})
      .attr('y',function(d,i) {return 400 - heightScale(d.value);})
      .style('fill','lightblue');
</script>
