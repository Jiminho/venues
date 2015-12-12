function setSliderColor(id, value)
{
	// Set color according to slider value
	var val = value, red = 0, green = 0, blue = 0;
	if (val >= 50) {
		red = 255 - Math.round(((val - 50) / 50) * 255);
		green = 255;
		blue = 0;
	} else if (val > 0) {
		red = 255;
		green = Math.round(((val) / 50) * 255);
		blue = 0;
	} else {
		red = 0;
		green = 0;
		blue = 255;
	}
	
	// Set Food Ratings Slider background color
	$(id + "Slider .slider-selection").css({
		background: "rgb(" + red + "," + green + "," + blue + ")"
	});
	
	// Set Food Ratings Slider text color
	$(id + "SliderVal").css({
		color: "rgb(" + red + "," + green + "," + blue + ")"
	});
}

function setSliderText(id, value)
{
			 if (value == 0)	$(id+'SliderVal').text('Not Rated');
		else if (value < 20)	$(id+'SliderVal').text('Terrible');
		else if (value < 35)	$(id+'SliderVal').text('Really Bad');
		else if (value < 50)	$(id+'SliderVal').text('Bad');
		else if (value < 60)	$(id+'SliderVal').text('Average');
		else if (value < 70)	$(id+'SliderVal').text('Above Average');
		else if (value < 80)	$(id+'SliderVal').text('Good');
		else if (value < 85)	$(id+'SliderVal').text('Very Good');
		else if (value < 90)	$(id+'SliderVal').text('Really Good');
		else if (value < 95)	$(id+'SliderVal').text('Excellent');
		else 					$(id+'SliderVal').text('Outstanding');
}

function setSliderTextAll(value)
{
		// Set text for all sliders, on slide event
		if (value == 0)	{
			$("#ratingsSliderVal").text('Not Rated');
			$("#fratingsSliderVal").text('Not Rated');
			$("#sratingsSliderVal").text('Not Rated');
			$("#vratingsSliderVal").text('Not Rated');
			$("#aratingsSliderVal").text('Not Rated');
		} else if (value < 20)	{
			$("#ratingsSliderVal").text('Terrible');
			$("#fratingsSliderVal").text('Terrible');
			$("#sratingsSliderVal").text('Terrible');
			$("#vratingsSliderVal").text('Terrible');
			$("#aratingsSliderVal").text('Terrible');
		} else if (value < 35)	{
			$("#ratingsSliderVal").text('Really Bad');
			$("#fratingsSliderVal").text('Really Bad');
			$("#sratingsSliderVal").text('Really Bad');
			$("#vratingsSliderVal").text('Really Bad');
			$("#aratingsSliderVal").text('Really Bad');
		} else if (value < 50)	{
			$("#ratingsSliderVal").text('Bad');
			$("#fratingsSliderVal").text('Bad');
			$("#sratingsSliderVal").text('Bad');
			$("#vratingsSliderVal").text('Bad');
			$("#aratingsSliderVal").text('Bad');
		} else if (value < 60)	{
			$("#ratingsSliderVal").text('Average');
			$("#fratingsSliderVal").text('Average');
			$("#sratingsSliderVal").text('Average');
			$("#vratingsSliderVal").text('Average');
			$("#aratingsSliderVal").text('Average');
		} else if (value < 70)	{
			$("#ratingsSliderVal").text('Above Average');
			$("#fratingsSliderVal").text('Above Average');
			$("#sratingsSliderVal").text('Above Average');
			$("#vratingsSliderVal").text('Above Average');
			$("#aratingsSliderVal").text('Above Average');
		} else if (value < 80)	{
			$("#ratingsSliderVal").text('Good');
			$("#fratingsSliderVal").text('Good');
			$("#sratingsSliderVal").text('Good');
			$("#vratingsSliderVal").text('Good');
			$("#aratingsSliderVal").text('Good');
		} else if (value < 85)	{
			$("#ratingsSliderVal").text('Very Good');
			$("#fratingsSliderVal").text('Very Good');
			$("#sratingsSliderVal").text('Very Good');
			$("#vratingsSliderVal").text('Very Good');
			$("#aratingsSliderVal").text('Very Good');
		} else if (value < 90)	{
			$("#ratingsSliderVal").text('Really Good');
			$("#fratingsSliderVal").text('Really Good');
			$("#sratingsSliderVal").text('Really Good');
			$("#vratingsSliderVal").text('Really Good');
			$("#aratingsSliderVal").text('Really Good');
		} else if (value < 95)	{
			$("#ratingsSliderVal").text('Excellent');
			$("#fratingsSliderVal").text('Excellent');
			$("#sratingsSliderVal").text('Excellent');
			$("#vratingsSliderVal").text('Excellent');
			$("#aratingsSliderVal").text('Excellent');
		} else 							{
			$("#ratingsSliderVal").text('Outstanding');
			$("#fratingsSliderVal").text('Outstanding');
			$("#sratingsSliderVal").text('Outstanding');
			$("#vratingsSliderVal").text('Outstanding');
			$("#aratingsSliderVal").text('Outstanding');
		}
}

