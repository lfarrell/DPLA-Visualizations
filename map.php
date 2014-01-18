<!DOCTYPE html>
<html>
<head>
    <title>DPLA Visualizations</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.1/leaflet.css" />
    <link rel="stylesheet" href="Leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="Leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <script src="http://cdn.leafletjs.com/leaflet-0.7.1/leaflet.js?2"></script>
    <script src="Leaflet.markercluster/dist/leaflet.markercluster-src.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript">
        $(function() {
            $('#d3-dpla').submit(function(e) {
                e.preventDefault();
                var q = $('#q').val();
                var hide = $('#hide');
                var maps = $('#map');
                var message = $('#message');

                message.text('');

                if(maps.html() != '') {
                    maps.detach();
                    var map_div = document.createElement('div');
                    map_div.id = 'map';
                    $(map_div).appendTo('body');
                }

                if(q) {
                    hide.removeClass('hide');

                    $.getJSON("DplaMap.php?q=" + q, function(data) {
                        var map = L.map('map').setView([36, -88], 2);
                        L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
                            maxZoom: 21
                        }).addTo(map);

                        var markers = L.markerClusterGroup();

                        for (var i = 0; i < data.length; i++) {
                            if(data[i].lat == null || data[i].lat == '') {
                                continue;
                            }
                            var title = data[i].title;
                            var marker = L.marker(L.latLng(data[i].lat, data[i].lon), { title: title });
                            marker.bindPopup('<strong>' + title + '</strong><br />' +
                                    '<img src="' + data[i].path + '" width="100" height="100" style="border:1px solid gray"> ' +
                                    '<br />View this item <a target="_blank" href="' + data[i].link +'">here</a>');
                            markers.addLayer(marker);
                        }

                        map.addLayer(markers);
                        hide.addClass('hide');
                        message.text("Your search term(s): " + q);
                    });
                } else {
                    message.text('Please enter a search phrase');
                }
            });
        });
    </script>
    <style type="text/css">
        #map {
            height: 600px;
            clear: both;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<h1>DPLA Visualizations - See where your term(s) are located geographically</h1>
<form action="#" method="post" id="d3-dpla">
    <input type="text" name="q" id="q" placeholder="Search the DPLA"/>
    <input type="submit" value="Search">
</form>
<p id="message"></p>
<img src="ajax-loader.gif" alt="load indicator" id="hide" class="hide" />
<div id="map"></div>
<?php include_once 'google.php'; ?>
</body>
</html>