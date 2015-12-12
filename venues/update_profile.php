<?php 
	$root = "/venues";
	require_once (realpath($_SERVER["DOCUMENT_ROOT"])."/venues/session.php");
	$uid = check_login($conn, 600, 'Biscuit');
	if($uid == null || $uid == 'GUEST')
		header("location: ".$root);
	
	if(!isset($_POST["name"]) || empty($_POST["name"]))
	{
		header("location: ".$root);
		exit();
	}
	else
		$name = $_POST["name"];
	
	if ($uid != $_POST['pk'])
	{
		header('HTTP 412 Precondition Failed', true, 412);
		echo "Invalid user id: " . $_POST['pk'] . "\n";
		exit();
	}
	
	prep_profile_qrys($conn);
	
	if($name == "name")
	{
		$firstname = $_POST['value'];
		$firstname = strip_tags($firstname);			// plain text only
		$firstname = stripslashes($firstname);			// plain text only
		$firstname = clean($firstname);					// remove undesired characters
		//$firstname = str_replace('"', '', $firstname);	// remove quotes
		//$firstname = str_replace("'", "", $firstname);	// remove quotes
		
		if ($firstname != $_POST['value'])
		{
			header('HTTP 406 Not Acceptable', true, 406);
			echo "This name contains invalid characters.\nDid you mean: $firstname?\n";
		}
		else
		{
			if ($firstname == '') $firstname = 'Undefined';
			else if (strlen($firstname) > 32)
			{
				header('HTTP 406 Not Acceptable', true, 406);
				echo "This name contains too many characters.\nName may only be set as: " . substr($firstname, 0, 32) . "\n";
				exit();
				//return ;
			}
			
			$result = pg_execute($conn, "update_firstname", array($firstname, $uid)) or die("Can't execute update_firstname: " . pg_last_error());
			$row = pg_fetch_row($result);
			pg_freeresult($result);
		}
		exit();
		//return ;
	}
	else if($name == "surname")
	{
		$lastname = $_POST['value'];
		$lastname = strip_tags($lastname);				// plain text only
		$lastname = stripslashes($lastname);			// plain text only
		$lastname = clean($lastname);					// remove undesired characters
		//$lastname = str_replace('"', '', $lastname);	// remove quotes
		//$lastname = str_replace("'", "", $lastname);	// remove quotes
		
		if ($lastname != $_POST['value'])
		{
			header('HTTP 406 Not Acceptable', true, 406);
			echo "This surname contains invalid characters.\nDid you mean: $lastname?\n";
		}
		else
		{
			if ($lastname == '') $lastname = 'Undefined';
			else if (strlen($lastname) > 32)
			{
				header('HTTP 406 Not Acceptable', true, 406);
				echo "This surname contains too many characters.\nSurname may only be set as: " . substr($lastname, 0, 32) . "\n";
				exit();
				//return ;
			}
			
			$result = pg_execute($conn, "update_lastname", array($lastname, $uid)) or die("Can't execute update_lastname: " . pg_last_error());
			$row = pg_fetch_row($result);
			pg_freeresult($result);
		}
		exit();
		//return ;
	}
	else if($name == "username")
	{
		$username = $_POST['value'];
		if ($username == '')
		{
			header('HTTP 406 Not Acceptable', true, 406);
			echo "Cannot use blank username...\n";
			exit();
			//return ;
		}
		$username = strip_tags($username);				// plain text only
		$username = stripslashes($username);			// plain text only
		$username = str_replace(' ', '', $username);	// remove blank spaces
		$username = str_replace('"', '', $username);	// remove quotes
		$username = str_replace("'", "", $username);	// remove quotes
		if ($username != $_POST['value'])
		{
			header('HTTP 406 Not Acceptable', true, 406);
			echo "This username contains invalid characters.\nDid you mean: $username?\n";
			exit();
			//return ;
		}
		
		// get key using the username
		$result = pg_execute($conn, "check_username", array($username)) or die("Can't execute check_username: " . pg_last_error());
		
		// The username is already taken
		if (pg_num_rows($result) == 1)
		{
			$result = pg_execute($conn, "get_username", array($uid)) or die("Can't execute get_username: " . pg_last_error());
			$row = pg_fetch_row($result);
			$username = $row[0];
			pg_freeresult($result);
			
			header('HTTP 501 Not Implemented', true, 501);
			echo "This username is already taken!";
		}
		else
		{
			pg_freeresult($result);
			$result = pg_execute($conn, "update_username", array($username, $uid)) or die("Can't execute update_username: " . pg_last_error());
			$row = pg_fetch_row($result);
			pg_freeresult($result);
		}
		
		exit();
		//return ;
	}
	else if($name == "password")
	{
		$password = $_POST['value'];
		if ($password == '')
		{
			header('HTTP 406 Not Acceptable', true, 406);
			echo "Cannot use blank password...\n";
			exit();
			//return ;
		}
		$password = strip_tags($password);				// plain text only
		$password = stripslashes($password);			// plain text only
		$password = str_replace(' ', '', $password);	// remove blank spaces
		$password = str_replace('"', '', $password);	// remove quotes
		$password = str_replace("'", "", $password);	// remove quotes
		if ($password != $_POST['value'])
		{
			header('HTTP 406 Not Acceptable', true, 406);
			echo "This password contains invalid characters.\n";
			exit();
			//return ;
		}
		
		// Get hash key
		$result = pg_execute($conn, "get_key2", array($uid)) or die("Can't execute get_key2: " . pg_last_error());
		$row = pg_fetch_row($result);
		$hkey = $row[0];
		pg_freeresult($result);
		/*
		if ($hkey == '')
		{
			header('HTTP 406 Not Acceptable', true, 406);
			echo "There was a problem updating user's password.\n";
			exit();
			//return ;
		}
		*/
		// Hash password with user's hash key
		$password = hash_pwd($password, $hkey);
		
		// update password
		$result = pg_execute($conn, "update_password", array($password, $uid)) or die("Can't execute update_password: " . pg_last_error());
		$row = pg_fetch_row($result);
		pg_freeresult($result);
		
		exit();
		//return ;
	}
	else if($name == "email")
	{
		// Get email
		$email = $_POST['value'];
		
		// Remove all illegal characters from email
		$email = filter_var($email, FILTER_SANITIZE_EMAIL);

		// Validate e-mail
		if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false)
		{
			// get key using the email
			$result = pg_execute($conn, "check_email", array(strtolower($email))) or die("Can't execute check_email: " . pg_last_error());
			// The email is already taken
			if (pg_num_rows($result) == 1)
			{
				pg_freeresult($result);
				
				header('HTTP 501 Not Implemented', true, 501);
				echo "This email is already taken!";
			}
			else
			{
				pg_freeresult($result);
				$result = pg_execute($conn, "update_email", array(strtolower($email), $uid)) or die("Can't execute update_email: " . pg_last_error());
				pg_freeresult($result);
			}
		}
		else
		{
			header('HTTP 406 Not Acceptable', true, 406);
			echo "$email is not a valid email address" ;
			exit();
			//return ;
		}
	}
	else if($name == "gender")
	{
		// Get gender
		if ($_POST['value'] == 2) $gender = 'M';
		else if ($_POST['value'] == 3) $gender = 'F';
		else $gender = 'U';
		
		// Update gender
		$result = pg_execute($conn, "update_gender", array($gender, $uid)) or die("Can't execute update_gender: " . pg_last_error());
		pg_freeresult($result);
		exit();
		//return ;
	}
	else if($name == "privacy")
	{
		
		// Update privacy
		$result = pg_execute($conn, "update_privacy", array($_POST['value'], $uid)) or die("Can't execute update_privacy: " . pg_last_error());
		pg_freeresult($result);
		exit();
		//return ;
	}
	else
	{
		header('HTTP 412 Precondition Failed', true, 412);
		echo "Invalid field: " . $_POST['name'] . "\n";
		exit();
		//return ;
	}
		//echo $_POST["value"];
		
	function clean($string) {
		return preg_replace('/[^A-Za-z\'\-]/', '', $string); // Removes special chars.
	}
?>
