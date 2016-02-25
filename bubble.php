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
            var diameter = 960,
                format = d3.format(",d"),
                color = d3.scale.category20c();

            var bubble = d3.layout.pack()
                .sort(null)
                .size([diameter, diameter])
                .padding(1.5);

            var div = d3.select("body").append("div")
                .attr("class", "tooltip")
                .style("opacity", 0);


            var svg = d3.select("body").append("svg")
                .attr("width", diameter)
                .attr("height", diameter)
                .attr("class", "bubble");

            d3.json("lang.json", function(error, root) {
                var node = svg.selectAll(".node")
                    .data(bubble.nodes(classes(root))
                        .filter(function(d) { return !d.children; }))
                    .enter().append("g")
                    .attr("class", "node")
                    .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });

                node.append("title")
                    .text(function(d) { return d.language + ": " + format(d.value); });

                node.append("circle")
                    .attr("r", function(d) { return d.r; })
                    .style("fill", function(d) { return color(d.value); })
                    .on("mouseover", function(d) {
                        div.transition()
                            .duration(200)
                            .style("opacity", .9);

                        div .html(format(d.value) + " books/journals are in "  + d.language
                                + "<br/>Click to view books in this language")
                            .style("top", (d3.event.pageY-28)+"px")
                            .style("left", (d3.event.pageX-28)+"px");
                    })
                    .on("mouseout", function() {
                        div.transition()
                            .duration(500)
                            .style("opacity", 0);
                    })
                    .on("click", function(d) {
                        window.open("http://dp.la/bookshelf?language%5B%5D=" + d.language);
                    });

                node.append("text")
                    .attr("dy", ".3em")
                    .style("text-anchor", "middle")
                    .style("pointer-events", "none")
                    .text(function(d) { return d.language.substring(0, d.r / 3); });
            });

            function classes(root) {
                var classes = [];

                function recurse(name, node) {
                    if (node.children) node.children.forEach(function(child) { recurse(node.name, child); });
                    else classes.push({root: root, language: node.lang, value: node.count});
                }

                recurse(null, root);
                return {children: classes};
            }

            function formatCount(number) {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            d3.select(self.frameElement).style("height", diameter + "px");
        });
    </script>
    <style type="text/css">
        svg {
            margin-left: 12%;
        }

        p {
            margin-bottom: -45px;
        }
        text {
            font-family: Raleway sans-serif;
            font-size: 12px;
        }
        circle:hover {
            opacity: 0.6;
        }
    </style>
</head>
<body>
<?php include_once 'header.php'; ?>
<h1>DPLA Visualizations - Less common languages represented in the DPLA Bookshelf</h1>
<h4>The top 10 languages are represented separately</h4>
<p>Hover over a bubble to see how many items there are for a particular language</p>
<?php include_once 'google.php'; ?>
</body>
</html>