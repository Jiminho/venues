<!DOCTYPE html>
<?php 
	$root = "/venues";
	require_once (realpath($_SERVER["DOCUMENT_ROOT"])."/venues/session.php");
	$uid = check_login($conn, 600, 'Biscuit');
	if($uid == null || $uid == 'GUEST')
		header("location: ".$root."/index.php");
		
	// Upload avatar picture function
	function upload($uid, $name="")
	{
		$parent = dirname(dirname($_SERVER['PHP_SELF']));
		$parent = 'venues';
		// Make a note of the directory that will receive the uploaded files
		//$bgdir = $_SERVER['DOCUMENT_ROOT'] . $parent . '/images/bg/';
		$avatar_dir = $_SERVER['DOCUMENT_ROOT'] . "/" . $parent . '/img/user/';
		
		
		// Around 10MB of file size
		$maxsize = 10000000;
		$fieldname = "avatar_file";
		
		// Check the upload form was actually submitted
		if (isset($_POST['submit']))
		{	// Check associated error code
			if (($error = $_FILES[$fieldname]['error']) == UPLOAD_ERR_OK)
			{	// Check whether file is uploaded with HTTP POST
				if (is_uploaded_file($_FILES[$fieldname]['tmp_name']))
				{	// Check image size
					if( $_FILES[$fieldname]['size'] < $maxsize )
					{
						if (empty($name))
						{
							// Give a unique name to the image
							$now = time();
							while(file_exists($avatar_name = $avatar_dir.$now.'_'.$uid.'_'.$_FILES[$fieldname]['name']))
							{
								$now++;
							}
							$avatar_filename = $now.'_'.$uid.'_'.$_FILES[$fieldname]['name'];
						}
						else
						{
							$avatar_name = $avatar_dir.$name;
							$avatar_filename = $name;
						}
						// Success...
						if (move_uploaded_file($_FILES[$fieldname]['tmp_name'], $avatar_name))
						{
							$uploaded = 1;
							$avatar_name = $avatar_filename;
						}
						// or failure...
						else
						{
							$uploaded = 0;
							$avatar_name = "";
							echo "<script type='text/javascript'>
							jError('Lack of write permission!',
								{
									HorizontalPosition : 'left',
									VerticalPosition : 'bottom',
									onClosed:function()
									{
										window.location.href='".$root."/profile/'
									}
								}
							);
							</script>";
						}
					}
					else	// Failed image file check
					{
						$uploaded = 0;
						$avatar_name = "";
						echo "<script type='text/javascript'>
						jError('Only image uploads are allowed!',
							{
								HorizontalPosition : 'left',
								VerticalPosition : 'bottom',
								onClosed:function()
								{
									window.location.href='".$root."/profile/'
								}
							}
						);
						</script>";
					}
				}
				else	// Failed HTTP POST upload check
				{
					$uploaded = 0;
					$avatar_name = "";
					echo "<script type='text/javascript'>
					jError('Not an http upload!',
						{
							HorizontalPosition : 'left',
							VerticalPosition : 'bottom',
							onClosed:function()
							{
								window.location.href='".$root."/profile/'
							}
						}
					);
					</script>";
				}
			}
			else	// Returned error code other than UPLOAD_ERR_OK
			{
				$uploaded = 0;
				$avatar_name = "";
				
				// Choose what error message to display
				switch ($error) {
					case UPLOAD_ERR_INI_SIZE:
						$error_code = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
					case UPLOAD_ERR_FORM_SIZE:
						$error_code = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
					case UPLOAD_ERR_PARTIAL:
						$error_code = 'The uploaded file was only partially uploaded';
					case UPLOAD_ERR_NO_FILE:
						$error_code = 'No file was uploaded';
					case UPLOAD_ERR_NO_TMP_DIR:
						$error_code = 'Missing a temporary folder';
					case UPLOAD_ERR_CANT_WRITE:
						$error_code = 'Failed to write file to disk';
					case UPLOAD_ERR_EXTENSION:
						$error_code = 'File upload stopped by extension';
					default:
						$error_code = 'Unknown upload error';
				}
				// and display the error message
				echo "<script type='text/javascript'>
				jError('Standard upload error ($error): $error_code',
					{
						HorizontalPosition : 'left',
						VerticalPosition : 'bottom',
						onClosed:function()
						{
							window.location.href='".$root."/profile/'
						}
					}
				);
				</script>";
			}
		}
		else	// The upload form was not submitted
		{
			$uploaded = 0;
			$avatar_name = "";
			echo "<script type='text/javascript'>
			jError('The upload form is needed!',
				{
					HorizontalPosition : 'left',
					VerticalPosition : 'bottom',
					onClosed:function()
					{
						window.location.href='".$root."/profile/'
					}
				}
			);
			</script>";
		}
		
		return $avatar_name;
	}