$(document).ready(function() {
	/* Total Ratings Slider */
	// Init Total Ratings Slider
	$("#ratings").slider({
		//value: 0,
		orientation: 'horizontal',
		selection: 'before',
		tooltip: 'always'
	});
	
	setSliderText("#ratings", $("#ratings").slider('getValue'));
	setSliderColor("#ratings", $("#ratings").slider('getValue'));
	
	// Total Ratings Slider onSlide event
	$("#ratings").on("slide", function(slideEvt) {
		setSliderTextAll(slideEvt.value);
		
		// Set color according to slider value
		var val = slideEvt.value, red = 0, green = 0, blue = 0;
		if (val >= 50) {
			red = 255 - Math.round(((val - 50) / 50) * 255);
			green = 255;
			blue = 0;
		} else if (val > 0) {
			red = 255;
			green = Math.round(((val) / 50) * 255);
			blue = 0;
		} else {
			red = 0;
			green = 0;
			blue = 255;
		}
		
		// Set Total Ratings Slider background color
		$('#ratingsSlider .slider-selection').css({
			background: "rgb(" + red + "," + green + "," + blue + ")"
		});
		// Set Food Ratings Slider background color
		$('#fratingsSlider .slider-selection').css({
			background: "rgb(" + red + "," + green + "," + blue + ")"
		});
		// Set Service Ratings Slider background color
		$('#sratingsSlider .slider-selection').css({
			background: "rgb(" + red + "," + green + "," + blue + ")"
		});
		// Set Value Ratings Slider background color
		$('#vratingsSlider .slider-selection').css({
			background: "rgb(" + red + "," + green + "," + blue + ")"
		});
		// Set Atmosphere Ratings Slider background color
		$('#aratingsSlider .slider-selection').css({
			background: "rgb(" + red + "," + green + "," + blue + ")"
		});
		
		// Set Total Ratings Slider text color
		$("#ratingsSliderVal").css({
			color: "rgb(" + red + "," + green + "," + blue + ")"
		});
		// Set Food Ratings Slider text color
		$("#fratingsSliderVal").css({
			color: "rgb(" + red + "," + green + "," + blue + ")"
		});
		// Set Service Ratings Slider text color
		$("#sratingsSliderVal").css({
			color: "rgb(" + red + "," + green + "," + blue + ")"
		})
		// Set Value Ratings Slider text color
		$("#vratingsSliderVal").css({
			color: "rgb(" + red + "," + green + "," + blue + ")"
		});
		// Set Atmosphere Ratings Slider text color
		$("#aratingsSliderVal").css({
			color: "rgb(" + red + "," + green + "," + blue + ")"
		});
		
		// Set ratings for all categories
		$("#fratings").slider('setValue', slideEvt.value);
		$("#sratings").slider('setValue', slideEvt.value);
		$("#vratings").slider('setValue', slideEvt.value);
		$("#aratings").slider('setValue', slideEvt.value);
		
		$('#total').val(slideEvt.value);
		$('#food').val(slideEvt.value);
		$('#service').val(slideEvt.value);
		$('#value').val(slideEvt.value);
		$('#atmosphere').val(slideEvt.value);
	});
	
	/* Food Ratings Slider */
	// Init Food Ratings Slider
	$("#fratings").slider({
		//ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 95, 97, 100],
		//ticks_labels: ['$0', '$10', '$20', '$30', '$40', '$50', '$60', '$70', '$80', '$90', '$95', '$97', '$100'],
		//ticks_snap_bounds: 1,
		//value: 0,
		//orientation: 'vertical',
		//selection: 'after',
		//reversed : true,
		tooltip: 'always'
	});
	
	setSliderText("#fratings", $("#fratings").slider('getValue'));
	setSliderColor("#fratings", $("#fratings").slider('getValue'));
	
	// Food Ratings Slider onSlide event
	$("#fratings").on("slide", function(slideEvt) {
		// Set text for Food Ratings slider, on slide event
		setSliderText("#fratings", slideEvt.value);
		
		// Set color according to slider value
		setSliderColor("#fratings", slideEvt.value);
		
		
		// Set Total Ratings Slider value
		var svalue = $("#sratings").slider('getValue');
		var vvalue = $("#vratings").slider('getValue');
		var avalue = $("#aratings").slider('getValue');
		var tvalue = (slideEvt.value + svalue + vvalue + avalue)/4;
		$("#ratings").slider('setValue', tvalue);
		
		// Set Total slider color according to slider value
		setSliderColor("#ratings", tvalue);
		
		// Set Total Ratings Slider text
		setSliderText("#ratings", tvalue);
		
		$('#total').val(tvalue);
		$('#food').val(slideEvt.value);
	});
	
	/* Service Ratings Slider */
	// Init Service Ratings Slider
	$("#sratings").slider({
		//ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 95, 97, 100],
		//ticks_labels: ['$0', '$10', '$20', '$30', '$40', '$50', '$60', '$70', '$80', '$90', '$95', '$97', '$100'],
		//ticks_snap_bounds: 1,
		//value: 0,
		//orientation: 'vertical',
		//selection: 'after',
		//reversed : true,
		tooltip: 'always'
	});
	
	setSliderText("#sratings", $("#sratings").slider('getValue'));
	setSliderColor("#sratings", $("#sratings").slider('getValue'));
	
	// Service Ratings Slider onSlide event
	$("#sratings").on("slide", function(slideEvt) {
		// Set text for Service Ratings slider, on slide event
		setSliderText("#sratings", slideEvt.value);
		
		// Set color according to slider value
		setSliderColor("#sratings", slideEvt.value);
		
		// Set Total Ratings Slider value
		var fvalue = $("#fratings").slider('getValue');
		var vvalue = $("#vratings").slider('getValue');
		var avalue = $("#aratings").slider('getValue');
		var tvalue = (slideEvt.value + fvalue + vvalue + avalue)/4;
		$("#ratings").slider('setValue', tvalue);
		
		// Set Total slider color according to slider value
		setSliderColor("#ratings", tvalue);
		
		// Set Total Ratings Slider text
		setSliderText("#ratings", tvalue);
		
		$('#total').val(tvalue);
		$('#service').val(slideEvt.value);
	});
	
	/* Value Ratings Slider */
	// Init Value Ratings Slider
	$("#vratings").slider({
		//ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 95, 97, 100],
		//ticks_labels: ['$0', '$10', '$20', '$30', '$40', '$50', '$60', '$70', '$80', '$90', '$95', '$97', '$100'],
		//ticks_snap_bounds: 1,
		//value: 0,
		//orientation: 'vertical',
		//selection: 'after',
		//reversed : true,
		tooltip: 'always'
	});
	
	setSliderText("#vratings", $("#vratings").slider('getValue'));
	setSliderColor("#vratings", $("#vratings").slider('getValue'));
	
	// Value Ratings Slider onSlide event
	$("#vratings").on("slide", function(slideEvt) {
		// Set text for Value Ratings slider, on slide event
		setSliderText("#vratings", slideEvt.value);
		
		// Set color according to slider value
		setSliderColor("#vratings", slideEvt.value);
		
		// Set Total Ratings Slider value
		var fvalue = $("#fratings").slider('getValue');
		var svalue = $("#sratings").slider('getValue');
		var avalue = $("#aratings").slider('getValue');
		var tvalue = (slideEvt.value + fvalue + svalue + avalue)/4;
		$("#ratings").slider('setValue', tvalue);
		
		// Set Total slider color according to slider value
		setSliderColor("#ratings", tvalue);
		
		// Set Total Ratings Slider text
		setSliderText("#ratings", tvalue);
		
		$('#total').val(tvalue);
		$('#value').val(slideEvt.value);
	});
	
	/* Atmosphere Ratings Slider */
	// Init Atmosphere Ratings Slider
	$("#aratings").slider({
		//ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 95, 97, 100],
		//ticks_labels: ['$0', '$10', '$20', '$30', '$40', '$50', '$60', '$70', '$80', '$90', '$95', '$97', '$100'],
		//ticks_snap_bounds: 1,
		//value: 0,
		//orientation: 'vertical',
		//selection: 'after',
		//reversed : true,
		tooltip: 'always'
	});
	
	setSliderText("#aratings", $("#aratings").slider('getValue'));
	setSliderColor("#aratings", $("#aratings").slider('getValue'));
	
	// Atmosphere Ratings Slider onSlide event
	$("#aratings").on("slide", function(slideEvt) {
		// Set text for Atmosphere Ratings slider, on slide event
		setSliderText("#aratings", slideEvt.value);
		
		// Set color according to slider value
		setSliderColor("#aratings", slideEvt.value);
		
		// Set Total Ratings Slider value
		var fvalue = $("#fratings").slider('getValue');
		var svalue = $("#sratings").slider('getValue');
		var vvalue = $("#vratings").slider('getValue');
		var tvalue = (slideEvt.value + fvalue + svalue + vvalue)/4;
		$("#ratings").slider('setValue', tvalue);
		
		// Set Total slider color according to slider value
		setSliderColor("#ratings", tvalue);
		
		// Set Total Ratings Slider text
		setSliderText("#ratings", tvalue);
		
		$('#total').val(tvalue);
		$('#atmosphere').val(slideEvt.value);
	});
});