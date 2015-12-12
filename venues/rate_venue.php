<!DOCTYPE html>
<?php
	$root = "/venues";
	require_once (realpath($_SERVER["DOCUMENT_ROOT"])."/venues/session.php");
	$uid = check_login($conn, 600, 'Biscuit');
	if($uid == null || $uid == 'GUEST')
		header("location: ".$root."/index.php");
?>
<html>
<head>
<title>Venues | Rate Venue</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<link href="<?php echo "$root/"; ?>css/jNotify.jquery.css" rel="stylesheet" type="text/css" />
<script src="<?php echo "$root/"; ?>js/jquery.min.js" type="text/javascript" ></script>
<script src="<?php echo "$root/"; ?>js/jNotify.jquery.js" type="text/javascript" ></script>
</head>
<body>
<?php
// Check if the venue id is set
if (!isset($_POST['venue_id']))
{
	echo "<script type='text/javascript'>
	jError('Venue id is not set...<br />',
		{
			onClosed:function()
			{
				window.location.href='" . $root . "/index.php';
			}
		}
	);
	</script>";
	exit();
}
else $vid = $_POST['venue_id'];

// Check if the user id is set
if (!isset($_POST['user_id']))
{
	echo "<script type='text/javascript'>
	jError('User id is not set...<br />',
		{
			onClosed:function()
			{
				window.location.href='" . $root . "/venue/" . $_POST['venue_id'] . "/';
			}
		}
	);
	</script>";
	exit();
}
if ($uid != $_POST['user_id'])
{
	echo "<script type='text/javascript'>
	jError('Invalid user id: " . $_POST['user_id'] . "<br />',
		{
			onClosed:function()
			{
				window.location.href='" . $root . "/venue/" . $_POST['venue_id'] . "/';
			}
		}
	);
	</script>";
	exit();
}

// Check if any ratings are missing
if (!isset($_POST['food']) || !isset($_POST['service']) || !isset($_POST['value']) || !isset($_POST['atmosphere']))
{
	echo "<script type='text/javascript'>
	jError('Missing ratings<br />',
		{
			onClosed:function()
			{
				window.location.href='" . $root . "/venue/" . $_POST['venue_id'] . "/';
			}
		}
	);
	</script>";
	exit();
}
else
{
	$food_rating = $_POST['food'];
	$service_rating = $_POST['service'];
	$value_rating = $_POST['value'];
	$atmosphere_rating = $_POST['atmosphere'];
}

prep_venue_qrys($conn);

// Check if the user has rated the venue before
$result = pg_execute($conn, "get_uv_ratings", array($uid, $vid)) or die("Can't execute get_uv_ratings: " . pg_last_error());

prep_rating_qrys($conn);

// If the user rates this venue for the first time  
if (pg_num_rows($result) == 0)
{
	pg_freeresult($result);

	// Insert user's ratings
	$result = pg_execute($conn, "set_rating", array($uid, $vid, 1, $food_rating)) or die("Can't execute set_rating(" . $food_rating . "): " . pg_last_error());
	pg_freeresult($result);
	$result = pg_execute($conn, "set_rating", array($uid, $vid, 2, $service_rating)) or die("Can't execute set_rating(" . $service_rating . "): " . pg_last_error());
	pg_freeresult($result);
	$result = pg_execute($conn, "set_rating", array($uid, $vid, 3, $value_rating)) or die("Can't execute set_rating(" . $value_rating . "): " . pg_last_error());
	pg_freeresult($result);
	$result = pg_execute($conn, "set_rating", array($uid, $vid, 4, $atmosphere_rating)) or die("Can't execute set_rating(" . $atmosphere_rating . "): " . pg_last_error());
	pg_freeresult($result);
}
// If the user changes his rating for the venue
else if (pg_num_rows($result) == 4)
{
	pg_freeresult($result);
	
	// Update user's ratings
	$result = pg_execute($conn, "update_rating", array($food_rating, $uid, $vid, 1)) or die("Can't execute update_rating(" . $food_rating . "): " . pg_last_error());
	pg_freeresult($result);
	$result = pg_execute($conn, "update_rating", array($service_rating, $uid, $vid, 2)) or die("Can't execute update_rating(" . $service_rating . "): " . pg_last_error());
	pg_freeresult($result);
	$result = pg_execute($conn, "update_rating", array($value_rating, $uid, $vid, 3)) or die("Can't execute update_rating(" . $value_rating . "): " . pg_last_error());
	pg_freeresult($result);
	$result = pg_execute($conn, "update_rating", array($atmosphere_rating, $uid, $vid, 4)) or die("Can't execute update_rating(" . $atmosphere_rating . "): " . pg_last_error());
	pg_freeresult($result);
}
// If we have an invalid number of entries, something has clearly gone wrong... 
else 
{
	pg_freeresult($result);
	
	// Delete user's invalid ratings
	$result = pg_execute($conn, "delete_ratings", array($uid, $vid)) or die("Can't execute delete_ratings: " . pg_last_error());
	pg_freeresult($result);
	
	// Insert user's ratings
	$result = pg_execute($conn, "set_rating", array($uid, $vid, 1, $food_rating)) or die("Can't execute set_rating(" . $food_rating . "): " . pg_last_error());
	pg_freeresult($result);
	$result = pg_execute($conn, "set_rating", array($uid, $vid, 2, $service_rating)) or die("Can't execute set_rating(" . $service_rating . "): " . pg_last_error());
	pg_freeresult($result);
	$result = pg_execute($conn, "set_rating", array($uid, $vid, 3, $value_rating)) or die("Can't execute set_rating(" . $value_rating . "): " . pg_last_error());
	pg_freeresult($result);
	$result = pg_execute($conn, "set_rating", array($uid, $vid, 4, $atmosphere_rating)) or die("Can't execute set_rating(" . $atmosphere_rating . "): " . pg_last_error());
	pg_freeresult($result);
}
// Update number of ratings
$result = pg_execute($conn, "update_ratings_number", array($uid)) or die("Can't execute update_ratings_number: " . pg_last_error());
pg_freeresult($result);

echo "<script type='text/javascript'>
jSuccess('Thanks for rating this venue!<br />',
	{
		onClosed:function()
		{
			window.location.href='" . $root . "/venue/" . $vid . "/';
		}
	}
);
</script>";

exit();
?>
</body>
</html>