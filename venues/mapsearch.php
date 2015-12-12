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
</head>
<body id="skin-blur-lights">
	<div class="clearfix"></div>
		<!-- Header -->
		<header id="header" class="media">
			<?php show_header_bar($conn, $uid); login_msg($uid); ?>
			
		</header>
		
		<!-- Sidebar -->
		<aside id="sidebar">
			<?php show_side_bar("mapsearch"); ?>
			<h4 style="padding: 0 5px;">Venues</h4>
			<p style="padding: 0 5px;">Click on 'Venues number!' button and each cluster will show the total number of venues in it!
			This view helps you check which area has the most venues gathered together!</p>
			<button id="cnt-btn" style="padding: 5px; margin: -4px 10px 5px; float: right;" type="button" class="btn btn-default" onclick='getPoints(<?php if($uid != 'GUEST' && $uid != null) echo $uid; else echo 0; ?>, <?php get_points($conn, $uid, TRUE);?>, false, false)'>Venues number!</button><br />
			<p style="padding: 0 5px;">Click on 'Average Ratings!' button and each cluster will show the average rating of the venues in it.
			This view helps you check which area has the top rated venues! All ratings are voted by the venues community.</p>
			<button id="avg-btn" style="padding: 5px; margin: 0px 10px 5px; float: right;" type="button" class="btn btn-default" onclick='getPoints(<?php if($uid != 'GUEST' && $uid != null) echo $uid; else echo 0; ?>, <?php get_points($conn, $uid, TRUE);?>, false, true)'>Average Ratings!</button><br />
			<p style="padding: 0 5px;">Click on 'Heatmap!' button to show a heatmap of all of the available venues!
			This view is a cool way of showing where the venues are gathered on the map!</p>
			<button id="heat-btn" style="padding: 5px; margin: 0px 10px 5px; float: right;" type="button" class="btn btn-default" onclick='getPoints(<?php if($uid != 'GUEST' && $uid != null) echo $uid; else echo 0; ?>, <?php get_points($conn, $uid, TRUE);?>, true, false)'>Heatmap!</button>
		</aside>
		
		<!-- Content >
		<section id="content" >
		</section-->
			<div id="content" class="map-canvas container"></div>
	
	
	<!-- Javascript Libraries -->
	<!-- Bootstrap -->
	<script src="<?php echo "$root/"; ?>js/bootstrap.min.js" ></script>
	
	<!-- All JS functions -->
	<script src="<?php echo "$root/"; ?>js/functions.js" ></script>
	
	<!-- Map -->
	<script src="https://maps.googleapis.com/maps/api/js?key=" ></script>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=visualization"></script>
	<script src="<?php echo "$root/"; ?>js/maptest22.js" language="javascript" type="text/javascript" ></script>
	<!--script src="<?php echo "$root/"; ?>js/custom-window.js" language="javascript" type="text/javascript" ></script-->
	<script src="<?php echo "$root/"; ?>js/search.js" language="javascript" type="text/javascript" ></script>
</body>
</html>