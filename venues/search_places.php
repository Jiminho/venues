<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php 
	$root = "/venues";
	require_once (realpath($_SERVER["DOCUMENT_ROOT"])."/venues/session.php");
	$uid = check_login($conn, 600, 'Biscuit');

	require_once(realpath($_SERVER["DOCUMENT_ROOT"])."/venues/pg2.php"); 
?>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<meta name="format-detection" content="telephone=no">
	<meta charset="UTF-8">
	
	<title>Αρχική Σελίδα</title>
		
	<!-- CSS -->
	<link href="<?php echo "$root/"; ?>css/bootstrap.min.css" rel="stylesheet" />
	<link href="<?php echo "$root/"; ?>css/font-awesome.min.css" rel="stylesheet" />
	<link href="<?php echo "$root/"; ?>css/style.css" rel="stylesheet" />
	<link href="<?php echo "$root/"; ?>css/icons.css" rel="stylesheet" />
	<link href="<?php echo "$root/"; ?>css/login.css" rel="stylesheet" />
	<link href="<?php echo "$root/"; ?>css/mapstyle.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo "$root/"; ?>css/ratings.css" rel="stylesheet" type="text/css" />
	<!--link href="<?php echo "$root/"; ?>css/custom-window.css" rel="stylesheet" type="text/css" -->
	
	<link href="<?php echo "$root/"; ?>css/jNotify.jquery.css" rel="stylesheet" type="text/css" />
	<!-- jQuery -->
	<script src="<?php echo "$root/"; ?>js/jquery.min.js" ></script> <!-- jQuery Library -->
	<script src="<?php echo "$root/"; ?>js/jquery-ui.min.js" ></script> <!-- jQuery UI -->
	<script src="<?php echo "$root/"; ?>js/jquery.easing.1.3.js" ></script> <!-- jQuery Easing - Required for Lightbox + Pie Charts-->
	
	<script src="<?php echo "$root/"; ?>js/jNotify.jquery.min.js" ></script>
	
	<!-- For infoBubble -->
	<script src="<?php echo "$root/"; ?>js/infobubble.js" type="text/javascript" ></script>
	<!-- For markerclusterer -->
	<script src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js" type="text/javascript" ></script>
	
	<style>
		html, body {
			height: 100%;
			margin: 0;
			padding: 0;
		}
		#map {
			height: 100%;
		}
		.controls {
			margin-top: 10px;
			border: 1px solid transparent;
			border-radius: 2px 0 0 2px;
			box-sizing: border-box;
			-moz-box-sizing: border-box;
			height: 32px;
			outline: none;
			box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
		}

		#pac-input {
			background-color: #fff;
			font-family: Roboto;
			font-size: 15px;
			font-weight: 300;
			margin-left: 12px;
			padding: 0 11px 0 13px;
			text-overflow: ellipsis;
			width: 300px;
			z-index: 1000;
			color: #000;
		}

		#pac-input:focus {
			border-color: #4d90fe;
		}

		.pac-container {
			font-family: Roboto;
		}

		#type-selector {
			color: #fff;
			background-color: #4d90fe;
			padding: 5px 11px 0px 11px;
		}

		#type-selector label {
			font-family: Roboto;
			font-size: 13px;
			font-weight: 300;
			color: #000;
		}
    </style>
  </head>
  <body id="skin-blur-lights">
	<div class="clearfix"></div>
		<!-- Header -->
		<header id="header" class="media">
			<?php show_header_bar($conn, $uid); login_msg($uid); ?>
			
		</header>
		
		<!-- Sidebar -->
		<aside id="sidebar">
			<?php show_side_bar("search_places"); ?>
			<h4 style="padding: 5px;">Search Locations</h4>
			<p style="padding: 5px;">This is where you can search your area for nearby venues.<br />
			Enter a location in the map search box and you will get all venues in a 5km radius.</p>
		</aside>
		
		<!-- Content >
		<section id="content" >
		</section-->
			<input id="pac-input" class="controls" type="text" placeholder="Search Location">
			<div id="content" class="map-canvas container"></div>
	
	
	<!-- Javascript Libraries -->
	<!-- Bootstrap -->
	<script src="<?php echo "$root/"; ?>js/bootstrap.min.js" ></script>
	
	<!-- All JS functions -->
	<script src="<?php echo "$root/"; ?>js/functions.js" ></script>
	
	<!-- Map -->
	<script src="https://maps.googleapis.com/maps/api/js?key=" ></script>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=visualization&sensor=true_or_false"></script>
	<script src="<?php echo "$root/"; ?>js/maptest55.js" language="javascript" type="text/javascript" ></script>
	<!--script src="<?php echo "$root/"; ?>js/custom-window.js" language="javascript" type="text/javascript" ></script-->
	<script src="<?php echo "$root/"; ?>js/search.js" language="javascript" type="text/javascript" ></script>
    <script>
	// This adds a search box to a map, using the Google Place Autocomplete
	// feature. People can enter geographical searches. The search box will return a
	// pick list containing a mix of places and predicted search terms.

	
