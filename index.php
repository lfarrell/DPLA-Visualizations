<!DOCTYPE html>
<html>
<head>
    <title>DPLA Visualizations</title>
    <link rel="stylesheet" href="style.css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
    <script type="text/javascript" src="http://d3js.org/d3.v3.min.js"></script>
    <script type="text/javascript">
        $(function() {
            $('#d3-dpla').submit(function(e) {
                e.preventDefault();
                var q = $('#q').val();
                var message = $('#message').text("Your search phrase: " + q);
                $('#hide').removeClass('hide');

                if(q) {
                    $('svg').detach();
                    $.getJSON("DplaHistogram.php?q=" + q, function(data) {
                        var margin = {top: 30, right: 20, bottom: 50, left: 26},
                            axisPadding = 5,
                            height = 450 - margin.top - margin.bottom,
                            width = 1100 - margin.left - margin.right;

                        var ticks = [];
                        for(var i=0; i<data.length; i++) {
                            ticks.push(data[i].decade);
                        }

                        var div = d3.select("body").append("div")
                            .attr("class", "tooltip")
                            .style("opacity", 0);

                        var svg = d3.select("body").append("svg")
                                .attr("height", height + margin.top)
                                .attr("width", width);

                        var xScale = d3.scale.ordinal()
                                .domain(ticks)
                                .rangeRoundBands([margin.right, width], 0.05);

                        var yScale = d3.scale.linear()
                                .domain([0,d3.max(data, function(d) { return d.count; })])
                                .range([0, height]);

                        var yTickScale = d3.scale.linear()
                                .domain([d3.max(data, function(d) { return d.count; }), 0])
                                .range([0, height]);

                        svg.selectAll("rect")
                                .data(data, function(d) {
                                    return d.decade;
                                })
                                .enter()
                                .append("rect")
                                .attr("x", function(d) {
                                    return xScale(d.decade);
                                })
                                .attr("y", function(d) {
                                    return height - yScale(d.count) + axisPadding;
                                })
                                .attr("width", xScale.rangeBand())
                                .attr("height", function(d) {
                                    return yScale(d.count);
                                })
                                .attr("fill", "steelblue")
                                .on("mouseover", function(d) {
                                    div.transition()
                                        .duration(200)
                                        .style("opacity", .9);

                                    div .html("Your term(s) appeared in <br/>" + d.count + " records in the "  + d.decade + "'s"
                                        + "<br/><br/>Click highlighted bar to view records")
                                        .style("left", (d3.event.pageX - 28) + "px")
                                        .style("top", (d3.event.pageY - 28) + "px");
                                })
                                .on("mouseout", function() {
                                    div.transition()
                                        .duration(500)
                                        .style("opacity", 0);
                                })
                                .on("click", function(d) {
                                    $.get("DplaHistogram.php?q=" + q + "&decade=" + d.decade, function(data) {
                                        if($('#records').length === 0) {
                                            d3.select("body").append("div")
                                                .attr("id", "records");
                                        }
                                        $('#records').html(data);
                                    });
                                });

                        var xAxis = d3.svg.axis()
                                .scale(xScale)
                                .orient("bottom");

                        svg.append("g")
                                .attr("class", "axis")
                                .attr("transform", "translate(0," + (height + axisPadding) + ")")
                                .call(xAxis);

                        var yAxis = d3.svg.axis()
                                .scale(yTickScale)
                                .orient("left");

                        svg.append("g")
                                .attr("class", "axis")
                                .attr("transform", "translate(" + margin.left + "," + axisPadding +")")
                                .call(yAxis);
                    });
                } else {
                    $('#message').text('Please submit a search term');
                }

                setTimeout(function() {
                    $('#hide').addClass('hide');
                }, 1800);
            });
        });
    </script>
    <style type="text/css">
        .axis path,
        .axis line {
            fill: none;
            stroke: black;
            shape-rendering: crispEdges;
            padding: 5px;
        }

        .axis text {
            font-family: sans-serif;
            font-size: 11px;
        }

        rect:hover {
            fill: orange;
        }

        div.tooltip {
            position: absolute;
            text-align: center;
            width:  auto;
            height: auto;
            padding: 5px;
            fill: black;
            font: 12px sans-serif;
            background: lightgray;
            border: 0px;
            border-radius: 8px;
            pointer-events: none;
        }

        ul a {
            text-decoration: none;
            color: steelblue;
        }
    </style>
</head>
<body>
<?php include_once 'header.php'; ?>
<h1>DPLA Visualizations - See how your term(s) frequency changes through time</h1>
<form action="#" method="post" id="d3-dpla">
    <input type="text" name="q" id="q" placeholder="Search the DPLA"/>
    <input type="submit" value="Search">
</form>
<p id="message"></p>
<img src="ajax-loader.gif" alt="load indicator" id="hide" class="hide" />
</body>
</html>