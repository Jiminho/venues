/**
 * Create a custom overlay for our window marker display, extending google.maps.OverlayView.
 * This is somewhat complicated by needing to async load the google.maps api first - thus, we
 * wrap CustomWindow into a closure, and when instantiating CustomWindow, we first execute the closure
 * (to create our CustomWindow function, now properly extending the newly loaded google.maps.OverlayView),
 * and then instantiate said function.
 * @type {Function}
 */
function GenCustomWindow(){
    var CustomWindow = function(){
        this.container = document.createElement('div');
        this.container.classList.add('map-info-window');
        this.layer = null;
        this.marker = null;
        this.position = null;
    };
    /**
     * Inherit from OverlayView
     * @type {google.maps.OverlayView}
     */
    CustomWindow.prototype = new google.maps.OverlayView();
    /**
     * Called when this overlay is set to a map via this.setMap. Get the appropriate map pane
     * to add the window to, append the container, bind to close element.
     * @see CustomWindow.open
     */
    CustomWindow.prototype.onAdd = function(){
        this.layer = this.getPanes().floatPane;
        this.layer.appendChild(this.container);
        this.container.getElementsByClassName('map-info-close')[0].addEventListener('click', function(){
            // Close info window on click
            this.close();
        }.bind(this), false);
        // Ensure newly opened window is fully in view
        setTimeout(this.panToView.bind(this), 200);
    };
    /**
     * Called after onAdd, and every time the map is moved, zoomed, or anything else that
     * would effect positions, to redraw this overlay.
     */
    CustomWindow.prototype.draw = function(){
        var markerIcon = this.marker.getIcon(),
            cHeight = this.container.offsetHeight + markerIcon.scaledSize.height + 10,
            cWidth = this.container.offsetWidth / 2;
        this.position = this.getProjection().fromLatLngToDivPixel(this.marker.getPosition());
        this.container.style.top = this.position.y - cHeight+'px';
        this.container.style.left = this.position.x - cWidth+'px';
    };
    /**
     * If the custom window is not already entirely within the map view, pan the map the minimum amount
     * necessary to bring the custom info window fully into view.
     */
    CustomWindow.prototype.panToView = function(){
        var position = this.position,
            latlng = this.marker.getPosition(),
            top = parseInt(this.container.style.top, 10),
            cHeight = position.y - top,
            cWidth = this.container.offsetWidth / 2,
            map = this.getMap(),
            center = map.getCenter(),
            bounds = map.getBounds(),
            degPerPixel = (function(){
                var degs = {},
                    div = map.getDiv(),
                    span = bounds.toSpan();

                degs.x = span.lng() / div.offsetWidth;
                degs.y = span.lat() / div.offsetHeight;
                return degs;
            })(),
            infoBounds = (function(){
                var infoBounds = {};

                infoBounds.north = latlng.lat() + cHeight * degPerPixel.y;
                infoBounds.south = latlng.lat();
                infoBounds.west = latlng.lng() - cWidth * degPerPixel.x;
                infoBounds.east = latlng.lng() + cWidth * degPerPixel.x;
                return infoBounds;
            })(),
            newCenter = (function(){
                var ne = bounds.getNorthEast(),
                    sw = bounds.getSouthWest(),
                    north = ne.lat(),
                    east = ne.lng(),
                    south = sw.lat(),
                    west = sw.lng(),
                    x = center.lng(),
                    y = center.lat(),
                    shiftLng = ((infoBounds.west < west) ? west - infoBounds.west : 0) +
                        ((infoBounds.east > east) ? east - infoBounds.east : 0),
                    shiftLat = ((infoBounds.north > north) ? north - infoBounds.north : 0) +
                        ((infoBounds.south < south) ? south - infoBounds.south : 0);

                return (shiftLng || shiftLat) ? new google.maps.LatLng(y - shiftLat, x - shiftLng) : void 0;
            })();

        if (newCenter){
            map.panTo(newCenter);
        }
    };
    /**
     * Called when this overlay has its map set to null.
     * @see CustomWindow.close
     */
    CustomWindow.prototype.onRemove = function(){
        this.layer.removeChild(this.container);
    };
    /**
     * Sets the contents of this overlay.
     * @param {string} html
     */
    CustomWindow.prototype.setContent = function(html){
        this.container.innerHTML = html;
    };
    /**
     * Sets the map and relevant marker for this overlay.
     * @param {google.maps.Map} map
     * @param {google.maps.Marker} marker
     */
    CustomWindow.prototype.open = function(map, marker){
        this.marker = marker;
        this.setMap(map);
    };
    /**
     * Close this overlay by setting its map to null.
     */
    CustomWindow.prototype.close = function(){
        this.setMap(null);
    };
    return CustomWindow;
}

