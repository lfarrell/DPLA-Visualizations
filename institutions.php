<!DOCTYPE html>
<html>
<head>
    <?php include 'meta.php'; ?>
    <title>DPLA Visualizations</title>
    <link href='http://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="style.css" />
    <script type="text/javascript" src="http://d3js.org/d3.v3.min.js"></script>
    <script type="text/javascript">
        (window.onload = function() {
            var contributors = [
                {"org": "HathiTrust", "items": 1914614},
                {"org": "New York Public Library", "items": 1168386},
                {"org": "Smithsonian Institution", "items": 897196},
                {"org": "Mountain West Digital Library", "items": 867246},
                {"org": "National Archives and Records Administration", "items": 700952},
                {"org": "Portal to Texas History", "items": 477639},
                {"org": "University of Southern California. Libraries", "items": 301325},
                {"org": "North Carolina Digital Heritage Center", "items": 260709},
                {"org": "Digital Library of Georgia", "items": 259640},
                {"org": "Internet Archive", "items": 208953},
                {"org": "GPO", "items": 148715},
                {"org": "Biodiversity Heritage Library", "items": 136444 },
                {"org": "Kentucky Digital Library", "items": 127755},
                {"org": "Digital Commonwealth", "items": 124804},
                {"org": "J. Paul Getty Trust", "items": 92681},
                {"org": "South Carolina Digital Library", "items": 76001},
                {"org": "ARTstor", "items": 56342},
                {"org": "David Rumsey", "items": 48132},
                {"org": "Missouri Hub", "items": 41557},
                {"org": "Minnesota Digital Library", "items": 40533},
                {"org": "University of Virginia Library", "items": 30188},
                {"org": "University of Illinois at Urbana-Champaign", "items": 18103},
                {"org": "Harvard Library", "items": 9068}
            ];
            var div = d3.select("body").append("div")
                .attr("class", "tooltip")
                .style("opacity", 0);

            var pie = d3.layout.pie()
                .sort(null)
                .value(function(d) { return d.items; });
            var width = 850;
            var height = 600;
            var color = d3.scale.category20();
            var outerRadius = width / 3;
            var innerRadius = 50;

            var arc = d3.svg.arc()
                .innerRadius(innerRadius)
                .outerRadius(outerRadius);

            var svg = d3.select("body")
                .append("svg")
                .attr("width", width)
                .attr("height", height);

            var arcs = svg.selectAll("g.arc")
                .data(pie(contributors))
                .enter()
                .append("g")
                .attr("class", "arc")
                .attr("transform", "translate(" + outerRadius + ", " + outerRadius + ")")
                .on("mouseover", function(d) {
                    div.transition()
                        .duration(200)
                        .style("opacity", .9);

                    div .html("The " + d.data.org + " has contributed <br/>" + formatCount(d.data.items) + " items to the DPLA")
                        .style("left", (d3.event.pageX - 28) + "px")
                        .style("top", (d3.event.pageY - 28) + "px");
                })
                .on("mouseout", function() {
                    div.transition()
                        .duration(500)
                        .style("opacity", 0);
                });

            arcs.append("path")
                .attr("fill", function(d, i) {
                    return color(i);
                })
                .attr("d", arc);

         /*   arcs.append("text")
                .attr("transform", function(d) {
                    return "translate(" + arc.centroid(d) + ")";
                })
                .attr("text-anchor", "middle")
                .text(function(d) {
                    return d.data.items;
                }); */

            var legend = svg.append("g")
                .attr("class", "legend")
                .attr("x", width - 270)
                .attr("y", 40)
                .attr("height", "auto")
                .attr("width", 295);

            legend.selectAll('g').data(contributors)
                .enter()
                .append('g')
                .each(function(d, i) {
                    var g = d3.select(this);
                    g.append("rect")
                        .attr("x", width - 265)
                        .attr("y", i*25)
                        .attr("width", 10)
                        .attr("height", 10)
                        .style("fill", color(i));

                    g.append("text")
                        .attr("x", width - 250)
                        .attr("y", i * 25 + 9)
                        .attr("height",30)
                        .attr("width",200)
                        .style("fill", "black")
                        .text(d.org);
                });

            function formatCount(number) {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
        })();
    </script>
    <style type="text/css">
        svg {
            margin-left: 17%;
        }

        text {
            font-family: Raleway, sans-serif;
            font-size: 12px;
            fill: white;
        }

        div.tooltip {
            position: absolute;
            text-align: center;
            width:  auto;
            height: auto;
            padding: 15px;
            fill: black;
            font: 14px Raleway;
            background: lightgray;
            border: 0px;
            border-radius: 8px;
            pointer-events: none;
        }
    </style>
</head>
<body>
<?php include_once 'header.php'; ?>
<h1>DPLA Visualizations - Depositor Items Contributed</h1>
<p>(Hover over an institutions wedge to see how many items it's contributed to the DPLA.)</p>
<?php include_once 'google.php'; ?>
</body>
</html>