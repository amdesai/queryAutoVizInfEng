var mainArray = new Array();    //This array gets the records from csv file
var headerArray = new Array();  //This array only has headers info
var tableIndex = 0;             //Constant that represents the location of table Index in the breadcrumb name;
var headIndex = 1;              // Constant that represents the location of head/field name in the breadcrumb name;
var valueIndex = 2;             // Constant that represents the location of value in the breadcrumb name;
var conditionArray = new Array();//This array helps make the "where" condition from the selected attributes
var matchedArray = new Array(); // This Array will have the rows from main array which match the condition
var isInserted = new Array();   //This array keeps a track of the fields inserted
var minHeight;                  // This is the min height the drop area can have
var tableArray = new Array();
var tableCount = new Array();
var joinField;

function dndObject(value,fieldNumber)
{
    this.fieldNumber = fieldNumber;
    this.value = value;
}

$(document).ready(function(){
    //loadjQueryHandler();
    loadjQueryHandlerNew();
    //readCSV();
    minHeight = $("#drop").height();
    $( "#tabs" ).tabs({
        heightStyle: "fill"
    });
    // $('#tabs-1').css('height','150%');
    $("#dialog").dialog({
        autoOpen:false,
        modal:true,
        width:500,
        buttons:{
            Ok:function(){
                $(this).dialog("close");
//                alert(joinField);
            }
        }
    });
});

function loadjQueryHandlerNew(){
    $( "#drop" ).sortable({
        revert: true,
        grid: [ 20, 10 ],
        update:function(event,ui){
            var title = $(ui.item).html(); // At this moment html() will only be the attribute being dragged 
            var name  = $(ui.item).attr("name");
            $(ui.item).addClass("head")
            var title = "<span class=''>"+title+"</span>";
            var collapse = "<span id='wrapper'><button class='collapse' name='"+name+"|collapse'></button>";
            var remove = "<button class='remove' name='"+name+"|remove'></button></span>";           
            // var selectedOption = "<span id='selectedOption'>Muaz</span>" 
            var title = "<div style='width:100%'>"+title+collapse+remove+"</div>";
            var tmp = name.split("~");
            addTable(tmp[tableIndex]); //This function will check if field dropped is from a different table or existing tables
            var index = tmp[headIndex];
            var loader = "<ul class='options' style='border:none' ><li style='text-align:center;border:none' ><img src='img/350.gif' alt='loading..' /></li></ul> ";	
            $(ui.item).html(title+loader); 	
            $.ajax({
                type:"POST",
                url:"db_calls.php",
                data:{
                    "task":"getUniqueFieldValues",
                    "table":tmp[tableIndex],
                    "field":tmp[headIndex]
                }
            }).done(function ( response ) {
                $(ui.item).html(title+response); 
                adjustHeights();
                $( ".remove" ).button({
                    icons: {
                        primary: "ui-icon-close"
                    },
                    text: false
                });
                $( ".collapse" ).button({
                    icons: {
                        primary: "ui-icon-minus"
                    },
                    text: false
                });
            });

        },
        receive:function(event,ui){
            $(ui.item).draggable("disable");
        }
    });
    $("#fieldList ul li ul li").draggable({
        connectToSortable: "#drop",
        containment: 'frame',
        helper:'clone',
        revert: 'invalid',
        scroll: false,
        zIndex: 100,
        start: function(event, ui)
        {
            $("#dropContainer").addClass("hilight");
        },
        stop: function(event, ui)
        {
            $("#dropContainer").removeClass("hilight");
        }

    });
}
function adjustHeights(){
/* var newHeight = $("#drop").height();
    if(newHeight<minHeight)
    {
        $("#tabs").height(minHeight+200);
        $("#tabs-1").height(minHeight+100);
    }else{
        $("#tabs").height(newHeight+200);
        $("#tabs-1").height(newHeight+100);
    }*/
    
}
/**
 * This will find the main UL element and remove it 
 **/
