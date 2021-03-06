
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
var map;
var markers = [];
var heatPoints = [];
var markerCluster = null;
var heatmap = null;
var description = 'Loading....';


function toggleBounce()
{
	if (marker.getAnimation() != null) {
		marker.setAnimation(null);
	} else {
		marker.setAnimation(google.maps.Animation.BOUNCE);
	}
}


function average()
{
	var total = 0;
	for (var i = 0;i < markers.length; i++) {
		total = total + markers[i].zIndex;
	}
	var value = Math.round(total * 10 / markers.length) / 10;

	// different index for different icons showed on cluster maker.
	var index = parseInt(value / 30, 10) + 1;
	console.log('value: ' + value + ', index: ' + index);
	return {
		text: value,
		index: index
	};
};

function getPoints(uid, map, json, toggleHeatmap, showAverage)
{
	var infowindow = new InfoBubble({
		content: "Loading...",
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
		hideCloseButton: false,
		arrowPosition: 50,
		//backgroundClassName: 'bubblewrap',
		arrowStyle: 0,
		disableAutoPan: false
		
	});
	
	// Unset all markers
	var i = 0, l = markers.length;
	for (i; i<l; i++) {
		markers[i].setMap(null);
	}
	markers = [];

	// Clear all clusters and markers from the clusterer.
	if (markerCluster != null)
		markerCluster.clearMarkers();
	
	// Unset heatmap points
	heatPoints.length = 0;
	
	// Clear heatmap from map
	if (heatmap != null)
		heatmap.setMap(null);
	
	var txt = String(json);
	var obj = JSON.parse(String(json));
	var rows = Object.keys(obj).length;
	
	for (var i = 0; i < rows; i++)
	{
		var latLng = new google.maps.LatLng(obj[i].latitude, obj[i].longitude);
		
		if (toggleHeatmap)
		{
			heatPoints.push(latLng);
		}
		else
		{
			var marker = new google.maps.Marker({'position': latLng, 'title': obj[i].name, 'zIndex': obj[i].id, 'rating': obj[i].rating});
			markers.push(marker);
			
			// Add marker click listener to load content and open infowindow
			google.maps.event.addListener(marker, "click", 
			(function (marker, description, infowindow){
			  return function() {
				var vid = this.zIndex;
				var data = { action: 'content', uid: uid, vid: vid };
				data = $(this).serialize() + "&" + $.param(data);
				
				$.ajax(
				{
					url : "/venues/content.php",
					type: "POST",
					dataType: "json",
					data: data,
					success:function(data, textStatus, description) 
					{
						var obj = JSON.parse(String(data));
						
						var uClr = getColor(obj[0].user_rating, 100);
						var rClr = getColor(obj[0].rating.toFixed(2), 100);
						var vClr = getColor(obj[0].votes, 50);
						
						if (obj[0].description == null) obj[0].description = "";
						if (obj[0].user_rating == null)
						{
							obj[0].user_rating = "Click here to rate!";
						}
						if (uid == 0)
						{
							description = "	<div class=\"map-info-window\">\
								<h4><b><a href=\"/venues/venue/"+vid+"\" target=\"_blank\" title=\"Click for details\" style=\"color:"+rClr+";\" onMouseOver=\"this.style.color='#fff'\" onMouseOut=\"this.style.color='"+rClr+"'\">"+obj[0].name+"</a></b></h4>\
								<div class=\"rating\">\
									<h6><b>Your rating: <span style=\"color:"+rClr+";\">Login to rate!</span></b></h6>\
								</div>\
								<h6><b>Ratings: <span style=\"clear: both; color:"+rClr+"\">"+obj[0].rating.toFixed(2)+"</span> from <span style=\"color:"+vClr+"\">"+obj[0].votes+"</span> users.</b></h6>\
								"+obj[0].description+"<br />\
								<a href=\"/venues/venue/"+vid+"\" target=\"_blank\" style=\"color:"+rClr+";\" onMouseOver=\"this.style.color='#fff'\" onMouseOut=\"this.style.color='"+rClr+"'\">View this venue!</a>\
							</div>";
						}
						else
						{	
							description = "	<div class=\"map-info-window\">\
								<h4><b><a href=\"/venues/venue/"+vid+"\" target=\"_blank\" title=\"Click for details\" style=\"color:"+uClr+";\" onMouseOver=\"this.style.color='#fff'\" onMouseOut=\"this.style.color='"+uClr+"'\">"+obj[0].name+"</a></b></h4>\
								<div class=\"rating\">\
									<h6><b>Your rating: <a href=\"/venues/venue/"+vid+"\" target=\"_blank\" style=\"color:"+uClr+";\" onMouseOver=\"this.style.color='#fff'\" onMouseOut=\"this.style.color='"+uClr+"'\">"+obj[0].user_rating+"</a></b></h6>\
								</div>\
								<h6><b>Ratings: <span style=\"clear: both; color:"+rClr+"\">"+obj[0].rating.toFixed(2)+"</span> from <span style=\"color:"+vClr+"\">"+obj[0].votes+"</span> users.</b></h6>\
								"+obj[0].description+"<br/>\
								<a href=\"/venues/venue/"+vid+"\" target=\"_blank\" style=\"color:"+uClr+";\" onMouseOver=\"this.style.color='#fff'\" onMouseOut=\"this.style.color='"+uClr+"'\">View and rate this venue!</a>\
							</div>";
						}
								
						infowindow.setContent(description.toString());
						infowindow.open(map, marker);
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						description = 'Loading error...';
					}
				});
			  };
			})(marker, description, infowindow)); 
			
			// Add map click listener to close any open infowindows
			google.maps.event.addListener(map, "click", function (event) {  
				infowindow.close();
			});
			
			google.maps.event.addListener(marker, "click", function () { 
				this.setAnimation(google.maps.Animation.BOUNCE);
			});
			
			google.maps.event.addListener(marker, "mouseout", function () { 
				this.setAnimation(null);
			});
		}
	}
	
	if (toggleHeatmap)
	{
		var gradient = [
			'rgba(0, 255, 255, 0)',
			'rgba(0, 255, 255, 1)',
			'rgba(0, 191, 255, 1)',
			'rgba(0, 127, 255, 1)',
			'rgba(0, 63, 255, 1)',
			'rgba(0, 0, 255, 1)',
			'rgba(0, 0, 223, 1)',
			'rgba(0, 0, 191, 1)',
			'rgba(0, 0, 159, 1)',
			'rgba(0, 0, 127, 1)',
			'rgba(63, 0, 91, 1)',
			'rgba(127, 0, 63, 1)',
			'rgba(191, 0, 31, 1)',
			'rgba(255, 0, 0, 1)'
		];
		
		heatmap = new google.maps.visualization.HeatmapLayer({
			data: heatPoints,
			gradient: gradient,
			maxZoom: 12,
			dissipating: true,
			radius: 12
		});
		
		heatmap.setMap(map);
	}
	else
	{
		var mcOptions = {
			//styles: clusterStyles, 
			gridSize: 45,
			minimumClusterSize: 2
		};
		
		markerCluster = new MarkerClusterer(map, markers, mcOptions);
		
		if (showAverage)
		{
			markerCluster.setCalculator(function(markers, numStyles){
				var total = 0;
				var index = 0;
				var count = markers.length;
				
				for (var i = 0; i < count; i++) {
					total = total + markers[i].rating;
				}
				var value = Math.round(total * 10 / count) / 10;
				
				total = count;
				while (total !== 0) {
					//Create a new total by dividing by a set number
					total = parseInt(total / 10, 10);
					//Increase the index and move up to the next style
					index++;
				}
				
				index = Math.min(index, numStyles);
				
				//console.log('value: ' + value + ', index: ' + index);
				return {
					text: value,
					index: index
				};
			});
		}
		else
		{
			markerCluster.setCalculator(function(markers, numStyles){
				var index = 0;
				var count = markers.length;
				var dv = count;
				while (dv !== 0) {
					dv = parseInt(dv / 10, 10);
					index++;
				}

				index = Math.min(index, numStyles);
				return {
					text: count,
					index: index
				};
			});
		}
	}
}

function getColor(count, total)
{
	var clr = "#6387A5";
	if (count == 0 || count == null) return clr;
	
	var red = 0, green = 0, blue = 0;
	if (count >= (total/2)) {
		red = 255 - Math.round(((count - (total/2)) / (total/2)) * 255);
		green = 255;
		blue = 0;
	} else if (count > 0) {
		red = 255;
		green = Math.round(((count) / (total/2)) * 255);
		blue = 0;
	} else {
		red = 0;
		green = 0;
		blue = 255;
	}
	clr = "rgb(" + red + "," + green + "," + blue + ")";
	
	return clr;
}
