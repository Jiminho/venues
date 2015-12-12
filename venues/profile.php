<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php 
	$root = "/venues";
	require_once (realpath($_SERVER["DOCUMENT_ROOT"])."/venues/session.php");
	$uid = check_login($conn, 600, 'Biscuit');
	//if($uid == null || $uid == 'GUEST')
	//	header("location: ".$root."/index.php");
		
	// $id = (isset($_GET['id'])) ? $_GET['id'] : null;
	/*
	// Check other user's profile
	if (isset($_GET['id']) && $_GET['id'] != $uid)
	{
		$id = $_GET['id'];
		$is_owner = false;
	}
	// Check own profile
	else
	{
		// Guest users do not have profiles. Redirect them to home page.
		if($uid == null || $uid == 'GUEST') {
			header("location: ".$root);
			exit;
		}
		// Users are checking their own profile
		$id = $uid;
		$is_owner = true;
	}
	*/
	// We are checking a profile using a specific user's id
	if(isset($_GET['id'])) {
		$id = $_GET['id'];						// Set id as the one that is provided by the url
		// We are not logged in
		if($uid == null || $uid == 'GUEST') {
			$is_owner = false;					// it is not our profile, since we are not logged in
			$is_guest = true;					// we are in guest mode, since we are not logged in
		} else {
			$is_guest = false;							// We are logged in
			if ($_GET['id'] == $uid) $is_owner = true;	// We are checking our own profile
			else $is_owner = false;						// We are checking another user's profile
		}
	} else {		// No id is given in url. We want to check our own profile
		$id = $uid;								// The id we are using is ours
		if($uid == null || $uid == 'GUEST') {	// But we are not logged in
			$is_guest = true;					// We are in guest mode, since we are not logged in
			$is_owner = false;					// Guests do not have their own profile
			header("location: ".$root);			// So, we should go back to the home page
			exit;
		} else {								// We are logged in
			$is_guest = false;					// So, we are not a guest
			$is_owner = true;					// and we are checking our own profile
		}
	}
	
	//if ($is_owner)
	//	$result = pg_execute($conn, "get_profile_menu", array($uid)) or die("Can't execute get_profile_menu: " . pg_last_error());
	//else
		$result = pg_execute($conn, "get_profile_menu", array($id)) or die("Can't execute get_profile_menu: " . pg_last_error());
	
	if (pg_num_rows($result) == 1)
	{
		$row = pg_fetch_row($result);
		$username = $row[0];
		$name = $row[1];
		$surname = $row[2];
		
		$gender = $row[3];
		if ($gender == 'M') { $gender = 2; $avatar = $root.'/img/user/male.png'; }
		else if ($gender == 'F') { $gender = 3; $avatar = $root.'/img/user/female.png'; }
		else { $gender = 1; $avatar = $root.'/img/user/undefined.png'; }
		
		$picture = $row[4];
		if ($picture == '') { $picture = $avatar; }
		else $picture = $root . "/" . $picture;
		
		$email = $row[5];
		
		$privacy = $row[6];
		
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
	}
	else
	{
		if ($is_owner) 
			header("location: ".$root."/logout.php");
		else
			header("location: ".$root);
		exit;
	}	
	
	//if ($is_owner)
	//	$result = pg_execute($conn, "get_user_stats", array($uid)) or die("Can't execute get_user_stats: " . pg_last_error());
	//else
		$result = pg_execute($conn, "get_user_stats", array($id)) or die("Can't execute get_user_stats: " . pg_last_error());
		
	if (pg_num_rows($result) == 1)
	{
		$row = pg_fetch_row($result);
		$logdate = $row[0];
		$prevlogdate = $row[1];
		$regdate = $row[2];
		$likes = $row[3];
		$dislikes = $row[4];
		$ratings = $row[5];
		$checkins = $row[6];
		$visits = $row[7];
		$favorites = $row[8];
		$vidname = strtok($row[9], "|");
		if ($vidname !== false) {
			$vid = $vidname;
			$vidname = strtok("|");
			$vname = $vidname;
		} else {
			$vid = null;
			$vname = 'No favourite place...';
		}
		$level = $row[10];
		$xp = $row[11];
		pg_freeresult($result);
	}
	else
	{
		if ($is_owner) 
			header("location: ".$root."/logout.php");
		else
			header("location: ".$root);
	}
	
	function about($is_owner, $uid, $root, $gender)
	{
		if (!$is_owner) return;
		
		echo "$(document).ready(function() {
			//turn to inline mode
			$.fn.editable.defaults.mode = 'inline';
			//$.fn.editable.defaults.mode = 'popup';
			$('#name').editable({
				type: 'text',
				pk: " . $uid . ",
				url: '" . $root . "/update_profile.php',
				title: 'Enter name',
				tooltip   : 'Click to edit...'
			});
			$('#surname').editable({
				type: 'text',
				pk: " . $uid . ",
				url: '" . $root . "/update_profile.php',
				title: 'Enter surname'
			});
			$('#username').editable({
				type: 'text',
				pk: " . $uid . ",
				url: '" . $root . "/update_profile.php',
				title: 'Enter username'
			});
			$('#password').editable({
				type: 'password',
				pk: " . $uid . ",
				url: '" . $root . "/update_profile.php',
				title: 'Enter password'
			});
			$('#email').editable({
				type: 'text',
				pk: " . $uid . ",
				url: '" . $root . "/update_profile.php',
				title: 'Enter email'
			});
			$('#gender').editable({
				type: 'select',
				pk: " . $uid . ",
				url: '" . $root . "/update_profile.php',
				title: 'Choose your gender',
				value: " . $gender . ",
				source: [
					{value: 1, text: 'Undefined'},
					{value: 2, text: 'Male'},
					{value: 3, text: 'Female'}
				]
			});
			$('#privacy').editable({
				type: 'select',
				pk: " . $uid . ",
				url: '" . $root . "/update_profile.php',
				title: 'Choose who can view your profile',
				value: " . $gender . ",
				source: [
					{value: 'O', text: 'Only me'},
					{value: 'M', text: 'All members'},
					{value: 'G', text: 'All members and guests'}
				]
			});
			$('[data-toggle=confirmation]').confirmation({
				//onConfirm: function(){
				//	alert('AOUA')
				//}
			});
		});";
	}