?>	
<html>
<head>
<title>Venues | Edit Avatar</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="<?php echo "$root/"; ?>css/jNotify.jquery.css" />
<script type="text/javascript" src="<?php echo "$root/"; ?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo "$root/"; ?>js/jNotify.jquery.js"></script>
</head>
<body>
<?php
	// Check from where we get the request
	if(isset($_POST['delete']))
	{
		do
		{
			$deleted = pg_execute($conn, "delete_stats", array($uid)) or die("Can't execute delete_stats: " . pg_last_error());
		} while (!$deleted);
		do
		{
			$deleted = pg_execute($conn, "delete_profile", array($uid)) or die("Can't execute delete_profile: " . pg_last_error());
		} while (!$deleted);
		do
		{
			$deleted = pg_execute($conn, "delete_user", array($uid)) or die("Can't execute delete_user: " . pg_last_error());
		} while (!$deleted);
		
		session_destroy();
		
		echo "<script type='text/javascript'>
		jError('Deleted account...<br />',
			{
				onClosed:function()
				{
					window.location.href='".$root."/index.php';
				}
			}
		);
		</script>";
		exit();
	}
	
	if(!isset($_POST['avatar']) || $_POST['avatar'] != 'avatar')
	{
		echo "<script type='text/javascript'>
		jError('Nice try...<br />',
			{
				onClosed:function()
				{
					window.location.href='".$root."/profile/';
				}
			}
		);
		</script>";
		exit();
	}
	// Check if the user id has been tampered
	if ($uid != $_POST['id'])
	{
		echo "<script type='text/javascript'>
		jError('Invalid user id: " . $_POST['id'] . "<br />',
			{
				onClosed:function()
				{
					window.location.href='".$root."/profile/';
				}
			}
		);
		</script>";
		exit();
	}
	
	prep_profile_qrys($conn);
	
	if (isset($_POST['default_avatar']) && $_POST['default_avatar'] == 'default')
	{
		$result = pg_execute($conn, "update_avatar", array("", $uid)) or die("Can't execute update_avatar: " . pg_last_error());
		$avatar_exists = pg_num_rows($result);
		pg_freeresult($result);
		
		echo "<script type='text/javascript'>
		jSuccess('Using default avatar!<br />',
			{
				onClosed:function()
				{
					window.location.href='".$root."/profile/';
				}
			}
		);
		</script>";
		exit();
	}
	/*// Check if an avatar is selected
	else if(!isset($_FILES['avatar_file']))
	{
		echo "<script type='text/javascript'>
		jError('Please select an avatar image<br />',
			{
				onClosed:function()
				{
					window.location.href='".$root."/profile/';
				}
			}
		);
		</script>";
	}*/
	else 
	{
		$avatar_name = $_FILES["avatar_file"]["name"];
		if ($avatar_name == "")
		{
			echo "<script type='text/javascript'>
			jError('Please select an avatar image<br />',
				{
					onClosed:function()
					{
						window.location.href='".$root."/profile/';
					}
				}
			);
			</script>";
			exit();
		}
		
		//$name = upload($avatar_name);
		$name = upload($uid);
		$name = "img/user/" . $name;
		
		if ($name != "")
		{
			$result = pg_execute($conn, "update_avatar", array($name, $uid)) or die("Can't execute update_avatar: " . pg_last_error());
			$avatar_exists = pg_num_rows($result);
			pg_freeresult($result);
			//$updated = mysql_query("UPDATE dyn_menu SET label='$label', body='$body' WHERE id='$id'");
			
			echo "<script type='text/javascript'>
			jSuccess('Updated avatar!<br />',
				{
					onClosed:function()
					{
						window.location.href='".$root."/profile/';
					}
				}
			);
			</script>";
			exit();
		}
		else
		{
			$updated = 0;
			echo "<script type='text/javascript'>
			jError('Error updating avatar... Could not upload avatar image!',
				{
					onClosed:function()
					{
						history.back()
					}
				}
			);
			</script>";
			exit();
		}
	}
?>
</body>
</html>