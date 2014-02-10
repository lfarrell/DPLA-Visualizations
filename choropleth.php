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
                            width = 875,
                            color_range = ["rgb(254,217,118)",
                                "rgb(254,178,76)","rgb(253,141,60)",
                                "rgb(240,59,32)", "rgb(189,0,38)"];

                        var color = d3.scale.quantize()
                            .domain([0, d3.max(data, function(d) { return d.value; })])
                            .range(color_range);

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

                                    var count = (typeof d.properties.value === "undefined") ? 0 : formatCount(d.properties.value);

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

                            var all_colors = ["rgb(204, 204, 204)"].concat(color_range);

                            svg.append("text")
                                .attr("x", width - 60)
                                .attr("y", height - (height / 2) - 5)
                                .attr("height",30)
                                .attr("width",100)
                                .attr("dx", "-15px")
                                .style("fill", "black")
                                .style("text-anchor", "center")
                                .text("Fewer items");

                            var legend = svg.append("g")
                                .attr("class", "legend")
                                .attr("x", width - 60)
                                .attr("y", 10)
                                .attr("height", "auto")
                                .attr("width", 30);

                            legend.selectAll('g').data(all_colors)
                                .enter()
                                .append('g')
                                .each(function(d,i) {
                                    var g = d3.select(this);
                                    g.append("rect")
                                        .attr("x", width - 55)
                                        .attr("y", (height - height /2) + i * 25)
                                        .attr("width", 30)
                                        .attr("height", 30)
                                        .style("fill", d);
                                });

                            svg.append("text")
                                .attr("x", width - 60)
                                .attr("y", height - (height - 470))
                                .attr("height",30)
                                .attr("width",100)
                                .attr("dx", "-15px")
                                .style("fill", "black")
                                .style("text-anchor", "center")
                                .text("More items");
                        });
                    });
                } else {
                    hide.addClass('hide');
                    message.text('Please submit a search term');
                }
            });

            function formatCount(number) {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
        });
    </script>
    <style type="text/css">
        path {
            stroke: white;
            shape-rendering: crispEdges;
        }
        path:hover {
            fill: brown;
            fill-opacity: .7;
        }
        svg {
            margin-left: 15%;
        }

        text {
            font-size: 12px;
            font-family: Raleway, sans-serif;
        }
    </style>
</head>
<body>
<?php include_once 'header.php'; ?>
<h1>DPLA Visualizations - See how your term(s) frequency varies by state</h1>
<p>Note: Depending on your search terms it can take some time to return the mapped items</p>
<p>Hover over a state to see how many items were returned for that state</p>
<form action="#" method="post" id="d3-dpla">
    <input type="text" name="q" id="q" placeholder="Search the DPLA"/>
    <input type="submit" value="Search">
</form>
<p id="message"></p>
<img src="ajax-loader.gif" alt="load indicator" id="hide" class="hide" />
<?php include_once 'google.php'; ?>
</body>
</html>