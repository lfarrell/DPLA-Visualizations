<!DOCTYPE html>
<html>
<head>
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
                            .range(["rgb(51,102,204)", "rgb(0,0,255)",
                                "rgb(0,0,204)", "rgb(0,0,153)","rgb(0,0,102)"]);

                        var projection = d3.geo.albersUsa()
                            .translate([width/2, height/2])
                            .scale([1200]);

                        var path = d3.geo.path()
                            .projection(projection);

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

                                        //Copy the data value into the JSON
                                        json.features[j].properties.value = dataValue;

                                        //Stop looking through the JSON
                                        break;

                                    }
                                }
                            }

                            svg.selectAll("path")
                                .data(json.features)
                                .enter()
                                .append("path")
                                .attr("d", path)
                                .style("fill", function(d) {
                                    var value = d.properties.value;

                                    if (value) {
                                        return color(value);
                                    } else {
                                        return "#ccc";
                                    }
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
        path:hover {
            fill: brown;
            fill-opacity: .7;
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