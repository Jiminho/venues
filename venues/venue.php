<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php 
	$root = "/venues";
	require_once (realpath($_SERVER["DOCUMENT_ROOT"])."/venues/session.php");
	$is_secret = false;
	$uid = check_login($conn, 600, 'Biscuit');
	if (!isset($_GET['id']))
		header("location: ".$root."/index.php");
	if ($uid == null || $uid == 'GUEST')
	{
		$uid == null;
		$is_secret = true;
	}
	$vid = $_GET['id'];
	prep_venue_qrys($conn);
	
	function showVenueDetails($conn, $vid)
	{
		$root = "/venues";
		$latitude = "";
		$longitude = "";
		$name = "";
		$description = "";
		$tag = "";
		$picture = "";
		
		$result = pg_execute($conn, "get_venues_details", array($vid)) or die("Can't execute get_venues_details: " . pg_last_error());
		if (pg_num_rows($result) == 1)
		{
			$row = pg_fetch_row($result);
			$latitude = $row[0];
			$longitude = $row[1];
			$name = $row[2];
			$description = $row[3];
			if ($description == "" || $description == null) $description = 'No description available';
			$tag = $row[4];
			$picture = $root."/".$row[5];
			$is_secret = false;
			pg_freeresult($result);
		}
		else 
		{
			pg_freeresult($result);
			
			$latitude = -62.2184041;
			$longitude = -58.9601368;
			$name = "Secret Venue";
			$description = "No description available";
			$tag = "";
			$picture = $root."/img/venue/logo/secret.png";
			$is_secret = true;
			
			echo "<script type='text/javascript'>
			jError('Cannot find venue...<br />',
				{
					onClosed:function()
					{
						window.location.href='" . $root . "/index.php';
					}
				}
			);
			</script>";
		}
		
		$food_rating = 0;
		$service_rating = 0;
		$value_rating = 0;
		$atmosphere_rating = 0;
		$total_rating = 0;
		
		$food_count = 0;
		$service_count = 0;
		$value_count = 0;
		$atmosphere_count = 0;
		$total_count = 0;
		
		$result = pg_execute($conn, "get_venue_ratings", array($vid)) or die("Can't execute get_venue_ratings: " . pg_last_error());
		if (pg_num_rows($result) == 0)
		{
			$rating = 'No Ratings Yet!';
		}
		else if (pg_num_rows($result) == 4)
		{
			while ($row = pg_fetch_row($result))
			{
					 if ($row[1] == 1) { $food_rating 		= round($row[3], 2);	$food_count 		= $row[2]; }
				else if ($row[1] == 2) { $service_rating 	= round($row[3], 2);	$service_count 		= $row[2]; }
				else if ($row[1] == 3) { $value_rating 		= round($row[3], 2);	$value_count 		= $row[2]; }
				else if ($row[1] == 4) { $atmosphere_rating = round($row[3], 2);	$atmosphere_count 	= $row[2]; }
			}
			$total_rating = round(($food_rating + $service_rating + $value_rating + $atmosphere_rating) / 4.0, 2);
			if ($food_count == $service_count && $food_count == $value_count && $food_count == $atmosphere_count)
			{
				$total_count = $food_count;
			}
			pg_freeresult($result);
			

			$rating = '
			<p>
				<b><i class="fa fa-star fa-lg"></i> Rating: </b>' . $total_rating . '&emsp;&emsp;
				<b>Votes: </b>
				<span class="fa-stack fa-1x">
					<i class="fa fa-circle fa-stack-2x"></i>
					<strong class="fa-stack-1x fa-stack-text fa-inverse">' . $total_count . '</strong>
				</span>
			</p>
			<p style="margin: 6px 0px 10px 40px; line-height: 9px; padding: 0px 0px 10px;">
				<span style="display:box; width:115px; clear:both; float:left;"><b>Food: </b>' . $food_rating . '</span>
				<span style="display:box; width:115px; float:left;"><b>Service: </b>' . $service_rating . '</span>
			</p>
			<p style="margin: 6px 0px 10px 40px; line-height: 9px; padding: 0px 0px 10px;">
				<span style="display:box; width:115px; clear:both; float:left;"><b>Value: </b>' . $value_rating . '</span>
				<span style="display:box; width:115px; float:left;"><b>Atmosphere: </b>' . $atmosphere_rating . '</span>
			</p>';
		}
		
		$location = get_address($latitude, $longitude);
		
		echo '
			<!--div class="container"> <!--Container-->
				<div class="tile rounded-border" style="max-width: 1430px;">
						<h2 class="tile-title rounded-border-top" style="line-height:1.0em; padding-top:7px;"><b><i class="fa fa-info-circle fa-2x"></i><span style="font-size:13px; vertical-align:3px;">&nbsp;&nbsp;' . $name . '</span></b></h2> 
						<div class="fb span9 offset1" style="background: url(' . $root . '/img/venue/logo/Coffee-Beans.png) no-repeat;">  <!--Cover-->
							<div class="profilePhoto"> <img src="' . $picture . '" alt="' . $name . ' Photo" /> </div>
						</div>  <!--Cover-->
						<div class="panel span9 offset1"> <!--Panel -->
							<div class="row"> <!--Row -->
								<div class="span9"> <!--Panel Inside -->
									<div class="row push"> <!--Row -->
										<!--First Line Panel -->
										<div class="span3 offset2 "> 
											<p class="name">' . $name . '</p>
										</div>
										<!--First Line Panel-->
										<!--Details -->
										<div class="span4 details "> 
											<p><i class="fa fa-map-marker fa-lg"></i> ' . $location['formatted_address'] . ' </p>
											' . $rating . '
										</div>
										<!--Details -->
									</div><!--Row -->
								</div> <!--Panel Inside -->
							</div>  <!--Row -->
						</div> <!--Panel -->
				</div>
			<!--/div> <!--Container-->
		';
	}
	
	function showRatingsForm($conn, $uid, $vid, $is_secret)
	{
		$root = "/venues";
		if ($uid != null && $uid != 'GUEST' && $is_secret == false)
		{
			$food_rating = 0;
			$service_rating = 0;
			$value_rating = 0;
			$atmosphere_rating = 0;
			$total_rating = 0;
			
			$result = pg_execute($conn, "get_uv_ratings", array($uid, $vid)) or die("Can't execute get_uv_ratings: " . pg_last_error());
			if (pg_num_rows($result) == 4)
			{
				while ($row = pg_fetch_row($result))
				{
						 if ($row[3] == 1) $food_rating 		= $row[4];
					else if ($row[3] == 2) $service_rating 		= $row[4];
					else if ($row[3] == 3) $value_rating 		= $row[4];
					else if ($row[3] == 4) $atmosphere_rating 	= $row[4];
				}
				$total_rating = ($food_rating + $service_rating + $value_rating + $atmosphere_rating) / 4.0;
				pg_freeresult($result);
			}
			
			echo '
				<!-- Ratings -->
				<div class="col-md-5">
					<div class="tile rounded-border">
						
						<div class="container">
							<div class="examples">
								<div class="slider-example">
									<div>
										<div class="listview">
										<form name="rateForm" action="' . $root . '/rate_venue.php" method="post">
											<!-- Total Rating --> 
											<h2 class="tile-title text-center rounded-border-top" style="line-height:1.0em; padding-top:7px;"><b><i class="fa fa-star fa-2x"></i><span style="font-size:13px; vertical-align:3px;">&nbsp;&nbsp;Total Rating:<span id="ratingsSliderValLabel"> <span id="ratingsSliderVal">Not rated</span></span></span></b></h2>
						
											<div class="media text-center" style="padding-top:15px;">
												<br />
												<input id="ratings" data-slider-id="ratingsSlider" type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-tooltip="show" data-slider-value="'.$total_rating.'" data-slider-handle="custom"/><br />
											</div>
											<!-- End of Total Rating --> 
										
											<!-- Food Rating --> 
											<h2 class="tile-title text-center" style="line-height:1.0em; padding-top:7px;"><b><i class="fa fa-star fa-2x"></i><span style="font-size:13px; vertical-align:3px;">&nbsp;&nbsp;Food:<span id="fratingsSliderValLabel"> <span id="fratingsSliderVal">Not rated</span></span></span></b></h2>
											
											<div class="media text-center" style="padding-top:15px;">
												<br />
												<input id="fratings" data-slider-id="fratingsSlider" type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-tooltip="show" data-slider-value="'.$food_rating.'" data-slider-handle="custom"/><br />
											</div>
											<!-- End of Food Rating --> 
										
											<!-- Service Rating --> 
											<h2 class="tile-title text-center" style="line-height:1.0em; padding-top:7px;"><b><i class="fa fa-star fa-2x"></i><span style="font-size:13px; vertical-align:3px;">&nbsp;&nbsp;Service:<span id="sratingsSliderValLabel"> <span id="sratingsSliderVal">Not rated</span></span></span></b></h2>
											
											<div class="media text-center" style="padding-top:15px;">
												<br />
												<input id="sratings" data-slider-id="sratingsSlider" type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-tooltip="show" data-slider-value="'.$service_rating.'" data-slider-handle="custom"/><br />
											</div>
											<!-- End of Service Rating -->
											
											<!-- Value Rating -->
											<h2 class="tile-title text-center" style="line-height:1.0em; padding-top:7px;"><b><i class="fa fa-star fa-2x"></i><span style="font-size:13px; vertical-align:3px;">&nbsp;&nbsp;Value:<span id="vratingsSliderValLabel"> <span id="vratingsSliderVal">Not rated</span></span></span></b></h2>
											
											<div class="media text-center" style="padding-top:15px;">
												<br />
												<input id="vratings" data-slider-id="vratingsSlider" type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-tooltip="show" data-slider-value="'.$value_rating.'" data-slider-handle="custom"/><br />
											</div>
											<!-- End of Value Rating -->
											
											<!-- Atmosphere Rating --> 
											<h2 class="tile-title text-center" style="line-height:1.0em; padding-top:7px;"><b><i class="fa fa-star fa-2x"></i><span style="font-size:13px; vertical-align:3px;">&nbsp;&nbsp;Atmosphere:<span id="aratingsSliderValLabel"> <span id="aratingsSliderVal">Not rated</span></span></span></b></h2>
											
											<div class="media text-center" style="padding-top:15px;">
												<br />
												<input id="aratings" data-slider-id="aratingsSlider" type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-tooltip="show" data-slider-value="'.$atmosphere_rating.'" data-slider-handle="custom"/><br />
											</div>
											<!-- End of Atmosphere Rating -->
											<input type="hidden" name="user_id" id="user_id" value="'.$uid.'" />
											<input type="hidden" name="venue_id" id="venue_id" value="'.$vid.'" />
											<input type="hidden" name="total" id="total" value="'.$total_rating.'" />
											<input type="hidden" name="food" id="food" value="'.$food_rating.'" />
											<input type="hidden" name="service" id="service" value="'.$service_rating.'" />
											<input type="hidden" name="value" id="value" value="'.$value_rating.'" />
											<input type="hidden" name="atmosphere" id="atmosphere" value="'.$atmosphere_rating.'" />
											<input class="btn btn-default" type="submit" name="submit" id="submit" style="color:#fff;" value="Rate venue" />
										</form>
										</div>
										
									</div>
								</div> <!-- /slider-example -->
							</div> <!-- /examples -->
						</div> <!-- /container -->
							
					</div>	<!-- End of tile -->
				</div>	<!-- End of Ratings column -->
			';
		}
	}
	
	function get_address($lat, $lng)
	{
		$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$lng;
		$data = @file_get_contents($url);
		$jsondata = json_decode($data,true);
		if(is_array($jsondata) && $jsondata['status'] == "OK")
		{
			$location = array();
			
			$result = $jsondata["results"]["0"];
			foreach ($result['address_components'] as $component) {

				switch ($component['types']) {
				  case in_array('street_number', $component['types']):
					$location['number'] = $component['long_name'];
					break;
				  case in_array('route', $component['types']):
					$location['street'] = $component['long_name'];
					$location['street_short'] = $component['short_name'];
					break;
				  case in_array('neighborhood', $component['types']):
					$location['neighborhood'] = $component['long_name'];
					break;
				  case in_array('locality', $component['types']):
					$location['city'] = $component['long_name'];
					break;
				  case in_array('sublocality', $component['types']):
					$location['sublocality'] = $component['long_name'];
					break;
				  case in_array('administrative_area_level_3', $component['types']):
					$location['admin_3'] = $component['long_name'];
					break;
				  case in_array('administrative_area_level_4', $component['types']):
					$location['admin_4'] = $component['long_name'];
					break;
				  case in_array('administrative_area_level_5', $component['types']):
					$location['admin_5'] = $component['long_name'];
					break;
				  case in_array('postal_code', $component['types']):
					$location['postal_code'] = $component['long_name'];
					break;
				  case in_array('country', $component['types']):
					$location['country'] = $component['long_name'];
					break;
				}
			}
			$location['formatted_address'] = $result["formatted_address"];

		}
		
		return $location;
	}