/********************************************************/
function GMaps(opts){
	this.mapReady = false;
	this.options = opts;
	this.appendGMaps(opts.apiKey, opts.sensor);
}

GMaps.prototype.appendGMaps = function(apiKey, sensor){
	var script = document.createElement('script'),
		key = (apiKey) ? '&key='+apiKey : '';
	script.type = 'application/javascript';
	script.src = 'http://maps.googleapis.com/maps/api/js?v=3'+key+'&sensor='+sensor
					 +'&callback=gMaps.init';
	document.getElementsByTagName('head')[0].appendChild(script);
};

GMaps.prototype.init = function(){
	this.mapReady = true;
	this.infoWindow = new (GenCustomWindow())();
	this.markers = [];
	this.map = new google.maps.Map(this.options.el, {
		zoom:4,
		maxZoom:16,
		center:new google.maps.LatLng(38.766363, 23.757935),
		mapTypeId:google.maps.MapTypeId.ROADMAP
	});

	this.scatterMarkers();
};

/**
 * Create some markers at random points in NA, and attach an event to each to show our custom info
 * window when clicked.
 */
GMaps.prototype.scatterMarkers = function(json){
	var infowindow = new (GenCustomWindow())();
	
	//var text = '<?php get_points("tCafe");?>';
	var txt = String(json);
	//var obj = JSON.parse(String('"'+json+'"'));
	console.log(txt);
	var obj = JSON.parse(String(json));
	var rows = Object.keys(obj).length;
	var markers = [];
	for (var i = 0; i < rows; i++) {
	  var latLng = new google.maps.LatLng(obj[i].latitude, obj[i].longitude);
	  var marker = new google.maps.Marker({'position': latLng, 'title': obj[i].name});
	  markers.push(marker);
	  
	  bindInfoWindow(markers[i], map, infowindow, obj[i].name, obj[i].description, obj[i].rating, obj[i].votes);
	}
	var markerCluster = new MarkerClusterer(map, markers);
};

/********************************************************/

var map;
function myFunction(json) {
	
	var infowindow = new (GenCustomWindow())();
	
	//var text = '<?php get_points("tCafe");?>';
	var txt = String(json);
	//var obj = JSON.parse(String('"'+json+'"'));
	console.log(txt);
	var obj = JSON.parse(String(json));
	var rows = Object.keys(obj).length;
	var markers = [];
	for (var i = 0; i < rows; i++) {
	  var latLng = new google.maps.LatLng(obj[i].latitude, obj[i].longitude);
	  var marker = new google.maps.Marker({'position': latLng, 'title': obj[i].name});
	  markers.push(marker);
	  
	  bindInfoWindow(markers[i], map, infowindow, obj[i].name, obj[i].description, obj[i].rating, obj[i].votes);
	}
	var markerCluster = new MarkerClusterer(map, markers);
}

