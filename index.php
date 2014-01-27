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
                var message = $('#message').text("Your search phrase: " + q);
                var hide = $('#hide');

                hide.removeClass('hide');

                if(q) {
                    $('svg, #records').detach();
                    $.getJSON("DplaHistogram.php?q=" + q, function(data) {
                        var count = 0;
                        for(var j=0; j<data.length; j++) {
                            count += data[j].count;
                        }
                        if(count > 0) {
                            var margin = {top: 30, right: 20, bottom: 50, left: 28},
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
                                        d3.select("body").append("div")
                                            .attr("id", "records");

                                        var recs = $('#records');
                                        recs.html('<img src="ajax-loader.gif"/>')

                                        $.get("DplaHistogram.php?q=" + q + "&decade=" + d.decade, function(data) {
                                            if(data.length === 0) {
                                                recs.html("<p>There were no records to add.</p>");
                                            } else {
                                                recs.html(data);
                                            }
                                            window.location = '#records';
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
                        } else {
                            $('#message').text('Your search returned no results');
                        }

                        hide.addClass('hide');
                    });
                } else {
                    hide.addClass('hide');
                    $('#message').text('Please submit a search term');
                }
            });
        });
    </script>
    <style type="text/css">
        .axis path,
        .axis line {
            fill: none;
            padding: 5px;
            stroke: black;
            shape-rendering: crispEdges;
        }

        .axis text {
            font-family: sans-serif;
            font-size: 11px;
        }

        rect:hover {
            fill: orange;
        }

        #records h2 {
            margin: 15px 0 -5px 25px;
        }

       #records ul {
            border: 1px solid lightgray;
            padding: 25px;
            border-radius: 5px;
            width: 50%;
        }

        #records ul a {
            text-decoration: none;
            color: steelblue;
        }

        #results {
            margin-bottom: 50px;
        }

        #full_results {
            height: auto;
            width: auto;
            padding: 10px;
            text-decoration: none;
        }
    </style>
</head>
<body>
<?php include_once 'header.php'; ?>
<h1>DPLA Visualizations - See how your term(s) frequency changes through time</h1>
<p>Note: Depending on your search terms it can take some time to return your graph</p>
<p>Hover over a column to see how many items were returned for that decade</p>
<form action="#" method="post" id="d3-dpla">
    <input type="text" name="q" id="q" placeholder="Search the DPLA"/>
    <input type="submit" value="Search">
</form>
<p id="message"></p>
<img src="ajax-loader.gif" alt="load indicator" id="hide" class="hide" />
<?php include_once 'google.php'; ?>
</body>
</html>