?>
<html style="overflow-y:auto;">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<meta name="format-detection" content="telephone=no">
	<meta charset="UTF-8">
	
	<title>Venue | </title>
		
	<!-- CSS -->
	<link href="<?php echo "$root/"; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo "$root/"; ?>css/bootstrap-slider.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo "$root/"; ?>css/animate.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo "$root/"; ?>css/form.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo "$root/"; ?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo "$root/"; ?>css/login.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo "$root/"; ?>css/style.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo "$root/"; ?>css/jNotify.jquery.css" rel="stylesheet" type="text/css" />
	
	<link href="<?php echo "$root/"; ?>css/custom.css" rel="stylesheet" media="screen" />
	
	<script src="<?php echo "$root/"; ?>js/jquery.min.js" type="text/javascript" ></script> <!-- jQuery Library -->
	<script src="<?php echo "$root/"; ?>js/jNotify.jquery.min.js" type="text/javascript" ></script>

</head>
<body id="skin-blur-lights">
	<div class="clearfix"></div>
	<section id="main">
		<!-- Header -->
		<header id="header" class="media">
			<?php
				show_header_bar($conn, $uid);
				login_msg($uid);
			?>
			
		</header>
		
		<!-- Sidebar -->
		<aside id="sidebar" style="height:100%;">
			<?php show_side_bar("index"); ?>
			
			<!-- Sidbar Widgets -->
			<?php show_profile_menu($conn, $uid); ?>
		</aside>
		
		<!-- Content -->
		<section id="content" class="container">
		<div class="block-area">
			<?php showVenueDetails($conn, $vid); ?>
			
			<div class="row">
				
				<?php showRatingsForm($conn, $uid, $vid, $is_secret); ?>
				
			</div>	<!-- End of row -->
			
			<!-- DISQUS -->
			<div class="tile rounded-border">
				<div id="disqus_thread" style="border-radius: 15px; padding: 15px;"></div>
			</div>
		</div>	<!-- End of block-area -->
		
		
		</section>
	</section>
	<!-- jQuery -->
	<script src="<?php echo "$root/"; ?>js/jquery.easing.1.3.js" type="text/javascript" ></script> <!-- jQuery Easing - Required for Lightbox + Pie Charts-->
	
	<!-- Bootstrap -->
	<script src="<?php echo "$root/"; ?>js/bootstrap.min.js" type="text/javascript" ></script>
	<script src="<?php echo "$root/"; ?>js/bootstrap-slider.js" type="text/javascript" ></script>
	

	<!-- All JS functions -->
	<script src="<?php echo "$root/"; ?>js/venue.js" type="text/javascript" ></script>
	<script src="<?php echo "$root/"; ?>js/scroll.min.js" type="text/javascript" ></script> <!-- Custom Scrollbar -->
	<script src="<?php echo "$root/"; ?>js/functions.js" type="text/javascript" ></script>
	
	<!-- DISQUS -->
	<script type="text/javascript">
		/* * * CONFIGURATION VARIABLES * * */
		var disqus_shortname = 'v4venues';
		
		/* * * DON'T EDIT BELOW THIS LINE * * */
		(function() {
			var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
			dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
			(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
		})();
	</script>

	<script type="text/javascript">
		/* * * CONFIGURATION VARIABLES * * */
		var disqus_shortname = 'v4venues';
		
		/* * * DON'T EDIT BELOW THIS LINE * * */
		(function () {
			var s = document.createElement('script'); s.async = true;
			s.type = 'text/javascript';
			s.src = '//' + disqus_shortname + '.disqus.com/count.js';
			(document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
		}());
	</script>
	<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a></noscript>

	<script type="text/javascript">
		/* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
		var disqus_shortname = 'v4venues'; // required: replace example with your forum shortname

		/* * * DON'T EDIT BELOW THIS LINE * * */
		(function () {
			var s = document.createElement('script'); s.async = true;
			s.type = 'text/javascript';
			s.src = '//' + disqus_shortname + '.disqus.com/count.js';
			(document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
		}());
	</script> 
</body>
</html>