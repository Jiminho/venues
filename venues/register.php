<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" class="ie6 ielt8"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="ie7 ielt8"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html lang="en"> <!--<![endif]-->
<meta charset="utf-8">
<?php 
	$root = "/venues";
	require_once (realpath($_SERVER["DOCUMENT_ROOT"])."/venues/session.php");
	if(!empty($_SESSION['uid']))
		header("location: ".$root."/index.php");
 ?>
<head>
<title>Venues | Register</title>
<link href="<?php echo "$root/"; ?>css/jNotify.jquery.css" rel="stylesheet" type="text/css" />
<script src="<?php echo "$root/"; ?>js/jquery.min.js" type="text/javascript" ></script>
<script src="<?php echo "$root/"; ?>js/jNotify.jquery.js" type="text/javascript" ></script>
</head>
<body>
<div class="container">
<?php
if(isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['password2']) 
&& $_POST['username'] != NULL && $_POST['email'] != NULL && $_POST['password'] != NULL && $_POST['password2'] != NULL)
{
	$username = $_POST['username'];
	$username = strip_tags($username);				// plain text only
	$username = str_replace(' ', '', $username);	// remove blank spaces
	
	$email = $_POST['email'];
	$email = strip_tags($email);					// plain text only
	$email = str_replace(' ', '', $email);			// remove blank spaces
	
	$password = $_POST['password'];
	$password = strip_tags($password);
	$password2 = $_POST['password2'];
	$password2 = strip_tags($password2);
	
	if ($password != $password2)	// password was not entered correctly
	{
		echo "<script type='text/javascript'>
		jError('You did not re-enter the same password.<br />Please try registering again!',
			{
				onClosed:function()
				{
					window.location.href='" . $root . "/index.php'
				}
			}
		);
		</script>";
		exit;
	}
	
	//$key = null;
	prep_qrys($conn);
	// Check if username exists in database
	$result = pg_execute($conn, "check_username", array($username)) or die("Can't execute check_username: " . pg_last_error());
	$username_exists = pg_num_rows($result);
	pg_freeresult($result);
	
	// Check if email exists in database
	$result = pg_execute($conn, "check_email", array($email)) or die("Can't execute check_email: " . pg_last_error());
	$email_exists = pg_num_rows($result);
	pg_freeresult($result);
	
	if ($username_exists)	// username already exists
	{
		echo "<script type='text/javascript'>
		jError('This username is already taken...<br />Please choose another one!',
			{
				onClosed:function()
				{
					window.location.href='" . $root . "/index.php'
				}
			}
		);
		</script>";
		exit;
	}
	else if ($email_exists)	// email already exists
	{
		echo "<script type='text/javascript'>
		jError('This mail has already been registered...<br />Please subscribe using another mail,<br />or log in using your existing username!',
			{
				onClosed:function()
				{
					window.location.href='" . $root . "/index.php'
				}
			}
		);
		</script>";
		exit;
	}
	else // register new username
	{
		$key = generateRandomString();
		$password = hash_pwd($password, $key);
		pg_execute($conn, "register_user", array($username, $password, $key)) or die("Can't execute register_user: " . pg_last_error());
		
		$result = pg_execute($conn, "check_username", array($username)) or die("Can't execute check_username: " . pg_last_error());		
		if (pg_num_rows($result) == 1)
		{
			$row = pg_fetch_row($result);
			$uid = $row[0];
			pg_freeresult($result);
			$registered = pg_execute($conn, "register_profile", array($uid, $email, "Undefined", "Undefined"));
			//or die("Can't execute register_profile: " . pg_last_error());
			if (!$registered)
			{
				pg_execute($conn, "delete_user", array($uid)) or die("Can't execute delete_user: " . pg_last_error());	
			}
			else
			{
				pg_freeresult($registered);
				$registered = pg_execute($conn, "register_stats", array($uid));
				if (!$registered)
				{
					pg_execute($conn, "delete_profile", array($uid)) or die("Can't execute delete_profile: " . pg_last_error());
					pg_execute($conn, "delete_user", array($uid)) or die("Can't execute delete_user: " . pg_last_error());
				}
			}
		}
		
		if ($registered)
		{
			pg_freeresult($registered);
			dealloc_qrys($conn);
			echo "<script type='text/javascript'>
			jSuccess('You have been registered! Please log in!',
				{
					onClosed:function()
					{
						window.location.href='" . $root . "/index.php'
					}
				}
			);
			</script>";
			exit;
		}
		else
		{
			pg_freeresult($registered);
			dealloc_qrys($conn);
			echo "<script type='text/javascript'>
			jError('Oops, something went wrong...',
				{
					onClosed:function()
					{
						window.location.href='" . $root . "/index.php'
					}
				}
			);
			</script>";
			exit;
		}
	}
}
else
	header("Location: " . $root . "/index.php");
exit;
?>
</div><!-- container -->
</body>
</html>