function bindInfoWindow(marker, map, infowindow, strName, strDescription, strRating, strVotes) {
	var description;
	var rClr = "#6387A5";
	var vClr = "#6387A5";
	if ( parseFloat(strRating) < 5.0) rClr = "#FF0030";
	else if ( parseFloat(strRating) < 6.5) rClr = "#E8600C";
	else if ( parseFloat(strRating) < 7.5) rClr = "#FFBE00";
	else if ( parseFloat(strRating) < 8.5) rClr = "#C4E80C";
	else rClr = "#0DFF36";
	
	if ( parseInt(strVotes) < 10) vClr = "#FF0030";
	else if ( parseInt(strVotes) < 20) vClr = "#E8600C";
	else if ( parseInt(strVotes) < 40) vClr = "#FFBE00";
	else if ( parseInt(strVotes) < 80) vClr = "#C4E80C";
	else vClr = "#0DFF36";
	if (strDescription == null) strDescription = "";
	description = "	<div class=\"map-info-window\">\
		<div class=\"map-info-close\">x</div>\
		<font color=\"#6387A5\" size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">\
			<h4><b>"+strName+"</b></h4>\
			<div class=\"rating\">\
				<h6><b>Your rating:</b></h6>\
				<input type=\"radio\" id=\"star10\" name=\"rating\" value=\"10\" /><label for=\"star10\" title=\"Rocks!\">&#9733;</label>\
				<input type=\"radio\" id=\"star9\" name=\"rating\" value=\"9\" /><label for=\"star9\" title=\"Really good\">&#9733;</label>\
				<input type=\"radio\" id=\"star8\" name=\"rating\" value=\"8\" /><label for=\"star8\" title=\"Pretty good\">&#9733;</label>\
				<input type=\"radio\" id=\"star7\" name=\"rating\" value=\"7\" /><label for=\"star7\" title=\"Good\">&#9733;</label>\
				<input type=\"radio\" id=\"star6\" name=\"rating\" value=\"6\" /><label for=\"star6\" title=\"Meh\">&#9733;</label>\
				<input type=\"radio\" id=\"star5\" name=\"rating\" value=\"5\" /><label for=\"star5\" title=\"Average\">&#9733;</label>\
				<input type=\"radio\" id=\"star4\" name=\"rating\" value=\"4\" /><label for=\"star4\" title=\"Kinda bad\">&#9733;</label>\
				<input type=\"radio\" id=\"star3\" name=\"rating\" value=\"3\" /><label for=\"star3\" title=\"Bad\">&#9733;</label>\
				<input type=\"radio\" id=\"star2\" name=\"rating\" value=\"2\" /><label for=\"star2\" title=\"Awful\">&#9733;</label>\
				<input type=\"radio\" id=\"star1\" name=\"rating\" value=\"1\" /><label for=\"star1\" title=\"Sucks big time\">&#9733;</label>\
			</div>\
			<h6><b>Ratings: <span style=\"color:"+rClr+"\">"+strRating+"</span> from <span style=\"color:"+vClr+"\">"+strVotes+"</span> users.</b></h6>\
			<br/>"+strDescription+"\
		</font>\
	</div>";
	google.maps.event.addListener(marker, 'click', function() {
		infowindow.setContent(description);
		infowindow.open(map, marker);
	});
}

$(function() {
	
		var marketId = []; //returned from the API
		var allLatlng = []; //returned from the API
		var allMarkers = []; //returned from the API
		var marketName = []; //returned from the API
		var infowindow = null;
		var pos;
		var userCords;
		var tempMarkerHolder = [];
		
		//Start geolocation
		
		if (navigator.geolocation) {    
		
			function error(err) {
				console.warn('ERROR(' + err.code + '): ' + err.message);
			}
			
			function success(pos){
				userCords = pos.coords;
				console.log("Latitude: " + userCords.latitude);
				console.log("Longitude: " + userCords.longitude);
				//return userCords;
			}
		
			// Get the user's current position
			navigator.geolocation.getCurrentPosition(success, error);
			//console.log(pos.latitude + " " + pos.longitude);
		} else {
			alert('Geolocation is not supported in your browser');
		}
		
		//End Geo location
	
		//map options
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
			scaleControl: false

		};
	
	//Adding infowindow option
	infowindow = new google.maps.InfoWindow({
		content: "holding..."
	});
	
	//Fire up Google maps and place inside the map-canvas div
	map = new google.maps.Map(document.getElementById('content'), mapOptions);
	
	// Make map responsive
	var cent;
	function calculateCenter() {
	  cent = map.getCenter();
	}
	google.maps.event.addDomListener(map, 'idle', function() {
	  calculateCenter();
	});
	google.maps.event.addDomListener(window, 'resize', function() {
	  map.setCenter(cent);
	});
});