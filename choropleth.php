<!DOCTYPE html>
<html>
<head>
    <?php include 'meta.php'; ?>
    <title>DPLA Visualizations</title>
    <link href='http://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="style.css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
    <script type="text/javascript" src="http://d3js.org/d3.v3.min.js"></script>
    <script type="text/javascript">
        $(function() {
            $('#d3-dpla').submit(function(e) {
                e.preventDefault();
                var q = $('#q').val();
                var message = $('#message');
                var hide = $('#hide');

                hide.removeClass('hide');

                if(q) {
                    $('svg, #records').detach();
                    $.getJSON("map-time.php?q=" + q, function(data) {
                        hide.addClass('hide');
                        message.text("Your search phrase: " + q);

                        var height = 600,
                            width = 850;

                        var color = d3.scale.quantize()
                            .domain([0, d3.max(data, function(d) { return d.value; })])
                            .range(["rgb(251,106,74)", "rgb(239,59,44)",
                                    "rgb(203,24,29)","rgb(153,0,13)"]);

                        var projection = d3.geo.albersUsa()
                            .translate([width/2, height/2])
                            .scale([1200]);

                        var path = d3.geo.path()
                            .projection(projection);

                        var div = d3.select("body").append("div")
                            .attr("class", "tooltip")
                            .style("opacity", 0);

                        var svg = d3.select("body").append("svg")
                            .attr("height", height)
                            .attr("width", width);


                        d3.json("us-states.json", function(json) {
                            for (var i = 0; i < data.length; i++) {
                                var dataState = data[i].state;
                                var dataValue = parseFloat(data[i].value);

                                //Find the corresponding state inside the GeoJSON
                                for (var j = 0; j < json.features.length; j++) {

                                    var jsonState = json.features[j].properties.name;

                                    if (dataState == jsonState) {
                                        json.features[j].properties.value = dataValue;
                                        json.features[j].properties.query = data[i].query;

                                        break;
                                    }
                                }
                            }

                            svg.selectAll("path")
                                .data(json.features)
                                .enter()
                                .append("path")
                                .attr("d", path)
                                .style("stroke", 6)
                                .style("fill", function(d) {
                                    var value = d.properties.value;

                                    if (value) {
                                        return color(value);
                                    } else {
                                        return "#ccc";
                                    }
                                }).on("mouseover", function(d) {
                                    div.transition()
                                        .duration(200)
                                        .style("opacity", .9);

                                    var count = (typeof d.properties.value === "undefined") ? 0 :d.properties.value;

                                    div .html("Your term(s) appeared in <br/>" + count + " records in " +
                                            d.properties.name
                                            + "<br/><br/>Click to view records for " + d.properties.name)
                                        .style("left", (d3.event.pageX - 28) + "px")
                                        .style("top", (d3.event.pageY - 28) + "px");
                                })
                                .on("mouseout", function() {
                                    div.transition()
                                        .duration(500)
                                        .style("opacity", 0);
                                })
                                .on("click", function(d) {
                                    window.open("http://dp.la/search?q=" + d.properties.query + "&state[]=" + d.properties.name,  '_blank');
                                });
                        });
                    });
                } else {
                    hide.addClass('hide');
                    message.text('Please submit a search term');
                }
            });
        });
    </script>
    <style type="text/css">
        path {
            stroke: gray;
            shape-rendering: crispEdges;
        }
        path:hover {
            fill: brown;
            fill-opacity: .7;
        }
    </style>
</head>
<body>
<?php include_once 'header.php'; ?>
<h1>DPLA Visualizations - See how your term(s) frequency varies by state</h1>
<p>Note: Depending on your search terms it can take some time to return the mapped items</p>
<p>Hover over a state to see how many items returned for that state</p>
<form action="#" method="post" id="d3-dpla">
    <input type="text" name="q" id="q" placeholder="Search the DPLA"/>
    <input type="submit" value="Search">
</form>
<p id="message"></p>
<img src="ajax-loader.gif" alt="load indicator" id="hide" class="hide" />
<?php include_once 'google.php'; ?>
</body>
</html>