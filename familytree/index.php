<!DOCTYPE html>
<html>
<head>
<title>Family Tree</title>
<meta charset="utf-8">
<style>
.node {
  cursor: pointer;
}

.node circle {
  fill: #fff;
  stroke: steelblue;
  stroke-width: 1.5px;
}

.node circle {
  fill: #fff;
  stroke: steelblue;
  stroke-width: 1.5px;
  margin-bottom: 10px;
}

.node text {
  font: 10px sans-serif;
}

.linkchild {
  fill: none;
  stroke: #ccc;
  stroke-width: 1.5px;
}

.linkmarried {
  fill: none;
  stroke: #666;
  stroke-width: 1.5px;
}

div.tooltip {
    position: absolute;
    text-align: left;
    padding: 3px;
    font: 12px sans-serif;
    background: #fff;
    border: solid 3px #fff;
    color: #333;
    pointer-events: none;
    border-radius: 4px;
	box-shadow: 0px 0px 5px #888888;
}
div.tooltip div.profile{
	display: block;
    width: 60px;
    height: 60px;
    border-radius: 33px;
    border: solid 3px #F76;
	position: relative;
    top: -30px;
    margin-left: auto;
    margin-right: auto;
	
	background-repeat: no-repeat;
    background-position: center; 
    background-size:cover;
}
div.tooltip div.info{
	position:relative;
	top:-10px;
}
</style>
</head>
<body>
<div><form method="get">Choose Family: <select id="familyid" name="familyid"></select><input type="Submit" value="Show Family tree" /></form></div>
<script type="text/javascript" src="d3.v3.min.js"></script>
<script type="text/javascript">

var margin = {top: 5, right: 50, bottom: 5, left: 70},
    width = 1000 - margin.right - margin.left,
    height = 650 - margin.top - margin.bottom;

var i = 0,
    duration = 750,
    root;

var tree = d3.layout.tree()
    .size([height, width]);

var diagonal = d3.svg.diagonal()
    .projection(function(d) { return [d.y, d.x]; });

var lineFunction = d3.svg.line()
                            .x(function(d) { return d.x; })
                            .y(function(d) { return d.y; })
                            .interpolate("linear");

var svg = d3.select("body").append("svg")
    .attr("width", width + margin.right + margin.left)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

// Add tooltip div
var div = d3.select("body").append("div")
.attr("class", "tooltip")
.style("opacity", 0);

function loaddata(flare) {
  root = flare;
  root.x0 = height / 2;
  root.y0 = 0;

  function collapse(d) {
    if (d.children) {
      d._children = d.children;
      d._children.forEach(collapse);
      d.children = null;
    }
  }
    if(root.children){
    root.children.forEach(collapse);
    }
  update(root);
}

d3.select(self.frameElement).style("height", "800px");

function update(source) {
  //console.log(source);
  // Compute the new tree layout.
  var nodes = tree.nodes(root).reverse(),
      links = tree.links(nodes);

  // Normalize for fixed-depth.
  nodes.forEach(function(d) { d.y = d.depth * 180; });

  // Update the nodes…
  var node = svg.selectAll("g.node")
      .data(nodes, function(d) { return d.id || (d.id = ++i); });

  // Enter any new nodes at the parent's previous position.
  var nodeEnter = node.enter().append("g")
      .attr("class", "node")
      .attr("transform", function(d) { return "translate(" + source.y0 + "," + source.x0 + ")"; })
      .on("click", click);

  nodeEnter.append("circle")
      .attr("class", "parent")
      .attr("r", 1e-6)
      .style("fill", function(d) { return d.haschild > 0 ? "lightsteelblue" : "#fff"; })
      .on("mouseover", mouseover)
      .on("mouseout",mouseout);
  nodeEnter.append("text")
      .attr("class", "parent")
      .attr("x", function(d) { return d.haschild > 0 ? -10 : 10; })
      .attr("dy", ".35em")
      .attr("text-anchor", function(d) { return d.haschild > 0 ? "end" : "start"; })
      .text(function(d) { return d.name; })
      .style("fill-opacity", 1e-6)
      .on("mouseover", mouseover)
      .on("mouseout",mouseout);

  nodeEnter.append("circle")
      .attr("class", "married")
      .attr("r", function(d) { return d.haschild > 0 ? 0 : 1e-6; })
      .attr("transform", function(d) { return "translate(0,-20)"; })
      .style("fill", function(d) { return d.haschild > 0 ? "lightsteelblue" : "#fff"; })
      .style("opacity", 1e-6)
      .on("mouseover", mouseoverpartner)
      .on("mouseout",mouseoutpartner);

  nodeEnter.append("text")
      .attr("class", "married")
      .attr("x", function(d) { return d.haschild > 0 ? -10 : 10; })
      .attr("dy", ".35em")
      .attr("text-anchor", function(d) { return d.haschild > 0 ? "end" : "start"; })
      .text(function(d) { return d.partner; })
      .attr("transform", function(d) { return "translate(0,-20)"; })
      .style("fill-opacity", 1e-6)
      .on("mouseover", mouseoverpartner)
      .on("mouseout",mouseoutpartner);

  nodeEnter.append("path")
      .attr("class", "linkmarried")
      .attr("d", function(d){ 
          var gd = [{ "x": 0 ,   "y": (0) },  { "x": 0,  "y": -20 }];
          if(d.partner != null){
            return lineFunction(gd); 
          }
          });
  
  // Transition nodes to their new position.
  var nodeUpdate = node.transition()
      .duration(duration)
      .attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; });

  nodeUpdate.select("circle.married")
      //.attr("r", 4.5)
      .attr("r", function(d) { return d.partner != null ? 4.5 : 1e-6; })
      //.attr("transform", function(d) { return "translate(" + (d.y + 10) + "," + d.x + ")"; })
      .style("fill", function(d) { return d.haschild > 0 ? "lightsteelblue" : "#fff"; })
      .style("opacity", function(d) { return d.partner != null ? 1 : 1e-6; });

  nodeUpdate.select("circle.parent")
      .attr("r", 4.5)
      .style("fill", function(d) { return d.haschild > 0 ? "lightsteelblue" : "#fff"; });

  nodeUpdate.select("text.parent")
      .style("fill-opacity", 1);

  nodeUpdate.select("text.married")
      .style("fill-opacity", function(d) { return d.partner != null ? 1 : 1e-6; });

  // Transition exiting nodes to the parent's new position.
  var nodeExit = node.exit().transition()
      .duration(duration)
      .attr("transform", function(d) { return "translate(" + source.y + "," + source.x + ")"; })
      .remove();

  nodeExit.select("circle.parent")
      .attr("r", 1e-6);

  nodeExit.select("circle.married")
      .attr("r", 1e-6);

  nodeExit.select("text.parent")
      .style("fill-opacity", 1e-6);

  nodeExit.select("text.married")
      .style("fill-opacity", 1e-6);

  // Update the links…
  var link = svg.selectAll("path.linkchild")
      .data(links, function(d) { return d.target.id; });

  // Enter any new links at the parent's previous position.
  link.enter().insert("path", "g")
      .attr("class", "linkchild")
      .attr("d", function(d) {
        var o = {x: source.x0, y: source.y0};
        return diagonal({source: o, target: o});
      });

  // Transition links to their new position.
  link.transition()
      .duration(duration)
      .attr("d", diagonal);

  // Transition exiting nodes to the parent's new position.
  link.exit().transition()
      .duration(duration)
      .attr("d", function(d) {
        var o = {x: source.x, y: source.y};
        return diagonal({source: o, target: o});
      })
      .remove();

  // Stash the old positions for transition.
  nodes.forEach(function(d) {
    d.x0 = d.x;
    d.y0 = d.y;
  });
}

