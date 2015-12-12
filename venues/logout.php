<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" class="ie6 ielt8"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="ie7 ielt8"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html lang="en"> <!--<![endif]-->
<head>
<?php header('Content-Type:text/html; charset=UTF-8');
	$root = "/venues";
	require_once (realpath($_SERVER["DOCUMENT_ROOT"])."/venues/session.php");
	// go to login page and destroy session
	if(empty($_SESSION['LOGGED']))
		header("location:".$root);
	session_destroy();
?>
<meta charset="utf-8">
<title>Logout</title>
<link href="<?php echo "$root/"; ?>css/jNotify.jquery.css" rel="stylesheet" type="text/css" />
<script src="<?php echo "$root/"; ?>js/jquery.min.js" type="text/javascript" ></script>
<script src="<?php echo "$root/"; ?>js/jNotify.jquery.js" type="text/javascript" ></script>
</head>
<body>
<?php
	echo "<script type='text/javascript'>
		jSuccess('You have successfully logged out!',
			{
				onClosed:function()
				{
					window.location.href='".$root."'
				}
			}
		);
		</script>";
?>
</body>
</html>