// Globals
var map, infowindow;
var info_content = "";
var count = 0;


function showIWContent(nr) {
	var request;
	try {
		if (typeof ActiveXObject != "undefined") {
			request = new ActiveXObject("Microsoft.XMLHTTP");
		} else if (window["XMLHttpRequest"]) {
			request = new XMLHttpRequest();
		}
	} catch (e) {}

	request.open("GET", "../infowindow-content.xml", true);
	request.onreadystatechange = function() {
		if(request.readyState == 4) {
			var xml = request.responseXML;

			var entries = xml.documentElement.getElementsByTagName("content");
			// Obtain the attributes of the requested entry
			var header = entries[nr].getAttribute("header");
			var text = entries[nr].getAttribute("text");

			// Style and store info window content 
			info_content = "<b>" + header + "<\/b><p>" + text + "<\/p>";

			// Adjust counter
			if(count < entries.length-1) { count++; }
			else { count = 0; }

			// Write content to appropriate div in infowindow
			document.getElementById("tochange").innerHTML = info_content; 
		}
	}; request.send(null);
}


function makeHTML() {
	var html ="<div id='infowindow'>" +
	"<div id='tochange'>" + info_content + "<\/div>" +
	"<p><a href='javascript:void(0)' onclick='showIWContent(count)'>Change content<\/a>" +
	"<\/p><\/div>";
	return html;
}


function createMarker(point) {
	var g = google.maps;
	var marker = new g.Marker({map: map, position: point});

	g.event.addListener(marker, "click", function() {
		var html = makeHTML();
		infowindow.setOptions({content: html, pixelOffset: new g.Size(0,0)});
		infowindow.open(map, this);
	});
	return marker;
}


function buildMap() {
	var g = google.maps;
	var point= new g.LatLng(38.766363, 23.757935);
	var opts_map = {
		zoom: 7, 
		center: point,
		panControl: false,
		panControlOptions: {
			position: g.ControlPosition.BOTTOM_LEFT
		},
		zoomControl: true,
		zoomControlOptions: {
			style: g.ZoomControlStyle.DEFAULT,
			position: g.ControlPosition.RIGHT_CENTER
		},
		scaleControl: false,
		mapTypeId: g.MapTypeId.ROADMAP
	};
	map = new g.Map(document.getElementById("map"), opts_map);
	infowindow = new g.InfoWindow();

	// Add a marker at the center of the map
	var html = makeHTML();
	createMarker(point);
	
	g.event.addListenerOnce(map, "tilesloaded", function() {
		infowindow.setOptions({ position: point, content: html, pixelOffset: new g.Size(0,-40) });
		infowindow.open(map);
	});
	// Display first content in infowindow
	g.event.addListenerOnce(infowindow, "domready", function() {
		showIWContent(count);
	});
}

window.onload = buildMap;