$(".remove").live("click",function(){
    //var t = $(this).parent().parent().parent();
    var name = $(this).attr("name");
    name = name.split("|")[0];
    $("#fieldList li[name='"+name+"']").draggable("enable"); // Enable drag in fieldlist 
    var tmp = name.split("~");
    var index = tmp[headIndex];
    isInserted[index] = false;
    $("#indvCount"+index).remove();
    $("#drop li[name='"+name+"']").remove(); // remove it from drop area
    adjustHeights();
    var str = $(this).html();
    var numtest = $(".selected").length;
    removeTable(tmp[tableIndex]);
    //    tableArray.splice(tableArray.indexOf(tmp[tableIndex]), 1); //Remove
    if (numtest==0){
        $('#stats').html(""); //Clear HTML if no fields are left.
        return;
    }
    else{
        addStats(str,index); //Update stats after removing if not last field removed
    }
});  

/** 
 *  This Function is repsonsible to hide/show of options
 ***/
$(".collapse").live("click",function(){
    var name = $(this).attr("name");
    name = name.split("|")[0];
    var ulOptions = $("#drop li[name='"+name+"']").children(".options").children("li .selected");
    showHideOptions($(ulOptions).siblings(),$(this));
    adjustHeights();
});

/**
 *   This will get the list of all LI elements based on the one being clicked
 *  Remove selected class from all of LI
 *  Apply selected class on clicked element
 **/
$(".options li").live("click",function(){
    $(this).siblings().removeClass("selected");
    $(this).addClass("selected");
    var name  = $(this).attr("name");
    var tmp = name.split("~");
    var index = tmp[headIndex];
    var liName = extractLiName(tmp); 
    var str = $(this).html();
    addStats(str,index); //String to be found Index from which to be found 
    var button =  $("#drop li[name='"+liName+"']").find(".collapse"); //Get the Collapse button of this field 
    showHideOptions($(this).siblings(),button);
}); 
/**
 *
 *  This function will keep a track of all the tables for which a field has been dropped  
 *  
 **/
function addTable(tableName){
    if(tableArray.indexOf(tableName)>=0){
        tableCount[tableName]++;
    }
    else{
        tableArray.push(tableName);
        tableCount[tableName] = 1;
        if(tableArray.length>1){
            $.ajax({
                type:"POST",
                url:"db_calls.php",
                data:{
                    "task":"getJoinFields",
                    "tables":tableArray
                }
            }).done(function ( response ) {
                $("#possibleJoinFields").html(response);
                $("input[name='joinFields']")[0].click();
                $("#dialog").dialog("open");
            });
        }
    }
    console.log(tableCount[tableName]);
}

function removeTable(tableName){
    tableCount[tableName]--;
    if(tableCount[tableName]==0){
        tableArray.splice(tableArray.indexOf(tableName), 1); //Remove
    }
    console.log(tableCount[tableName]);
}

function updateJoinField(t){
    joinField = t.value;
}

/**
 *  This function will hide/show options. It will be called when a option is selected or collapse button is clicked
 *  Remove selected class from all of LI
 *  Apply selected class on clicked element
 **/
function showHideOptions(options,button){
    if($(button).data('state') == "arm"){
        console.log("show");
        $(options).show("slow");
        $(button).data('state', "disarm");
        $(button).button({
            icons: {
                primary: "ui-icon-minus"
            }
        });
    }else{
        console.log("hide");
        $(options).hide('slow');
        //$(options).children(".selected").show("slow");
        $(options).find(".selected").show("slow");
        $(button).data('state', "arm");
        $(button).button({
            icons: {
                primary: "ui-icon-plus"
            }
        });
        
    }
}

function extractLiName(arr){
    return arr[tableIndex]+"~"+arr[headIndex];
}

/*function sort_unique(arr) {
    arr = arr.sort();
    var ret = [arr[0]];
    for (var i = 1; i < arr.length; i++) { // start loop at 1 as element 0 can never be a duplicate
        if (arr[i-1] !== arr[i]) {
            ret.push(arr[i]);
        }
    }
    return ret;
}*/
function sortUniqueAndCount(arr){
    var a = [], b = [], prev;
    
    arr.sort();
    for ( var i = 0; i < arr.length; i++ ) {
        if ( arr[i] !== prev ) {
            a.push(arr[i]);
            b.push(1);
        } else {
            b[b.length-1]++;
        }
        prev = arr[i];
    }
    var data = Array();
    for(var j=0; j<a.length-1;j++){
        data.push({
            "label":a[j],
            "value":b[j]
        });
    }
    /* console.log(a);
    console.log(b);
    console.log(data);*/
    return data;
}

