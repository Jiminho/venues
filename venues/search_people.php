<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php 
	$root = "/venues";
	require_once (realpath($_SERVER["DOCUMENT_ROOT"])."/venues/session.php");
	$uid = check_login($conn, 600, 'Biscuit');
	
	// md5 for venues_search_people
	$key = '371555a819ed7a48f8c117e4cf6832a3';
	
	if (isset($_GET['people']))
	{
		$people = $_GET['people'];
		$people = base64_url_decode($people);
	}
	else
	{
		$people = null;
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
			<?php show_side_bar("search_people"); ?>
			
			<!-- Sidbar Widgets -->
			<?php show_profile_menu($conn, $uid); ?>
		</aside>
		
		<!-- Content -->
		<section id="content" class="container">
			<div class="block-area">
				<div class="row">
					<div class="col-md-5">
						<div class="tile rounded-border">
							<h2 class="tile-title rounded-border-top" style="line-height:1.0em; padding-top:7px;"><b><i class="fa fa-search fa-2x"></i><span style="font-size:13px; vertical-align:3px;">&nbsp;&nbsp;Search people</span></b></h2>
							
							<div class="listview">
							
							<?php 
							if ($people == null)
							{
								echo '
								<div class="media">
									<form method="POST" action="validate/" class="form-horizontal" id="search_people" role="form">
										<div class="form-group">
											<label class="control-label col-sm-3" for="uname">Username:</label>
											<div class="col-sm-7">          
												<input type="text" class="form-control" name="uname" id="uname" placeholder="Search username">
											</div>
										</div>
										
										<div class="form-group">
											<label class="control-label col-sm-3" for="fname">First name:</label>
											<div class="col-sm-7">          
												<input type="text" class="form-control" name="fname" id="fname" placeholder="Search first name">
											</div>
										</div>
										
										<div class="form-group">
											<label class="control-label col-sm-3" for="lname">Last name:</label>
											<div class="col-sm-7">          
												<input type="text" class="form-control" name="lname" id="lname" placeholder="Search last name">
											</div>
										</div>
										
										<div class="form-group">
											<label class="control-label col-sm-3" for="email">e-mail:</label>
											<div class="col-sm-7">          
												<input type="text" class="form-control" name="email" id="email" placeholder="Search e-mail">
											</div>
										</div>
										
										<div class="form-group form-actions">        
											<div class="col-sm-offset-3 col-sm-10">
												<button type="submit" class="btn btn-success">Submit</button>
												<button type="reset" class="btn btn-default">Reset</button>
											</div>
										</div>
									</form>
								</div>';
							}
							else
							{
								echo decrypt($people, $key);
							}
							?>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<?php /*
				// md5 for venues_search_people
				$key = '371555a819ed7a48f8c117e4cf6832a3';
				
				// Check search results
				if (isset($_GET['people']))
				{
					$people = $_GET['people'];
					$people = base64_url_decode($people);
					$search_results = true;
				}
				else
				{
					$people = null;
					$search_results = false;
				}
				
				if ($search_results)
				{
					$search = decrypt($people, $key);
					
					echo "<script type='text/javascript'>
						jError('<".$people.">',
							{
								autoHide : false,
								onClosed:function()
								{
									console.log('people: ".$people."');
									//console.log('search: ".$search."');
								}
							}
						);
						</script>";
						
					echo $search;
				}*/
			?>
		</section>
	</section>
	
	<script type="text/javascript">
		$(document).ready(function () {

			$('#search_people').validate({
				rules: {
					uname: {
						require_from_group: [1, ".form-control"]
					},
					fname: {
						require_from_group: [1, ".form-control"]
					},
					lname: {
						require_from_group: [1, ".form-control"]
					},
					email: {
						require_from_group: [1, ".form-control"],
						email: true
					}
				},
				highlight: function (element) {
					$(element).closest('.control-group').removeClass('success').addClass('error');
				},
				success: function (element) {
					element.text('OK!').addClass('valid')
						.closest('.control-group').removeClass('error').addClass('success');
				}
			});

		});
	</script>

	
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