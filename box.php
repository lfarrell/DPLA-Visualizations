<!DOCTYPE html>
<html>
<head>
    <?php include 'meta.php'; ?>
    <title>DPLA Visualizations</title>
    <link href='http://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="style.css" />
    <style>
        .node {
            border: solid 1px white;
            font: 10px sans-serif;
            line-height: 12px;
            overflow: hidden;
            position: absolute;
            text-indent: 2px;
        }
    </style>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
    <script type="text/javascript" src="http://d3js.org/d3.v3.min.js"></script>
    <script type="text/javascript">
        $(function() {
           var width = 1200,
               height = 800,
               colors = d3.scale.category20c(),
               valueAccessor = function(d) {
                   return d.count;
               };

           var svg = d3.select("body").append("div")
               .style("position", "relative")
               .style("width", (width) + "px")
               .style("height", (height) + "px")
               .style("left", 10 + "px")
               .style("top", 10 + "px");


           var treemap = d3.layout.treemap()
                .round(false)
                .size([width, height])
                .sticky(true)
                .value(valueAccessor);

           d3.json("lang.json", function(error, root) {
                var node = svg.datum(root).selectAll("div.node")
                    .data(treemap.nodes)
                    .enter().append("div")
                    .attr("class", "node")
                    .call(position)
                    .style("background", function(d) { return colors(d.lang); })
                    .text(function(d) { return d.lang; });
           });

            function position() {
                this.style("left", function(d) { return d.x + "px"; })
                    .style("top", function(d) { return d.y + "px"; })
                    .style("width", function(d) { return Math.max(0, d.dx - 1) + "px"; })
                    .style("height", function(d) { return Math.max(0, d.dy - 1) + "px"; });
            }
        });
    </script>
</head>
<body>
    <?php include_once 'header.php'; ?>
    <h1>DPLA Visualizations - Languages represented in the DPLA Bookshelf</h1>
</body>
</html>