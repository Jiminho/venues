<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php 
	$root = "/venues";
	require_once (realpath($_SERVER["DOCUMENT_ROOT"])."/venues/session.php");
	$uid = check_login($conn, 600, 'Biscuit');
?>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<meta name="format-detection" content="telephone=no">
	<meta charset="UTF-8">
	
	<title>Αρχική Σελίδα</title>
		
	<!-- CSS -->
	<link href="<?php echo "$root/"; ?>css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo "$root/"; ?>css/animate.min.css" rel="stylesheet">
	<link href="<?php echo "$root/"; ?>css/font-awesome.min.css" rel="stylesheet">
	<link href="<?php echo "$root/"; ?>css/login.css" rel="stylesheet">
	<link href="<?php echo "$root/"; ?>css/style.css" rel="stylesheet">
	<link href="<?php echo "$root/"; ?>css/icons.css" rel="stylesheet">
	
	<link href="<?php echo "$root/"; ?>css/jNotify.jquery.css" rel="stylesheet" type="text/css" />

	<!-- jQuery -->
	<script src="<?php echo "$root/"; ?>js/jquery.min.js"></script> <!-- jQuery Library -->
	<script src="<?php echo "$root/"; ?>js/jquery-ui.min.js"></script> <!-- jQuery UI -->
	<script src="<?php echo "$root/"; ?>js/jquery.easing.1.3.js"></script> <!-- jQuery Easing - Required for Lightbox + Pie Charts-->
	
	<!-- Bootstrap -->
	<script src="<?php echo "$root/"; ?>js/bootstrap.min.js"></script>
	

	<!-- All JS functions -->
	<script src="<?php echo "$root/"; ?>js/scroll.min.js"></script>
	<script src="<?php echo "$root/"; ?>js/functions.js"></script>
	<script src="<?php echo "$root/"; ?>js/jNotify.jquery.min.js"></script>

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
		<aside id="sidebar">
			<?php show_side_bar("index"); ?>
			
			<!-- Sidbar Widgets -->
			<?php show_profile_menu($conn, $uid); ?>
		</aside>
		
		<!-- Content -->
		<section id="content" class="container">
			<div id="map-canvas"></div>
		</section>
	</section>
	
	<?php /*
	<!-- Javascript Libraries -->
	<!-- jQuery Libraries -->
	<script src="<?php echo "$root/"; ?>js/jquery.min.js"></script> <!-- jQuery Library -->
	<script src="<?php echo "$root/"; ?>js/jquery-ui.min.js"></script> <!-- jQuery UI -->
	<script src="<?php echo "$root/"; ?>js/jquery.easing.1.3.js"></script> <!-- jQuery Easing - Required for Lightbox + Pie Charts-->
	
	<!-- Bootstrap -->
	<script src="<?php echo "$root/"; ?>js/bootstrap.min.js"></script>
	
	<!-- All JS functions -->
	<script src="<?php echo "$root/"; ?>js/functions.js"></script>
	*/ ?>
</body>
</html>