function drawChart(){
    var w = 300,                        //width
    h = 300,                            //height
    r = 100,                            //radius
    color = d3.scale.category20c();     //builtin range of colors
    var data  = sortUniqueAndCount(matchedArray);
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
    });        //get the label from our original data array
}
            
        
function addStats(str,index){
    $("#stats").html("<div style='text-align: center;vertical-align: middle;padding-top: 100px' ><img src='img/285.gif' alt='Loading...'/></div>");
    var selected = "&name=";
    $(".selected").each(function(i,t){
        selected += $(t).attr("name");
        if(i != $(".selected").length-1)
            selected += "|";
    });
    //console.log(selected);
    $.ajax({
        type:"POST",
        url:"db_calls.php",
        data:"task=generateStatistics"+selected+"&joinField="+joinField
    }).done(function ( response ) {
        //alert(response);
        $("#stats").html(response);
    });
    
/* alert(str);
    alert(index);
    var count = countOccurrences(str,index);
    var html = "<div id='indvCount"+index+"'><b>"+index+"</b> "+str+" N = "+count+"</div>";
    if($("#indvCount"+index).length>0)
    {
        $("#indvCount"+index).html(html); 
        replaceCondition(str,index)
    }else{
        $("#indvCount").append(html);
        conditionArray.push(new dndObject(str,index));
    }
    //countCommonOccurrences(); 
    $("#chart").html("<p>Male/Female Ratio</p>");
    drawChart();*/
}        
/*********************************************************************************/           
/* 
 * The following functions are for demo purpose only, They should eventualy be
 *  replacted with Ajax calls for SQL querying
 *  */           
/*********************************************************************************/   
function replaceCondition(str,index){
    for(var i=0;i<conditionArray.length;i++){
        if(conditionArray[i].fieldNumber == index){
            conditionArray[i] = new dndObject(str,index);
        }
    }
}

function countOccurrences(str,index){
    return 100;
    var count = 0;
    var temp = mainArray[index];
    for(var i=0;i<temp.length;i++){
        if(temp[i]==str){
            count++;
        }
    }
    return count;
}
function countCommonOccurrences(){
    var condition ="";
    for(var i=0;i<conditionArray.length;i++){
        condition += "mainArray['"+conditionArray[i].fieldNumber+"'][i]=='"+conditionArray[i].value+"'";
        if(i<conditionArray.length-1){
            condition += " && ";
        }
    }
    console.log(headerArray);
    var count = 0;
    for(var i=0;i<mainArray[headerArray[1]].length;i++){
        if(eval(condition)){
            count++;
            matchedArray[i]=mainArray[headerArray[8]][i];
        }
        
    }
    var html = "<div><b>Matched</b> N = "+count+"</div>";
    $("#matchCount").html(html);
}

function readCSV(){      
    console.log("here");
    $.ajax({
        type:"GET",
        url:"../data/ticket41_nopatientid.csv",
        success:function(data){
            var temp = data.split("\n");
            var fields = temp[0].split(",");
            var innerList = "";
            for(var i=1;i<fields.length;i++){
                var head = fields[i].replace(/"/g,'')
                if(i<fields.length-3){
                    innerList += "<li name='table1_"+head+"'>"+head+"</li>";
                }
                headerArray[i] = head;
                mainArray[head] = new Array();
                isInserted[head] = false;
            }
            
            $("#fields").html(innerList);
            for(var i=1;i<temp.length;i++){
                var fields = temp[i].split(",");
                for(var j=1;j<fields.length;j++){
                    mainArray[headerArray[j]].push(fields[j].replace(/"/g,'')); 
                }
            }
            //console.log(mainArray);
            loadjQueryHandler();
        }
    })
}   
 