?>
<html style="overflow-y:auto;">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<meta name="format-detection" content="telephone=no">
	<meta charset="UTF-8">
	
	<title>Προφίλ Χρήστη</title>
		
	<!-- CSS -->
	<link href="<?php echo "$root/"; ?>css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo "$root/"; ?>css/animate.min.css" rel="stylesheet">
	<link href="<?php echo "$root/"; ?>font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link href="<?php echo "$root/"; ?>css/login.css" rel="stylesheet">
	<link href="<?php echo "$root/"; ?>css/style.css" rel="stylesheet">
	<!--link href="css/icons.css" rel="stylesheet"-->
    <link href="<?php echo "$root/"; ?>css/generics.css" rel="stylesheet"> 
    <link href="<?php echo "$root/"; ?>css/form.css" rel="stylesheet">
	
	<link href="<?php echo "$root/"; ?>css/jNotify.jquery.css" rel="stylesheet" type="text/css" />
	<!--link href="<?php echo "$root/"; ?>css/jEditable.jquery.css" rel="stylesheet" type="text/css" /-->
	<link href="<?php echo "$root/"; ?>css/tip-twitter.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo "$root/"; ?>css/jquery-editable.css" rel="stylesheet" type="text/css" />
	

	<!-- jQuery -->
	<script src="<?php echo "$root/"; ?>js/jquery.min.js" type="text/javascript" ></script> <!-- jQuery Library -->
	<script src="<?php echo "$root/"; ?>js/jquery-ui.min.js" type="text/javascript" ></script> <!-- jQuery UI -->
	<script src="<?php echo "$root/"; ?>js/jquery.easing.1.3.js" type="text/javascript" ></script> <!-- jQuery Easing - Required for Lightbox + Pie Charts-->
	
	<!-- Bootstrap -->
	<script src="<?php echo "$root/"; ?>js/bootstrap.min.js"></script>
	

	<!-- All JS functions -->
	<script src="<?php echo "$root/"; ?>js/functions.js" type="text/javascript" ></script>
	<script src="<?php echo "$root/"; ?>js/jNotify.jquery.min.js" type="text/javascript" ></script>
	<script src="<?php echo "$root/"; ?>js/bootstrap-filestyle.min.js" type="text/javascript" > </script>
	<script src="<?php echo "$root/"; ?>js/bootstrap-confirmation.min.js" type="text/javascript" > </script>
	<!--script src="<?php echo "$root/"; ?>js/jEditable.jquery.min.js"></script>
	<script src="<?php echo "$root/"; ?>js/jquery.jeditable.js"></script>
	<script src="<?php echo "$root/"; ?>js/jquery.validate.min.js"></script-->

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
			<div class="row">
				
			<!-- About Me -->
            <div class="col-md-4">
				<div class="tile rounded-border">
					<h2 class="tile-title rounded-border-top" style="line-height:1.0em; padding-top:7px;"><b><i class="fa fa-info-circle fa-2x"></i><span style="font-size:13px; vertical-align:3px;">&nbsp;&nbsp;About <?php if ($is_owner) echo 'me'; else echo htmlspecialchars($username);?></span></b></h2>
					<!--div class="tile-config dropdown">
						<a data-toggle="dropdown" href="<?php echo "$root/"; ?>profile.php" class="tooltips tile-menu" title="" data-original-title="Options"></a>
						<ul class="dropdown-menu pull-right text-right"> 
							<li><a href="<?php echo "$root/"; ?>profile.php">Edit</a></li>
						</ul>
					</div-->
					
					<div class="listview">
					
						<div class="media">
							<a href="#" class="tooltip-right unset-top" data-tooltip="Name"><i class="fa fa-user fa-2x">&nbsp;</i></a>
							<a href="#" id="name"><?php echo $name; ?></a>
						</div>
						
						<div class="media">
							<a href="#" class="tooltip-right unset-top" data-tooltip="Surname"><i class="fa fa-user fa-2x">&nbsp;</i></a>
							<a href="#" id="surname"><?php echo $surname; ?></a>
						</div>
						
						<div class="media">
							<a href="#" class="tooltip-right unset-top" data-tooltip="Username"><i class="fa fa-user-secret fa-2x">&nbsp;</i></a>
							<a href="#" id="username"><?php echo htmlspecialchars($username); ?></a>
						</div>
						
						<div class="media">
							<a href="#" class="tooltip-right unset-top" data-tooltip="Password"><i class="fa fa-lock fa-2x">&nbsp;&nbsp;</i></a>
							<a href="#" id="password" type="password">[hidden]</a>
						</div>
						
						<div class="media">
							<a href="#" class="tooltip-right unset-top" data-tooltip="e-mail"><i class="fa fa-envelope fa-2x">&nbsp;</i></a>
							<a href="#" id="email"><?php echo $email; ?></a>
						</div>
						
						<div class="media">
							<a href="#" class="tooltip-right unset-top" data-tooltip="gender"><i class="fa fa-venus-mars fa-2x">&nbsp;</i></a>
							<a href="#" id="gender"><?php if($gender == 2) echo 'Male'; else if($gender == 3) echo 'Female'; else if($gender == 1) echo 'Undefined'; ?></a>
						</div>
						
						<div class="media">
							<a href="#" class="tooltip-right unset-top" data-tooltip="Privacy Setting">
								<span class="fa-stack fa-1x">
									<i class="fa fa-cog fa-stack-1x" style="left: 0.7em; margin-top: -0.7em;"></i>
									<i class="fa fa-lock fa-stack-2x"></i>
								</span>
								&nbsp;
							</a>
							<a href="#" id="privacy"><?php if($privacy == 1) echo 'Only '.htmlspecialchars($username); else if($privacy == 2) echo 'All members'; else if($gender == 3) echo 'All members and guests'; ?></a>
						</div>
						
						<div class="media">
							<a href="#" class="tooltip-right unset-top" data-tooltip="Image"><i class="fa fa-camera fa-2x">&nbsp;</i></a>
							<img src="<?php echo $picture; ?>" alt="Avatar" width="100">
							<div>
							<?php
							if ($is_owner)
							{
								echo '
								<form action="' . $root . '/edit_avatar.php" method="post" enctype="multipart/form-data"><br>
									<input type="checkbox" name="default_avatar" id="default_avatar" value="default" onclick="document.getElementById(\'avatar_label\').hidden=this.checked; document.getElementById(\'avatar_file\').disabled=this.checked; document.getElementById(\'avatar_file\').setAttribute(\'data-buttonText\', \'default\');
									toggle_file(this.checked);"> Use the default image for my gender.<br />
									<label id="avatar_label" for="file">Choose your avatar:</label><br>
									<input type="file" name="avatar_file" id="avatar_file" class="filestyle" data-buttonText="&nbsp;Choose avatar">
									<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
									<input type="hidden" name="avatar" id="avatar" value="avatar">
									<input type="hidden" name="id" id="id" value="' . $uid . '">
									<button type="submit" name="submit" id="submit" class="btn btn-default" style="color:#fff;">Change avatar</button>
									<button type="submit" name="delete" id="delete" class="btn btn-default" data-toggle="confirmation" data-target=>Delete account</button>
								</form>
								';
							}
							?>
							</div>
						</div>
						
					</div>
				</div>
			</div>	<!-- End of about me column -->
			
			<!-- Stats -->
            <div class="col-md-4">
				<div class="tile rounded-border">
					<h2 class="tile-title rounded-border-top" style="line-height:1.0em; padding-top:7px;"><b><i class="fa fa-map-marker fa-2x"></i><span style="font-size:13px; vertical-align:3px;">&nbsp;&nbsp;Venues</span></b></h2>
					
					<div class="listview">
					
						<div class="media">
							<table class="table" style="margin: 0px;">
								<tbody>
									<tr>
										<td style="border: 0px none; padding: 0px;"><b>Places:</b></td>
										<td style="border: 0px none; padding: 0px; text-align: right;">
										<?php echo '<a href="'.$root.'/visit/user/'.$id.'">'.$visits.'<b>&nbsp;&nbsp;<i class="fa fa-globe fa-2x"></i></b></a>'; ?>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						
						<div class="media">
							<table class="table" style="margin: 0px;">
								<tbody>
									<tr>
										<td style="border: 0px none; padding: 0px;"><b>Check-in:</b></td>
										<td style="border: 0px none; padding: 0px; text-align: right;">
										<?php echo '<a href="'.$root.'/checkin/user/'.$id.'">'.$checkins.'<b>&nbsp;&nbsp;<i class="fa fa-street-view fa-2x"></i></b></a>'; ?>
										</td>
									</tr>
								</tbody>
							</table>
						</div>

						<div class="media">
							<table class="table" style="margin: 0px;">
								<tbody>
									<tr>
										<td style="border: 0px none; padding: 0px;"><b>Ratings:</b></td>
										<td style="border: 0px none; padding: 0px; text-align: right">
										<?php echo '<a href="'.$root.'/rating/user/'.$id.'">'.$ratings.'<b>&nbsp;&nbsp;<i class="fa fa-star fa-2x"></i></b></a>'; ?>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
												
						<div class="media">
							<table class="table" style="margin: 0px;">
								<tbody>
									<tr>
										<td style="border: 0px none; padding: 0px;"><b>Favorites:</b></td>
										<td style="border: 0px none; padding: 0px; text-align: right">
										<?php echo '<a href="'.$root.'/favorite/user/'.$id.'">'.$favorites.'<b>&nbsp;&nbsp;<i class="fa fa-heart fa-2x"></i></b></a>'; ?>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						
						<div class="media">
							<table class="table" style="margin: 0px;">
								<tbody>
									<tr>
										<td style="border: 0px none; padding: 0px;"><b>Most visited place:</b></td>
										<td style="border: 0px none; padding: 0px; text-align: right">
											<b>
												<span class="fa-stack fa-1x">
													<i class="fa fa-heart-o fa-stack-2x"></i>
													<strong class="fa-stack-1x" style="margin-top: -0.1em;" >1</strong>
												</span>
											</b>
										</td>
									</tr>
									<tr>
										<td style="border: 0px none; padding: 0px;">
										<?php 
											if ($vid != null) { echo '<a href="'.$root.'/venue/'.$vid.'"><b>'.$vname.'</b></a>'; }
											else { echo '<b>'.$vname.'</b>'; }
										?>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						
					</div>	<!-- End of Places listview -->
				</div>	<!-- End of Places tile -->
			
				<!-- Messages -->
				<div class="tile rounded-border">
					<h2 class="tile-title rounded-border-top" style="line-height:1.0em; padding-top:7px;"><b><i class="fa fa-comments-o fa-2x"></i><span style="font-size:13px; vertical-align:3px;">&nbsp;&nbsp;Comments</span></b></h2>
					
					<div class="listview icon-list">
					
						<div class="media">
							<table class="table" style="margin: 0px;">
								<tbody>
									<tr>
										<td style="border: 0px none; padding: 0px;"><b>Voted Up:</b></td>
										<td style="border: 0px none; padding: 0px; text-align: right"><b><i class="fa fa-thumbs-up fa-2x">&nbsp;</i></b><?php echo " 0"; ?></td>
									</tr>
								</tbody>
							</table>
						</div>
						
						<div class="media">
							<table class="table" style="margin: 0px;">
								<tbody>
									<tr>
										<td style="border: 0px none; padding: 0px;"><b>Voted Down:</b></td>
										<td style="border: 0px none; padding: 0px; text-align: right"><b><i class="fa fa-thumbs-down fa-2x">&nbsp;</i></b><?php echo " 0"; ?></td>
									</tr>
								</tbody>
							</table>
						</div>
						
						<div class="media">
							<table class="table" style="margin: 0px;">
								<tbody>
									<tr>
										<td style="border: 0px none; padding: 0px;"><b>Number of Comments:</b></td>
										<td style="border: 0px none; padding: 0px; text-align: right"><b><i class="fa fa-comment fa-2x">&nbsp;</i></b><?php echo " 0"; ?></td>
									</tr>
								</tbody>
							</table>
						</div>
						
					</div>	<!-- End of Messages listview -->
				</div>	<!-- End of Messages tile -->
				
			</div>	<!-- End of Stats column -->
			
			<!-- Login -->
            <div class="col-md-3">
				<div class="tile rounded-border">
					<h2 class="tile-title rounded-border-top" style="line-height:1em; padding-top:7px;"><b><i class="fa fa-lock fa-2x"></i><span style="font-size:13px; vertical-align:3px;">&nbsp;&nbsp;Login</span></b></h2>
					
					<div class="listview icon-list">
					
						<div class="media">
							<table class="table" style="margin: 0px;">
								<tbody>
									<tr>
										<td style="border: 0px none; padding: 0px;"><b>Logged since:</b></td>
										<td style="border: 0px none; padding: 0px; text-align: right"><b><i class="fa fa-clock-o fa-2x">&nbsp;</i></b><?php echo substr($logdate, 0, 19); ?></td>
									</tr>
								</tbody>
							</table>
						</div>
						
					<div class="listview icon-list">
					
						<div class="media">
							<table class="table" style="margin: 0px;">
								<tbody>
									<tr>
										<td style="border: 0px none; padding: 0px;"><b>Last login:</b></td>
										<td style="border: 0px none; padding: 0px; text-align: right"><b><i class="fa fa-clock-o fa-2x">&nbsp;</i></b><?php echo substr($prevlogdate, 0, 19); ?></td>
									</tr>
								</tbody>
							</table>
						</div>
						
						<div class="media">
							<table class="table" style="margin: 0px;">
								<tbody>
									<tr>
										<td style="border: 0px none; padding: 0px;"><b>Registered:</b></td>
										<td style="border: 0px none; padding: 0px; text-align: right"><b><i class="fa fa-calendar fa-2x">&nbsp;</i></b><?php echo substr($regdate, 0, 19); ?></td>
									</tr>
								</tbody>
							</table>
						</div>
						
					</div>	<!-- End of Login listview -->
				</div>	<!-- End of Login tile -->
			</div>	<!-- End of Login column -->
			
		</div>	<!-- End of row -->
		</div>	<!-- End of block-area -->
		</section>
	</section>
        <script src="<?php echo "$root/"; ?>js/scroll.min.js"></script> <!-- Custom Scrollbar -->
	
	<?php /*
	<!-- Javascript Libraries -->
	<!-- jQuery Libraries -->
	<script src="<?php echo "$root/"; ?>js/jquery.min.js"></script> <!-- jQuery Library -->
	<script src="<?php echo "$root/"; ?>js/jquery-ui.min.js"></script> <!-- jQuery UI -->
	<script src="<?php echo "$root/"; ?>js/jquery.easing.1.3.js"></script> <!-- jQuery Easing - Required for Lightbox + Pie Charts-->
	
	<!-- Bootstrap -->
	<script src="<?php echo "$root/"; ?>js/bootstrap.min.js"></script>
	*/ ?>
	
	<!-- All JS functions -->
	<script src="<?php echo "$root/"; ?>js/jquery.poshytip.min.js" type="text/javascript" ></script>
	<script src="<?php echo "$root/"; ?>js/jquery-editable-poshytip.min.js" type="text/javascript" ></script>
	<script>
		<?php about($is_owner, $uid, $root, $gender); ?>
		
		function toggle_file(value)
		{
			if (!value)
				$(":file").filestyle();
			else
				$(':file').filestyle('destroy', true);
		}
	</script>
</body>
</html>