function mouseover(d) {
    div.style("left", (d3.event.pageX+10 ) + "px")
    .style("top", (d3.event.pageY-20) + "px")
    .style("opacity", 1);
	div.html("<div class='profile' style=\"background-image: url('"+ d.profilepic +"')\"></div>"
	+ "<div class='info'>Family name: "+ d.familyname
	+ "<br>Nick name: " + d.nickname 
    + "<br>Date of birth: " + (d.dob==null?"?":d.dob)
    + (d.dod==null? "": "<br>Date of Death: " + d.dod)
    + "<br>Age: "+ (d.age==null?"?":d.age)
	+ "<br>Gender: " + d.gender
	+ "</div>"
    ) ;
}

// Toggle children on click.
function mouseout(d) {
    div.transition().duration(300).style("opacity", 0);
}

function mouseoverpartner(d) {
    div.style("left", (d3.event.pageX+10 ) + "px")
    .style("top", (d3.event.pageY-20) + "px")
    .style("opacity", 1);
    div.html("<div class='profile' style=\"background-image: url('"+ d.partner_profilepic +"')\"></div>"
	+ "<div class='info'>Family name: "+ d.partner_familyname
	+ "<br>Nick name: " + d.partner_nickname 
    + "<br>Date of birth: " + (d.partner_dateofbirth==null?"?":d.partner_dateofbirth)
	+ (d.partner_dateofdeath==null? "": "<br>Date of Death: " + d.partner_dateofdeath)
    + "<br>Age: "+ (d.partner_age==null?"?":d.partner_age)
	+ "<br>Gender: " + d.partner_gender
	+ "</div>"
    ) ;
}

// Toggle children on click.
function mouseoutpartner(d) {
    div.transition().duration(300).style("opacity", 0);
}

// Toggle children on click.
function click(d) {
  if (!d.children && !d._children) {
        loadChilder(d.id,function(childObjects){
            childObjects.forEach(function(node) {
                if(node.name != d.name){
                    (d._children || (d._children = [])).push(node);
                }
            });
            if (d.children) {
                d._children = d.children;
                d.children = null;
            } else {
                d.children = 
                d.children = d._children;
                d._children = null;
            }
            update(d);
        });
  }else{
        if (d.children) {
            d._children = d.children;
            d.children = null;
        } else {
            d.children = d._children;
            d._children = null;
        }
        update(d);
  }
}
</script>
<script type="text/javascript" src="jquery-3.2.1.min.js"></script>
<script>
var familyid = <?php if(isset($_GET["familyid"])){ echo $_GET["familyid"]; }else{ echo 2;} ?>;
var settings = {
  "async": true,
  "crossDomain": true,
  "url": "get_master_node.php?familyid="+familyid,
  "method": "GET"
}

$.ajax(settings).done(function (response) {
  $.each(response, function(k,v){
        loaddata(v);
  });
});

function loadChilder(id,callbk){
    var settings = {
    "async": false,
    "crossDomain": true,
    "url": "get_child_of_node.php?id="+id,
    "method": "GET"
    }

    $.ajax(settings).done(function (response) {
        callbk(response);
    });
}



settings = {
  "async": true,
  "crossDomain": true,
  "url": "get_all_families.php",
  "method": "GET"
}

$.ajax(settings).done(function (response) {
  $.each(response, function(k,v){
        $("#familyid").append("<option value='"+ v.idfamily +"'>"+ v.familyname +"</option>");
  });
  $("#familyid").val(familyid);
});
</script>

</body>
</html>