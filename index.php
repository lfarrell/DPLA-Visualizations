<!DOCTYPE html>
<html>
<head>
    <title>DPLA Visualizations</title>
    <link rel="stylesheet" href="style.css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
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
                        var margin = {top: 30, right: 20, bottom: 50, left: 50},
                                height = 450 - margin.top - margin.bottom,
                                width = 1100 - margin.left - margin.right;

                        var ticks = [];
                        for(var i=0; i<data.length; i++) {
                            ticks.push(data[i].decade);
                        }

                        var svg = d3.select("body").append("svg")
                                .attr("height", height + 30)
                                .attr("width", width);

                        var xScale = d3.scale.ordinal()
                                .domain(ticks)
                                .rangeRoundBands([20, width], 0.05);

                        var yScale = d3.scale.linear()
                                .domain([0,d3.max(data, function(d) { return d.count; })])
                                .range([0, height]);

                        var yTickScale = d3.scale.linear()
                                .domain([d3.max(data, function(d) { return d.count; }), 0])
                                .range([0, height]);

                        var bars = svg.selectAll("rect")
                                .data(data, function(d) {
                                    return d.decade;
                                })
                                .enter()
                                .append("rect")
                                .attr("x", function(d, i) {
                                    return xScale(d.decade);
                                })
                                .attr("y", function(d) {
                                    return height - yScale(d.count);
                                })
                                .attr("width", xScale.rangeBand())
                                .attr("height", function(d) {
                                    return yScale(d.count);
                                })
                                .attr("fill", "steelblue");

                        var xAxis = d3.svg.axis()
                                .scale(xScale)
                                .orient("bottom");

                        svg.append("g")
                                .attr("class", "axis")
                                .attr("transform", "translate(0," + (height) + ")")
                                .call(xAxis);

                        var yAxis = d3.svg.axis()
                                .scale(yTickScale)
                                .orient("left");

                        svg.append("g")
                                .attr("class", "axis")
                                .attr("transform", "translate(" + 28 + ", 0)")
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