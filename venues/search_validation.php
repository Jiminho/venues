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
<title>Venues | Search</title>
<link rel="stylesheet" type="text/css" href="<?php echo "$root/"; ?>css/jNotify.jquery.css" />
<script type="text/javascript" src="<?php echo "$root/"; ?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo "$root/"; ?>js/jNotify.jquery.js"></script>
</head>
<body>
<div class="container">
<?php
if(empty($_POST['uname']) && empty($_POST['fname']) && empty($_POST['lname']) && empty($_POST['email']))
{
	echo "<script type='text/javascript'>
	jError('I guess you are too lazy to type a search term...',
		{
			onClosed:function()
			{
				window.location.href='".$root."/search/people/'
			}
		}
	);
</script>";
	exit;
}
else
{
	$where_clause = '';
	$people = '';
	
	if(isset($_POST['uname']) && !empty($_POST['uname'])) $uname = $_POST['uname'];
	else $uname = null;
	if(isset($_POST['fname']) && !empty($_POST['fname'])) $fname = $_POST['fname'];
	else $fname = null;
	if(isset($_POST['lname']) && !empty($_POST['lname'])) $lname = $_POST['lname'];
	else $lname = null;
	if(isset($_POST['email']) && !empty($_POST['email'])) $email = $_POST['email'];
	else $email = null;
	
	if ($uname != null)
	{
		$where_clause = $where_clause . "lower(u.uname) = lower('" . $uname . "') ";
	}
	if ($fname != null)
	{
		if ($where_clause == '') $where_clause = $where_clause . "lower(p.fname) = lower('" . $fname . "') ";
		else $where_clause = $where_clause . "OR lower(p.fname) = lower('" . $fname . "') ";
	}
	if ($lname != null)
	{
		if ($where_clause == '') $where_clause = $where_clause . "lower(p.lname) = lower('" . $lname . "') ";
		else $where_clause = $where_clause . "OR lower(p.lname) = lower('" . $lname . "') ";
	}
	if ($email != null)
	{
		if ($where_clause == '') $where_clause = $where_clause . "lower(p.email) = lower('" . $email . "') ";
		else $where_clause = $where_clause . "OR lower(p.email) = lower('" . $email . "') ";
	}
	
	$date = date('d/m/Y H:i:s:ms', time());
	//echo "<script>console.log( '[DEBUG] " . $date . ": " . $where_clause . "' );</script>";
	//echo $where_clause;
	
	$sql='SELECT u.uid, u.uname, p.fname, p.lname, p.email, p.pic, p.sex FROM tUser u, tProfile p WHERE ('.$where_clause.') AND u.uid = p.uid';
	
	// get search results
	$result = pg_query($conn, $sql) or die("Cannot execute query: $sql\n");
	// There should be no more than one entry with that username
	if (pg_num_rows($result) > 0)
	{
		$people = '';
		/*
		$people = '
			<div class="block-area">
				<div class="row">
					<div class="col-md-15">
						<div class="tile rounded-border">
							<div class="listview">';
				*/		
		while ($row = pg_fetch_row($result)) {
			
			$sex = $row[6];
			if ($sex == 'M') { $gender = 'Male'; $avatar = $root.'/img/user/male.png'; }
			else if ($sex == 'F') { $gender = 'Female'; $avatar = $root.'/img/user/female.png'; }
			else { $gender = 'Unknown'; $avatar = $root.'/img/user/undefined.png'; }
			
			if ($row[5] != '') $avatar = $root.'/'.$row[5];
			
			$people = $people.'
								<div class="media">
									<div style="float:left; margin: 0px 10px 10px 0px;">
										<img src="'.$avatar.'" alt="Avatar" width="100"><br />
									</div>
									<div style="float:left;">
										Username:   '.$row[1].'<br />
										First Name: '.$row[2].'<br />
										Last Name:  '.$row[3].'<br />
										e-mail:     '.$row[4].'<br />
										gender:     '.$gender.'<br />
										<a href="'.$root.'/profile/'.$row[0].'">Visit profile</a><br />
									</div>
								</div>
			';
		}
		/*
		$people = $people.'
							</div>
						</div>
					</div>
				</div>
			</div>';*/
		pg_freeresult($result);
		
		// md5 for venues_search_people
		$key = '371555a819ed7a48f8c117e4cf6832a3';
		$people_results = base64_url_encode(encrypt($people, $key));
		header("Location: ".$root."/search/people/".$people_results);
		/*
		$p1 = encrypt($people, $key);
		echo "1) ".$p1."<br />";
		//echo '<script type="text/javascript">alert('.$people_results.')</script>';
		
		$p2 = base64_url_encode($p1);
		echo "2) ".$p2."<br />";
		//echo "<script type='text/javascript'>console.log('people_results: ".$people_results.")</script>";
		$p3 = base64_url_decode($p2);
		echo "3) ".$p3."<br />";
		//echo "<script type='text/javascript'>console.log('people_results: ".$decoded.")</script>";
		$p4 = decrypt($p3, $key);
		echo "4) ".$p4."<br />5)";
		//echo "<script type='text/javascript'>console.log('people_results: ".$decoded.")</script>";
		*/
		//header("Location: ".$root."/search/people/".$people);
	}
	// The username was not in the database
	else
	{
		pg_freeresult($result);
		//dealloc_qrys($conn);
		//pg_close($conn);
		//header('Location: index.php');
		echo "<script type='text/javascript'>
		jError('No results found. Please try again',
			{
				onClosed:function()
				{
					window.location.href='".$root."/search/people/'
				}
			}
		);
		</script>";
		exit;
	}
	
	//header("Location: ".$root."/search/people/");
}
//exit;
?>
</div><!-- container -->
</body>
</html>
