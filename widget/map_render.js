
function map_widgetlist()
{
  var widgets = {
    "map":
    {
      "width":450,"height":400,
      "menu":"Widgets"
    }
  }
  return widgets;
}

function map_init()
{
  //setup_widget_canvas('node');
  $("#page").html('<div class="map"></div>');
  $("#page").html('<script type="text/javascript" src="http://d3js.org/d3.v3.js"></script>');
  draw_map();
}

function map_slowupdate()
{
}

function map_fastupdate()
{
}

function draw_map() {
	var width = 450,
		height = 400,
		radius = 4;

	var color = d3.scale.category10();

	var force = d3.layout.force()
		.size([width, height]);

	var svg = d3.select(".map").append("svg")
		.attr("width", width)
		.attr("height", height);
		
	/** Returns an event handler for fading a given node group. */
	function fade_nodes(opacity) {
		return function(g, i) {
			svg.selectAll(".node")
			   .filter(function(d) {
					return d.typeid != i+1;
					
				})
				.transition()
				.style("opacity", opacity);
		};
	}
	
	function icon(name) {
		imagepath = path+"Modules/map/maps/";
		
		if(name.indexOf("temperature") !== -1) {
			imagepath += "temperature";
		}
		else if(name.indexOf("power") !== -1) {
			imagepath += "power";
		}
		else if(name.indexOf("kwhd") !== -1) {
			imagepath += "kwhd";
		}
		else if(name.indexOf("humidity") !== -1) {
			imagepath += "humidity";
		}
		else return null;
		
		imagepath += ".svg";
		return imagepath;
		
		
	}
		
	function draw_legend() {
		d3.json(path + "map/types.json", function(error, types) {
			var legend = svg.selectAll(".legend")
							.data(d3.entries(types))
						  .enter().append("g")
							.attr("class", "legend")
							.attr("transform", function(d, i) { return "translate(0," + i * 20 + ")"; });
							
			legend.append("rect")
				  .attr("x", width - 16)
				  .attr("y", 1)
				  .attr("width", 14)
				  .attr("height", 15)
				  .attr("rx", 3)
				  .attr("ry", 3)
				  .style("fill", function(d) { return d3.rgb(color(d.key)).brighter(); })
				  .style("stroke", function(d) { return color(d.key); })
				  .style("stroke-width", "1.5px")
				  .on("mouseover", fade_nodes(.1))
				  .on("mouseout", fade_nodes(1));
				  
			legend.append("text")
				  .attr("x", width - 20)
				  .attr("y", 9)
				  .attr("dy", ".35em")
				  .style("text-anchor", "end")
				  .text(function(d) { return d.value; });
		});
	}
		
	svg.append("image")
		.attr("width", width)
		.attr("height", height)
		.attr("xlink:href", path+"Modules/map/maps/" + apikey + ".svg");
	
	var drag = d3.behavior.drag()
	    .origin(Object)
	    .on("drag", dragmove)
	    .on("dragend", dragend);	
		
	function dragmove(d) {
	  d.x = Math.max(radius, Math.min(width - radius, d3.event.x));
	  d.y = Math.max(radius, Math.min(height - radius, d3.event.y));
	  d3.select(this)
	  	.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
	  
	}
	
	function dragend(d) {

    $.ajax({ url: path+"map/set.json", data: "id="+d.id+"&fields="+JSON.stringify({'x':d.x,'y':d.y}), async: false, success: function(data){} });

		d3.select(this)
	  	.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
          table.data = map.list();
          table.draw();
	}

	d3.json(path + "map/nodes.json", function(error, graph) {
    console.log(graph);
	  force
		  .nodes(graph.nodes)
		  .links(graph.links);
		  
	  var link = svg.selectAll(".link")
		  .data(graph.links)
		.enter().append("line")
		  .attr("class", "link")
		  .style("stroke-width", function(d) { return Math.sqrt(d.value); });

	  var node = svg.selectAll(".node")
		  .data(graph.nodes)
		.enter().append("g")
		  .attr("class", "node")
		  .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")" ; })
		  .on("mouseover", function(d) { d3.selectAll(this.childNodes).style("opacity", 1); })
		  .on("mouseout", function(d) { d3.selectAll(this.childNodes).filter(":not(:first-child)").style("opacity", 0); d3.select(this.firstChild).style("opacity", 1); })
		  .call(drag);
		  
	  node.append("circle")
	  	.attr("r", radius)
	  	.style("fill", function(d) { return d3.rgb(color(d.typeid)).brighter(); })
	  	.style("stroke", function(d) { return color(d.typeid); })
	  	.style("stroke-width", "1.5px");
	  	
	  node.append("text")
	  	.attr("dx", 6)
	  	.attr("dy", ".35em")
	  	.attr("opacity", 0)
	  	.text(function(d) { return d.hostname });
	  	
	  var test = node.selectAll(".text")
		    .data(function(d) { return d.feedlist; })
		  .enter().append("image")
		    .attr("x", function(d, i) { return i * 10 + radius; } )
		    .attr("y", 8)
		    .attr("width", 10)
		    .attr("height", 18)
		    .attr("opacity", 0)
		    .attr("xlink:href", function(d) { return icon(d.name); } )
		    .on("click",  function(d) {  window.open(path + "vis/auto?feedid=" + d.id, '_blank'); });
	  	

	  node.append("title")
		  .text(function(d) { return d.hostname; });
		  
	  draw_legend();
	});	  
}
