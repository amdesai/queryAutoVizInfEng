    <div id="chart"></div>
    <div id="bars"></div>
    <style type="text/css">
		.axis path,
		.axis line {
			fill: none;
			stroke: black;
			shape-rendering: crispEdges;
		}
		.axis text {
			font-family: sans-serif;
			font-size: 11px;
		}
	</style>
    <script type="text/javascript">
        var width = 300,   // width of svg
            height = 300,  // height of svg
            padding = 20; // space around the chart, not including labels

            color = d3.scale.category20c();     //builtin range of colors
            var data  = <?php echo $data; ?>;
            
         	var data1=[	{"data":new Date(2012,0,1), "value": 3},
            			{"data":new Date(2012,0,3), "value": 2},
            			{"data":new Date(2012,0,12), "value": 33},   
            			{"data":new Date(2012,0,21), "value": 13},
            			{"data":new Date(2012,0,30), "value": 23}];
            
            
        var x_domain = d3.extent(data, function(d) { return d.data; }),
            y_domain = d3.extent(data, function(d) { return d.value; });


        var x_domain = d3.extent(data, function(d) { return d.data; }),
        y_domain = [0, d3.max(data, function(d) { return d.value; })];
        
        /* get
        y_domain: Array[2]
            0: 2
            1: 33
        x_domain: Array[2]
            0: Sun Jan 01 2012 00:00:00 GMT+0000 (GMT)
            1: Mon Jan 30 2012 00:00:00 GMT+0000 (GMT)
        */
        
        // display date format
        var  date_format = d3.time.format("%d %b");
        
        // create an svg container
        var vis = d3.select("#chart")
            		.append("svg:svg")
            		.data([data])
                	.attr("width", width)
                	.attr("height", height);
                
        // define the y scale  (vertical)
        var yScale = d3.scale.linear()
	        .domain(y_domain).nice()   // make axis end in round number
		.range([height - padding, padding]);   // map these to the chart height, less padding.  In this case 300 and 100
                 //REMEMBER: y axis range has the bigger number first because the y value of zero is at the top of chart and increases as you go down.
		    
            
        var xScale = d3.time.scale()
	        .domain(x_domain)    // values between for month of january
		    .range([padding, width - padding]);   // map these sides of the chart, in this case 100 and 600
		    
	
        // define the y axis
        var yAxis = d3.svg.axis()
            .orient("left")
            .scale(yScale);
        
        // define the x axis
        var xAxis = d3.svg.axis()
            .orient("bottom")
            .scale(xScale)
            .tickFormat(date_format);
            
        // draw y axis with labels and move in from the size by the amount of padding
        vis.append("g")
        	.attr("class", "axis")
            .attr("transform", "translate("+padding+",0)")
            .call(yAxis);

        // draw x axis with labels and move to the bottom of the chart area
        vis.append("g")
            .attr("class", "xaxis axis")  // two classes, one for css formatting, one for selection below
            .attr("transform", "translate(0," + (height - padding) + ")")
            .call(xAxis);
            
        // now rotate text on x axis
        // solution based on idea here: https://groups.google.com/forum/?fromgroups#!topic/d3-js/heOBPQF3sAY
        // first move the text left so no longer centered on the tick
        // then rotate up to get 45 degrees.
        vis.selectAll(".xaxis text")  // select all the text elements for the xaxis
          .attr("transform", function(d) {
             return "translate(" + this.getBBox().height*-2 + "," + this.getBBox().height + ")rotate(-45)";
         });
    
        // now add titles to the axes
        vis.append("text")
            .attr("text-anchor", "middle")  // this makes it easy to centre the text as the transform is applied to the anchor
            .attr("transform", "translate("+ (padding/2) +","+(height/2)+")rotate(-90)")  // text is drawn off the screen top left, move down and out and rotate
            .text("Value");

        vis.append("text")
            .attr("text-anchor", "middle")  // this makes it easy to centre the text as the transform is applied to the anchor
            .attr("transform", "translate("+ (width/2) +","+(height-(padding/3))+")")  // centre below axis
            .text("Data");
            
    </script>