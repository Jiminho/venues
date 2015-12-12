/* Pull local Farers market data from the USDA API and display on 
** Google Maps using GeoLocation or user input zip code. By Paul Dessert
** www.pauldessert.com | www.seedtip.com
*/
var map;
function myFunction(json) {
	
	var infowindow = new InfoBubble({
		content: "",
		map: map,
		shadowStyle: 1,
		padding: 5,
		//backgroundColor: 'rgba(57,57,57,0.9)',
		backgroundColor: 'rgba(33, 32, 38, 0.95)',
		borderRadius: 20,
		arrowSize: 10,
		borderWidth: 2,
		borderColor: '#a2e55f',
		disableAutoPan: true,
		minWidth: 270,
		maxWidth: 300,
		minHeight: 190,
		maxHeight: 500,
		hideCloseButton: true,
		arrowPosition: 50,
		//backgroundClassName: 'bubblewrap',
		arrowStyle: 0,
		disableAutoPan: false
		
    });

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
		<!--div class=\"map-info-close\">x</div-->\
		<!--font color=\"#6387A5\" size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\"-->\
		<font color=\"#ddd\" size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">\
			<h4><b><span style=\"color: #A1D700;\">"+strName+"</span></b></h4>\
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
			</div><br />\
			<h6><b>Ratings: <span style=\"clear: both; color:"+rClr+"\">"+strRating+"</span> from <span style=\"color:"+vClr+"\">"+strVotes+"</span> users.</b></h6>\
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
	
	/*
	//grab form data
    $('#chooseZip').submit(function() { // bind function to submit event of form
		
		//define and set variables
		var userZip = $("#textZip").val();
		//console.log("This-> " + userCords.latitude);
		
		var accessURL;
		
		if(userZip){
			accessURL = "http://search.ams.usda.gov/farmersmarkets/v1/data.svc/zipSearch?zip=" + userZip;
		} else {
			accessURL = "http://search.ams.usda.gov/farmersmarkets/v1/data.svc/locSearch?lat=" + userCords.latitude + "&lng=" + userCords.longitude;
		}
			

			//Use the zip code and return all market ids in area.
			$.ajax({
				type: "GET",
				contentType: "application/json; charset=utf-8",
				url: accessURL,
				dataType: 'jsonp',
				success: function (data) {

					 $.each(data.results, function (i, val) {
						marketId.push(val.id);
						marketName.push(val.marketname);
					 });
						
					//console.log(marketName);
					
					var counter = 0;
					//Now, use the id to get detailed info
					$.each(marketId, function (k, v){
						$.ajax({
							type: "GET",
							contentType: "application/json; charset=utf-8",
							// submit a get request to the restful service mktDetail.
							url: "http://search.ams.usda.gov/farmersmarkets/v1/data.svc/mktDetail?id=" + v,
							dataType: 'jsonp',
							success: function (data) {

							for (var key in data) {

								var results = data[key];
								
								//console.log(results);
								
								//The API returns a link to Google maps containing lat and long. This pulls it apart.
								var googleLink = results['GoogleLink'];
								var latLong = decodeURIComponent(googleLink.substring(googleLink.indexOf("=")+1, googleLink.lastIndexOf("(")));
								
								var split = latLong.split(',');
								var latitude = split[0];
								var longitude = split[1];
								
								//set the markers.	  
								myLatlng = new google.maps.LatLng(latitude,longitude);
						  
								allMarkers = new google.maps.Marker({
									position: myLatlng,
									map: map,
									title: marketName[counter],
									html: 
											'<div class="markerPop">' +
											'<h1>' + marketName[counter].substring(4) + '</h1>' + //substring removes distance from title
											'<h3>' + results['Address'] + '</h3>' +
											'<p>' + results['Products'].split(';') + '</p>' +
											'<p>' + results['Schedule'] + '</p>' +
											'</div>'
								});

								//put all lat long in array
								allLatlng.push(myLatlng);
								
								//Put the marketrs in an array
								tempMarkerHolder.push(allMarkers);
								
								counter++;
								//console.log(counter);
							};
								
								google.maps.event.addListener(allMarkers, 'click', function () {
									infowindow.setContent(this.html);
									infowindow.open(map, this);
								});
								
								
								//console.log(allLatlng);
								//  Make an array of the LatLng's of the markers you want to show
								//  Create a new viewpoint bound
								var bounds = new google.maps.LatLngBounds ();
								//  Go through each...
								for (var i = 0, LtLgLen = allLatlng.length; i < LtLgLen; i++) {
								  //  And increase the bounds to take this point
								  bounds.extend (allLatlng[i]);
								}
								//  Fit these bounds to the map
								map.fitBounds (bounds);
								
										
							}
						});
					}); //end .each
				}
			});

        return false; // important: prevent the form from submitting
    });*/
});

