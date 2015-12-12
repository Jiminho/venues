<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php 
	$root = "/venues";
	require_once (realpath($_SERVER["DOCUMENT_ROOT"])."/venues/session.php");
	$uid = check_login($conn, 600, 'Biscuit');
	
	// We are checking the favorites using a specific user's id
	if(isset($_GET['user'])) {
		$id = $_GET['user'];					// Set id as the one that is provided by the url
		// We are not logged in
		if($uid == null || $uid == 'GUEST') {
			$is_owner = false;					// it is not our favorites, since we are not logged in
			$is_guest = true;					// we are in guest mode, since we are not logged in
		} else {
			$is_guest = false;								// We are logged in
			if ($_GET['user'] == $uid) $is_owner = true;	// We are checking our own favorites
			else $is_owner = false;							// We are checking another user's favorites
		}
	} else {		// No id is given in url. We want to check our own favorites
		$id = $uid;								// The id we are using is ours
		if($uid == null || $uid == 'GUEST') {	// But we are not logged in
			$is_guest = true;					// We are in guest mode, since we are not logged in
			$is_owner = false;					// Guests do not have their own favorites
			header("location: ".$root);			// So, we should go back to the home page
			exit;
		} else {								// We are logged in
			$is_guest = false;					// So, we are not a guest
			$is_owner = true;					// and we are checking our own favorites
		}
	}
	
	// Check privacy setting for the requested user's stats
	$sql = 'SELECT privacy FROM tProfile WHERE uid = '.$id;
	$result = pg_query($conn, $sql) or die("Cannot execute query: $sql\n");
	// Found results
	if (pg_num_rows($result) == 1) {
		$row = pg_fetch_row($result);
		$privacy = $row[0];
		
		if (($privacy == 'O' && $is_owner) || ($privacy == 'M' && !$is_guest) || $privacy == 'G') {
			//$allow_access = true;
			if ($privacy == 'G') { $privacy = 3; }
			else if ($privacy == 'M') { $privacy = 2; }
			else { $privacy = 1;}
		} else {
			//$allow_access = false;
			header("location: ".$root);
			exit;
		}
		
		pg_freeresult($result);
	} else {
		if ($is_owner) 
			header("location: ".$root."/logout.php");
		else
			header("location: ".$root);
		exit;
	}
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
	<script src="<?php echo "$root/"; ?>js/jquery.validate.min.js"></script>
	<script src="<?php echo "$root/"; ?>js/additional-methods.min.js"></script>
	
	<!-- Bootstrap -->
	<script src="<?php echo "$root/"; ?>js/bootstrap.min.js"></script>
	

	<!-- All JS functions -->
	<script src="<?php echo "$root/"; ?>js/scroll.min.js"></script>
	<script src="<?php echo "$root/"; ?>js/functions.js"></script>
	<script src="<?php echo "$root/"; ?>js/jNotify.jquery.min.js"></script>

</head>
<body id="skin-blur-lights">
	<div class="clearfix"></div>
	<section id="main" style="overflow-y: scroll;">
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
			<div class="block-area">
				<div class="row">
					<div class="col-md-5">
						<div class="tile rounded-border">
							<h2 class="tile-title rounded-border-top" style="line-height:1.0em; padding-top:7px;"><b><i class="fa fa-heart fa-2x"></i><span style="font-size:13px; vertical-align:3px;">&nbsp;&nbsp;Favorite Venues</span></b></h2>
							
							<div class="listview">
<?php 
							// If the user is not defined go to homepage
							if ($id == null)
							{
								echo "<script type='text/javascript'>
									jError('You did not select a valid user id',
										{
											onClosed:function()
											{
												window.location.href='".$root."/profile/'
											}
										}
									);
									</script>";
								exit;
							}
							// User is defined, continue
							else
							{
								$sql='SELECT f.vid, v.vname, vct.vctimg, AVG(r.rating) FROM tFav f, tRating r, tVenue v, tVenueCategoryType vct WHERE f.uid = '.$id.' AND f.vid = v.vid AND r.vid = v.vid AND v.vctid = vct.vctid GROUP BY 1,2,3 ORDER BY 4 DESC';
								
								// get search results
								$result = pg_query($conn, $sql) or die("Cannot execute query: $sql\n");
								// Found results
								if (pg_num_rows($result) > 0)
								{
									$rating_results = '';
									while ($row = pg_fetch_row($result)) {
										$rating_results = $rating_results.'
										<div class="media">
											<div style="float:left; margin: 0px 10px 10px 0px;">
												<a href="'.$root.'/venue/'.$row[0].'"><img src="'.$root.'/'.$row[2].'" alt="Venue" width="100" /></a><br />
											</div>
											<div style="float:left;">
												<b>Venue</b>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row[1].'<br />
												<b>Rating</b>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.round($row[3], 2).'<br /><br />
												<a href="'.$root.'/venue/'.$row[0].'"><b>Visit venue page</b></a><br />
											</div>
										</div>
										';
									}
									pg_freeresult($result);
									echo $rating_results;
									exit;
								}
								// User has not rated any venues
								else
								{
									pg_freeresult($result);
									echo "<script type='text/javascript'>
									jError('This user has not tagged any venues as favorites',
										{
											onClosed:function()
											{
												window.location.href='".$root."/profile/".$id."/'
											}
										}
									);
									</script>";
									exit;
								}
							}
?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</section>
</body>
</html>