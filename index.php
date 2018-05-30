<?php

$schema = json_decode(file_get_contents("etc/schema.json"), true);
$form_html = $schema['form'];
$form_columns = $schema['columns'];

?><!DOCTYPE html>
<html>
<!--

Mappyclick by Ash Smith ads04r@ecs.soton.ac.uk

Mappyclick is based on Clickymap.

Clickymap by Christopher Gutteridge cjg@ecs.soton.ac.uk

Clickymap is public domain. Free to modify and reuse for any purpose.

(Leaflet & jQuery are still subject to their respective licenses)
-->
<head>
  <meta charset="utf-8"/>
  <title>Mappyclick!</title>
  <link rel="stylesheet" href="leaflet.css" />
  <script src="leaflet.js"></script>
  <script src="jquery.min.js"></script>
  <style>
body {
	margin:0;
}
#map {
	position: absolute;
	width: 100%;
	height: 100%;
	cursor: crosshair;
}
#output {
	position: absolute;
	width: 30%;
	height: 100%;
	right: 0;
	background-color: #ccd;
	font-family: monospace;
	text-align:left;
	overflow-y: auto;
	font-size: 80%;
}
#controls {
	position: absolute;
	width: 20%;
	left: 45%;
	top: 2%;
	background-color: rgba( 0,0,0,0.3);
	padding: 0.3em;
	color: white;
}
#controls a {
	display: inline-block;
	color: white;
	font-size:80%;
}
  </style>
</head>

<body>
    <div id="map"></div>
<script>
var map = L.map('map').setView([50.93564,-1.39614], 17);
var iconurl = '<?php print($schema['icon-pin']); ?>';
var icon = L.icon({
	iconUrl: iconurl,
	shadowUrl: '<?php print($schema['icon-shadow']); ?>',
	iconSize: [32, 37],
	shadowSize: [69, 33],
	iconAnchor: [16, 36],
	popupAnchor: [0, -38],
	shadowAnchor: [16, 33]
});

L.tileLayer('<?php print($schema['tiles']); ?>',{
    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
    maxZoom: 20
}).addTo(map);
var first=true;
function addPins(map, icon)
{
	var url = './pins.php';
	$.getJSON(url, function(data)
	{
		var i;
		for(i = 0; i < data.length; i++)
		{
			var lat = data[i].latitude;
			var lon = data[i].longitude;

			L.marker([lat, lon], {icon: icon}).addTo(map).on('click', function() { return false; });
		}
	});
}
function onMapClick(e)
{
	var RES = 100000;
	var lat = Math.round(e.latlng.lat*RES)/RES;
	var lon = Math.round(e.latlng.lng*RES)/RES;

	var html = '<form id="new_item_form" method="POST" action="./update.php"><table>';
	html = html + '<?php print(str_replace("[LAT]", "' + lat + '", str_replace("[LON]", "' + lon + '", str_replace("[ICON]", $schema['icon-pin'], $form_html)))); ?>';
	html = html + '</form>';

	var ll = L.latLng(lat, lon);
	var popup = L.popup()
		.setLatLng(ll)
		.setContent(html)
		.openOn(map);

	$("#new_item_submit").on('click', function()
	{
		var lat = $('#new_item_lat').val();
		var lon = $('#new_item_lon').val();
		var label = $('#new_item_label').val();
		var itemaccess = $('#new_item_access').val();
		var itemclass = $('#new_item_class').val();
		var itemtype = $('#new_item_type').val();

		var data = {
			'label': label,
			'access': itemaccess,
			'class': itemclass,
			'type': itemtype,
			'lat': lat,
			'lon': lon
		}

		$('#new_item_form').parent().html('<img src="https://maps.southampton.ac.uk/graphics/spinner.gif"> Saving...');

		$.post("./update.php", data, function()
		{
			map.closePopup();
			L.marker([lat, lon], {icon: icon}).addTo(map).on('click', function() { return false; });
		});
	});
}
$(document).ready(function(){
	addPins(map, icon);
	$('#output').focus();
});
map.on('click', onMapClick);
</script>
</body>
</html>