function initAutocomplete() {
	var map;
	var mapOptions = {
		zoom: 7,
		center: new google.maps.LatLng(38.766363, 23.757935),
		// center: new google.maps.LatLng(userCords.latitude, userCords.longitude),
		panControl: false,
		panControlOptions: {
			position: google.maps.ControlPosition.BOTTOM_LEFT
		},
		zoomControl: true,
		zoomControlOptions: {
			style: google.maps.ZoomControlStyle.DEFAULT,
			position: google.maps.ControlPosition.RIGHT_CENTER
		},
		scaleControl: false,
		styles: [{"featureType":"all","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#aadd55"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#7dff00"},{"lightness":"-26"},{"saturation":"38"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"visibility":"on"},{"color":"#37962e"}]},{"featureType":"road.highway","elementType":"labels","stylers":[{"visibility":"on"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#00ff0f"},{"saturation":"-51"},{"lightness":"80"}]},{"featureType":"road.arterial","elementType":"labels.text","stylers":[{"visibility":"on"}]},{"featureType":"road.local","elementType":"labels.text","stylers":[{"visibility":"on"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#0987bf"}]}]
	};
	map = new google.maps.Map(document.getElementById('content'), mapOptions);
	
	// Create the search box and link it to the UI element.
	var input = /** @type {!HTMLInputElement} */(document.getElementById('pac-input'));
	var searchBox = new google.maps.places.SearchBox(input);
	map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

	// Bias the SearchBox results towards current map's viewport.
	map.addListener('bounds_changed', function() {
		searchBox.setBounds(map.getBounds());
	});

	var markers = [];
	
	var infowindow = new google.maps.InfoWindow();
	var marker = new google.maps.Marker({
		map: map,
		anchorPoint: new google.maps.Point(0, -29)
	});
	
	// [START region_getplaces]
	// Listen for the event fired when the user selects a prediction and retrieve
	// more details for that place.
	searchBox.addListener('places_changed', function() {
		infowindow.close();
		marker.setVisible(false);
		
		var places = searchBox.getPlaces();
		
		var lat = 0;
		var lng = 0;
		
		// No places found, exit listener
		if (places.length == 0) {
			window.alert("No places found... Check again or click on a place from the list!");
			return;
		}
		
		// Clear out the old markers.
		markers.forEach(function(marker) {
			marker.setMap(null);
		});
		markers = [];
		
		// Get bounds
		var bounds = new google.maps.LatLngBounds();
		
		// For each place, get the icon, name and location.
		places.forEach(function(place) {
			// Get icon
			var icon = {
				url: place.icon,
				size: new google.maps.Size(71, 71),
				origin: new google.maps.Point(0, 0),
				anchor: new google.maps.Point(17, 34),
				scaledSize: new google.maps.Size(25, 25)
			};
			lat = place.geometry.location.lat();
			lng = place.geometry.location.lng();
			// Create a marker for each place.
			markers.push(new google.maps.InfoWindow({
				map: map,
				icon: icon,
				title: place.name,
				position: place.geometry.location,
				content: '<div style="color: #000;"><strong>' + place.name + '</strong><br>(' + lat + ', ' + lng + ')'
			}));
			if (place.geometry.viewport) {
				// Only geocodes have viewport.
				bounds.union(place.geometry.viewport);
			} else {
				bounds.extend(place.geometry.location);
			}
		});
		map.fitBounds(bounds);
		
		var data = { action: 'points',  lat: lat, lng: lng };
		data = $(this).serialize() + "&" + $.param(data);
		console.log("Serialised data: " + data);
		
		$.ajax({
			url: "/venues/map_helper.php",
			type: "POST",
			dataType: "json",
			data: data,
			success: function(data)
			{
				getPoints(<?php if($uid != 'GUEST' && $uid != null) echo $uid; else echo 0; ?>, map, data, false, false);
			}
		});
	});
	// [END region_getplaces]
}
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&callback=initAutocomplete" async defer></script>
  </body>
</html>