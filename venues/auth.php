<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" class="ie6 ielt8"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="ie7 ielt8"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html lang="en"> <!--<![endif]-->
<meta charset="utf-8">
<?php 
	$root = "/venues";
	require_once (realpath($_SERVER["DOCUMENT_ROOT"])."/venues/session.php");
?>
<title>Venues | Login</title>
<link rel="stylesheet" type="text/css" href="<?php echo "$root/"; ?>css/jNotify.jquery.css" />
<script type="text/javascript" src="<?php echo "$root/"; ?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo "$root/"; ?>js/jNotify.jquery.js"></script>
</head>
<body>
<div class="container">
<?php
if(isset($_POST['username']) && isset($_POST['password']) && $_POST['username'] != NULL)
{
	// Set username
	$username = $_POST['username'];
	
	// Set password and strip slashes for security
	$password = $_POST['password'];
	$password = stripslashes($password);
	
	// initialize key to null
	$key = null;
	
	// prepare statements
	prep_qrys($conn);
	
	// get key using the username
	$result = pg_execute($conn, "get_key", array($username)) or die("Can't execute get_key: " . pg_last_error());
	
	// There should be no more than one entry with that username
	if (pg_num_rows($result) == 1)
	{
		$row = pg_fetch_row($result);
		$key = $row[0];
		pg_freeresult($result);
	}
	// The username was not in the database
	else
	{
		pg_freeresult($result);
		//dealloc_qrys($conn);
		//pg_close($conn);
		//header('Location: index.php');
		echo "<script type='text/javascript'>
		jError('Invalid username/password combination.',
			{
				onClosed:function()
				{
					window.location.href='".$root."'
				}
			}
		);
		</script>";
	}
	
	// Encrypt password
	$password = hash_pwd($password, $key);
	
	// Get uid using username and encrypted password
	$result = pg_execute($conn, "get_uid", array($username, $password)) or die("Can't execute get_uid: " . pg_last_error());
	// There should be no more than one entry with this username / encrypted password combination
	if (pg_num_rows($result) == 1)
	{
		$row = pg_fetch_row($result);
		$uid = $row[0];
		pg_freeresult($result);
		
		$randkey = generateRandomString();
		
		// Store the user id in session
		$_SESSION['uid']= encrypt($uid, $randkey); // storing username in session
		$_SESSION['LOGGED'] = 'true';
		
		$result = pg_execute($conn, "update_sess_key", array($randkey, $_COOKIE['Biscuit'])) or die("Can't execute update_sess_key: " . pg_last_error());
		pg_freeresult($result);
		
		$result = pg_execute($conn, "update_login_date", array($uid)) or die("Can't execute 'update_login_date': " . pg_last_error());
		pg_freeresult($result);
		
		dealloc_qrys($conn);
		//pg_close($conn);
		header("Location: ".$_SERVER['PHP_SELF']);
	}
	else
	{
		pg_freeresult($result);
		dealloc_qrys($conn);
		//pg_close($conn);
		echo "<script type='text/javascript'>
		jError('Invalid username or password... Please try again!',
			{
				onClosed:function()
				{
					window.location.href='".$root."'
				}
			}
		);
		</script>";
	}
}
else
	header("Location: ".$root);
//exit;
?>
</div><!-- container -->
</body>
</html>
