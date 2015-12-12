<?php
header('Content-type: text/html; charset=utf-8');

// Function that loads the header bar with info about the user
function show_header_bar($conn, $uid, $show_side_bar=true)
{
	$root = "/venues";
	
	// If the page has a sidebar, make a toggle sidebar button
	if($show_side_bar)
	{
		echo '
			<a href="#" id="menu-toggle"><i class="fa fa-align-justify fa-lg left" style="line-height: 1.4em; padding-left: 2px;"></i></a> 
			<a class="logo pull-left" href="'.$root.'"><b>Venues!</b></a>
		
		';
	}
	// If the user is not logged in, show log in and sign up buttons
	if ($uid == "GUEST" || $uid == null)
	{
		echo '
		<nav id="user_login">
		  <div style="display: block; float: right; position: absolute; right: 262px; top: 12px; font-size: medium;">Welcome, Guest Star!</div>
		  <ul>
			<li id="log-in">
			  <a id="log-in-trigger" href="#">
				Log in <span>▼</span>
			  </a>
			  <div id="log-in-content">
				<form method="post" action="'.$root.'/auth.php" name="loginForm" id="loginForm">
				  <fieldset class="inputs">
					<input id="username" type="text" name="username" placeholder="Username" autofocus required />   
					<input id="password" type="password" name="password" placeholder="Password" required />
				  </fieldset>
				  <fieldset class="actions">
					<input type="submit" id="submit" value="Log in">
					<label><input type="checkbox" checked="checked"> Remember me...</label>
				  </fieldset>
				</form>
			  </div>                     
			</li>
			<li id="signup">
			  <a id="signup-trigger" href="#">
				Sign up <span>▼</span>
			  </a>
			  <div id="signup-content">
				<form method="post" action="'.$root.'/register.php" name="regForm" id="regForm" onsubmit="return(validate_password());">
				  <fieldset class="inputs">
					<input id="username" type="text" name="username" placeholder="Username" autofocus="autofocus" required />   
					<input id="email" type="email" name="email" placeholder="e-mail" required />   
					<input id="password" type="password" name="password" placeholder="Password" required />
					<input id="password2" type="password" name="password2" placeholder="Confirm Password" required />
				  </fieldset>
				  <fieldset class="actions">
					<input type="submit" id="submit" value="Sign up">
				  </fieldset>
				</form>
			  </div>  
			</li>
		  </ul>
		</nav>
		';
		
	}
	// check if the user id belongs to a valid username
	else
	{
		// get username using the uid
		$result = pg_execute($conn, "get_username", array($uid)) or die("Can't execute get_username: " . pg_last_error());
		
		// There should be no more than one entry with that username
		if (pg_num_rows($result) == 1)
		{
			$row = pg_fetch_row($result);
			$username = $row[0];
			pg_freeresult($result);
		}
		// User not found... Refresh page to try again
		else
		{
			$username = "Guest Star";
			echo "<script type='text/javascript'>
			jError('Could not get username from user ID.',
				{
					onClosed:function()
					{
						window.location.href=".$_SERVER['PHP_SELF']."
					}
				}
			);
			</script>";
		}
		// Now that we confirmed that the user is logged in, show the user's info and a logout button
		echo '
		<nav>
			<div style="display: block; float: right; position: absolute; right: 189px; top: 13px; font-size: medium;">Welcome, '.$username.'!</div>
			<ul>
				<li id="log-out">
					<a id="log-out-trigger" href="'.$root.'/logout.php">
						Log out
					</a>
				</li>
			</ul>
		</nav>
		';
	}
	
	// Show clock
	echo '
		<div class="media-body">
			<div class="media" id="top-menu">
				<div id="time" class="pull-right">
					<span id="hours"></span>
					:
					<span id="min"></span>
					:
					<span id="sec"></span>
				</div>
			</div>
		</div>
	';
}

// Function that loads the sidebar with the pages menu
function show_side_bar($act) {
	$root = "/venues";
	
	echo '
			<!-- Side Menu -->
			<ul class="list-unstyled side-menu">
				<li'; if($act == "index") echo ' class="active"'; echo '>
					<a href="'.$root.'">
						<i class="fa fa-home fa-2x left" style="line-height: 1.7em;"></i>
						<span class="menu-item">Αρχικη</span>
					</a>
				</li>
				<li'; if($act == "mapsearch") echo ' class="active"'; echo '>
					<a href="'.$root.'/mapsearch.php">
						<i class="fa fa-globe fa-2x left" style="line-height: 1.7em;"></i>
						<span class="menu-item">Search Map</span>
					</a>
				</li>
				<li'; if($act == "search_places") echo ' class="active"'; echo '>
					<a href="'.$root.'/search/places/">
						<i class="fa fa-map-marker fa-2x left" style="line-height: 1.7em;"></i>
						<span class="menu-item">Search Places</span>
					</a>
				</li>
				<li'; if($act == "search_people") echo ' class="active"'; echo '>
					<a href="'.$root.'/search/people/">
						<i class="fa fa-user fa-2x left" style="line-height: 1.7em;"></i>
						<span class="menu-item">Search People</span>
					</a>
				</li>
			</ul>
	';
}

function show_profile_menu($conn, $uid)
{
	$root = "/venues";
	
	if($uid == "GUEST") $uid = null;
	// get username using the uid
	$result = pg_execute($conn, "get_profile_menu", array($uid)) or die("Can't execute get_profile_menu: " . pg_last_error());
	
	// There should be no more than one entry with that username
	if (pg_num_rows($result) == 1)
	{
		$row = pg_fetch_row($result);
		$username = $row[0];
		$firstname = $row[1];
		if ($firstname == 'Undefined') $firstname = 'Mysterious';
		$lastname = $row[2];
		if ($lastname == 'Undefined') $lastname = 'Stranger';
		$gender = $row[3];
		$pic = $row[4];
		if ($pic == null || $pic == "")
		{
			if ($gender == 'U') $pic = $root."/img/user/undefined.png";
			else if ($gender == 'M') $pic = $root."/img/user/male.png";
			else if ($gender == 'F') $pic = $root."/img/user/female.png";
		}
		else $pic = $root . "/" . $pic;
		pg_freeresult($result);
	}
	else
	{
		$uid = null;
		$username = "Guest Star";
		$firstname = "Guest";
		$lastname = "Star";
		$gender = 'U';
		$pic = $root."/img/user/undefined.png";
	}
	echo '
			<div class="side-widgets overflow">
				<!-- Profile Menu -->
				<div class="text-center s-widget m-b-25 dropdown" id="profile-menu">
					<a href="'.$root.'" data-toggle="dropdown">
						<img class="profile-pic animated" src="'.$pic.'" alt="">
					</a>';
	if ($uid != null)
	{
		echo '
					<ul class="dropdown-menu profile-menu">
						<li><a href="'.$root.'/profile/"><i class="fa fa-angle-left fa-2x left" style="line-height: 0.65em;"></i>Το προφίλ μου</a><i class="fa fa-angle-right fa-2x right" style="line-height: 0.65em;"></i></li>
						<li><a href="'.$root.'/messages.html"><i class="fa fa-angle-left fa-2x left" style="line-height: 0.65em;"></i>Μηνύματα</a><i class="fa fa-angle-right fa-2x right" style="line-height: 0.65em;"></i></a></li>
						<li><a href="'.$root.'"><i class="fa fa-angle-left fa-2x left" style="line-height: 0.65em;"></i>Ρυθμίσεις</a><i class="fa fa-angle-right fa-2x right" style="line-height: 0.65em;"></i></a></li>
						<li><a href="'.$root.'/logout.php"><i class="fa fa-angle-left fa-2x left" style="line-height: 0.65em;"></i>Αποσύνδεση</a><i class="fa fa-angle-right fa-2x right" style="line-height: 0.65em;"></i></a></li>
					</ul>';
	}
	echo '
					<h4 class="m-0">'.$firstname.' '.$lastname.'</h4>
					@'.$username.'
				</div>
			</div>
	';
}

// Function for logging Debug messages to the console
function logger( $data, $level='DEBUG' )
{
	$date = date('d/m/Y H:i:s:ms', time());
	if ( is_array( $data ) )
		$output = "<script>console.log( '[" . $level . "] " . $date . ": " . implode( ',', $data) . "' );</script>";
	else
		$output = "<script>console.log( '[" . $level . "] " . $date . ": " . $data . "' );</script>";
	//echo $output;
}

// Get a parameter from the session data string
function getParam($str, $param)
{
	$token = strtok($str, ";");
	while ($token !== false)
	{
		$token = preg_replace('/\|(.*):/', '=', $token, 1);

		if(strchr($token,"=",true) == $param)
		{
			$token2 = $token;
			$token2 = strtok($token2, "=");
			$name = $token2;
			$token2 = strtok("=");
			$val=$token2;
			return $val;
		}
		$token = strtok(";");
	}
	return null;
}

// Generate a random alphanumeric string of a given length
function generateRandomString($length = 32) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Hash the password to insert in database
function hash_pwd($data, $key = null) {
	return hash_hmac("sha256", hash("sha256", hash("sha1", md5($data, FALSE), FALSE), FALSE), $key, FALSE);
}

// Hash the user's ID
function hash_uid($data, $key = null) {
	return md5($data.$key, FALSE);
}

// Function to base64 encode a string using a key
function encrypt($string, $key) {
	$result = '';
	for($i=0; $i<strlen($string); $i++)
	{
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)+ord($keychar));
		$result.=$char;
	}

	return base64_encode($result);
}

// Function to base64 decode a string using a key
function decrypt($string, $key) {
	$result = '';
	$string = base64_decode($string);

	for($i=0; $i<strlen($string); $i++)
	{
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)-ord($keychar));
		$result.=$char;
	}

	return $result;
}

function base64_url_encode($input) {
	return strtr(base64_encode($input), '+/=', '-_~');
}

function base64_url_decode($input) {
	return base64_decode(strtr($input, '-_~', '+/='));
}

// Prepare queries
function prep_qrys($conn)
{
	pg_query($conn, "DEALLOCATE ALL");
	
	// get key
	$sql = 'SELECT hkey FROM tUser WHERE uname = $1';
	pg_prepare($conn, "get_key", $sql) or die("Can't prepare 'get_key': " . pg_last_error());
	
	// get key 2
	$sql = 'SELECT hkey FROM tUser WHERE uid = $1';
	pg_prepare($conn, "get_key2", $sql) or die("Can't prepare 'get_key2': " . pg_last_error());
	
	// get uid
	$sql='SELECT uid FROM tUser WHERE uname = $1 and pwd = $2';
	pg_prepare($conn, "get_uid", $sql) or die("Can't prepare 'get_uid': " . pg_last_error());
	
	// get username
	$sql='SELECT uname FROM tUser WHERE uid = $1';
	pg_prepare($conn, "get_username", $sql) or die("Can't prepare 'get_username': " . pg_last_error());
	
	// check username
	$sql='SELECT uid FROM tUser WHERE uname=$1';
	pg_prepare($conn, "check_username", $sql) or die("Can't prepare 'check_username': " . pg_last_error());
	
	// check email
	$sql='SELECT uid FROM tProfile WHERE email=$1';
	pg_prepare($conn, "check_email", $sql) or die("Can't prepare 'check_email': " . pg_last_error());
	
	// register user
	$sql='INSERT INTO tUser(uname, pwd, hkey) VALUES ($1, $2, $3)';
	pg_prepare($conn, "register_user", $sql) or die("Can't prepare 'register_user': " . pg_last_error());
	
	// register profile
	$sql='INSERT INTO tProfile(uid, email, fname, lname) VALUES ($1, $2, $3, $4)';
	pg_prepare($conn, "register_profile", $sql) or die("Can't prepare 'register_profile': " . pg_last_error());
	
	// register user stats
	$sql='INSERT INTO tUserStats(uid, logdate, prevlogdate, likes, dislikes, ratings, checkins, lvl, xp) VALUES ($1, now(), now(), 0, 0, 0, 0, 1, 0)';
	pg_prepare($conn, "register_stats", $sql) or die("Can't prepare 'register_stats': " . pg_last_error());
	
	// delete user
	$sql='DELETE FROM tUser WHERE uid = $1';
	pg_prepare($conn, "delete_user", $sql) or die("Can't prepare 'delete_user': " . pg_last_error());
	
	// delete profile
	$sql='DELETE FROM tProfile WHERE uid = $1';
	pg_prepare($conn, "delete_profile", $sql) or die("Can't prepare 'delete_profile': " . pg_last_error());
	
	// delete stats
	$sql='DELETE FROM tUserStats WHERE uid = $1';
	pg_prepare($conn, "delete_stats", $sql) or die("Can't prepare 'delete_stats': " . pg_last_error());
	
	// get session
	$sql='SELECT sdata, datetouched, randkey FROM tSession WHERE session_id = $1';
	pg_prepare($conn, "get_session", $sql) or die("Can't prepare 'get_session': " . pg_last_error());
	
	// update session key
	$sql='UPDATE tSession SET randkey = $1 WHERE session_id = $2;';
	pg_prepare($conn, "update_sess_key", $sql) or die("Can't prepare 'update_sess_key': " . pg_last_error());
	
	// get profile menu
	$sql='SELECT u.uname, p.fname, p.lname, p.sex, p.pic, p.email, p.privacy FROM tUser u, tProfile p WHERE u.uid = $1 AND u.uid = p.uid';
	pg_prepare($conn, "get_profile_menu", $sql) or die("Can't prepare 'get_profile_menu': " . pg_last_error());
	
	// get user stats
	$sql='SELECT us.logdate, us.prevlogdate, u.regdate, us.likes, us.dislikes, us.ratings, us.checkins, us.visits, us.favorites, us.venue, us.lvl, us.xp FROM tUserStats us, tUser u WHERE u.uid = $1 AND u.uid = us.uid';
	pg_prepare($conn, "get_user_stats", $sql) or die("Can't prepare 'get_user_stats': " . pg_last_error());
	
	// update login date
	$sql='UPDATE tUserStats SET prevlogdate = logdate, logdate = now() WHERE uid = $1';
	pg_prepare($conn, "update_login_date", $sql) or die("Can't prepare 'update_login_date': " . pg_last_error());
	
}

function prep_venue_qrys($conn)
{
	// get user venue ratings
	$sql='SELECT * FROM tRating WHERE uid = $1 AND vid = $2';
	pg_prepare($conn, "get_uv_ratings", $sql) or die("Can't prepare 'get_uv_ratings': " . pg_last_error());
	
	// get venue details
	$sql='SELECT
	v.latlong[0] as latitude,
	v.latlong[1] as longitude,
	v.vname as name,
	v.vdetails as description,
	vct.vctdetails as tag,
	vct.vctimg as picture
FROM 
	tVenue v,
	tVenueCategoryType vct
WHERE v.vid = $1 
AND	v.vctid = vct.vctid';
	pg_prepare($conn, "get_venues_details", $sql) or die("Can't prepare 'get_venues_details': " . pg_last_error());
	
	// get venue ratings
	$sql='SELECT vid, rcid, count(*), AVG(rating) FROM tRating WHERE vid = $1 GROUP BY vid, rcid ORDER BY 2 ASC';
	pg_prepare($conn, "get_venue_ratings", $sql) or die("Can't prepare 'get_venue_ratings': " . pg_last_error());
}

function prep_rating_qrys($conn)
{
	// set rating
	$sql='INSERT INTO tRating(uid, vid, rcid, rating, rdate) VALUES ($1, $2, $3, $4, now())';
	pg_prepare($conn, "set_rating", $sql) or die("Can't prepare 'set_rating': " . pg_last_error());
	
	// update rating
	$sql='UPDATE tRating SET rating = $1, rdate = now() WHERE uid = $2 AND vid = $3 AND rcid = $4';
	pg_prepare($conn, "update_rating", $sql) or die("Can't prepare 'update_rating': " . pg_last_error());
	
	// add favorite
	$sql='INSERT INTO tFav(uid, vid) VALUES ($1, $2)';
	pg_prepare($conn, "fav_add", $sql) or die("Can't prepare 'fav_add': " . pg_last_error());
	
	// remove favorite
	$sql='DELETE FROM tFav WHERE uid = $1 AND vid = $2';
	pg_prepare($conn, "fav_remove", $sql) or die("Can't prepare 'fav_remove': " . pg_last_error());
	
	// update ratings number
	$sql='UPDATE tUserStats set ratings = (SELECT COUNT(DISTINCT vid) FROM tRating WHERE uid = $1) WHERE uid = $1';
	pg_prepare($conn, "update_ratings_number", $sql) or die("Can't prepare 'update_ratings_number': " . pg_last_error());
	
	// update checkin number
	$sql='UPDATE tUserStats set checkins = (SELECT COUNT(vid) FROM tCheckin WHERE uid = $1) WHERE uid = $1';
	pg_prepare($conn, "update_checkin_number", $sql) or die("Can't prepare 'update_checkin_number': " . pg_last_error());
	//UPDATE tUserStats SET checkins = (select count(vid) cnt FROM tCheckin WHERE tUserStats.uid = tCheckin.uid)
	//FROM tcheckin
	//WHERE tUserStats.uid = tCheckin.uid;
	
	// update visits number
	$sql='UPDATE tUserStats set visits = (SELECT COUNT(DISTINCT vid) FROM tCheckin WHERE uid = $1) WHERE uid = $1';
	pg_prepare($conn, "update_visits_number", $sql) or die("Can't prepare 'update_visits_number': " . pg_last_error());
	//UPDATE tUserStats SET visits = (select count(distinct vid) cnt FROM tCheckin WHERE tUserStats.uid = tCheckin.uid)
	//FROM tcheckin
	//WHERE tUserStats.uid = tCheckin.uid;
	
	// update favorites number
	$sql='UPDATE tUserStats SET favorites = (SELECT COUNT(*) FROM tFav where uid = $1) WHERE uid = $1';
	pg_prepare($conn, "update_favorites_number", $sql) or die("Can't prepare 'update_favorites_number': " . pg_last_error());
	
	// delete ratings
	$sql='DELETE FROM tRating WHERE uid = $1 AND vid = $2';
	pg_prepare($conn, "delete_ratings", $sql) or die("Can't prepare 'delete_ratings': " . pg_last_error());
}

function prep_profile_qrys($conn)
{
	// update firstname
	$sql='UPDATE tProfile SET fname = $1 WHERE uid = $2';
	pg_prepare($conn, "update_firstname", $sql) or die("Can't prepare 'update_firstname': " . pg_last_error());
	
	// update lastname
	$sql='UPDATE tProfile SET lname = $1 WHERE uid = $2';
	pg_prepare($conn, "update_lastname", $sql) or die("Can't prepare 'update_lastname': " . pg_last_error());
	
	// update username
	$sql='UPDATE tUser SET uname = $1 WHERE uid = $2';
	pg_prepare($conn, "update_username", $sql) or die("Can't prepare 'update_username': " . pg_last_error());
	
	// update password
	$sql='UPDATE tUser SET pwd = $1 WHERE uid = $2';
	pg_prepare($conn, "update_password", $sql) or die("Can't prepare 'update_password': " . pg_last_error());
	
	// update email
	$sql='UPDATE tProfile SET email = $1 WHERE uid = $2';
	pg_prepare($conn, "update_email", $sql) or die("Can't prepare 'update_email': " . pg_last_error());
	
	// update gender
	$sql='UPDATE tProfile SET sex = $1 WHERE uid = $2';
	pg_prepare($conn, "update_gender", $sql) or die("Can't prepare 'update_gender': " . pg_last_error());
	
	// update privacy
	$sql='UPDATE tProfile SET privacy = $1 WHERE uid = $2';
	pg_prepare($conn, "update_privacy", $sql) or die("Can't prepare 'update_privacy': " . pg_last_error());
	
	// update avatar
	$sql='UPDATE tProfile SET pic = $1 WHERE uid = $2';
	pg_prepare($conn, "update_avatar", $sql) or die("Can't prepare 'update_avatar': " . pg_last_error());
}

// Deallocate prepared queries
function dealloc_qrys($conn)
{
	pg_query($conn, "DEALLOCATE get_key");
	pg_query($conn, "DEALLOCATE get_uid");
	pg_query($conn, "DEALLOCATE get_username");
	pg_query($conn, "DEALLOCATE check_username");
	pg_query($conn, "DEALLOCATE check_email");
	pg_query($conn, "DEALLOCATE register_user");
	pg_query($conn, "DEALLOCATE register_profile");
	pg_query($conn, "DEALLOCATE register_stats");
	pg_query($conn, "DEALLOCATE delete_user");
	pg_query($conn, "DEALLOCATE delete_profile");
	pg_query($conn, "DEALLOCATE delete_stats");
	pg_query($conn, "DEALLOCATE get_session");
	pg_query($conn, "DEALLOCATE update_sess_key");
	pg_query($conn, "DEALLOCATE get_profile_menu");
	pg_query($conn, "DEALLOCATE get_user_stats");
	pg_query($conn, "DEALLOCATE update_login_date");
}

// Function to check if a user is logged in
function check_login($conn, $exp_time, $cookie_name='Biscuit')
{
	// prepare statements
	prep_qrys($conn);
	
	// Get the session_id from cookie, if it exists
	if (isset($_COOKIE[$cookie_name]))
		$sessid = $_COOKIE[$cookie_name];
	else
		$sessid = '';
	// Get session data using the session_id
	$result = pg_execute($conn, "get_session", array($sessid)) or die("Can't execute get_session: " . pg_last_error());
	// If we found a session
	if (pg_num_rows($result) == 1)
	{
		// Get session data
		$row = pg_fetch_row($result);
		$session_data = $row[0];
		$exp_date = $row[1];
		$randkey = $row[2];
		pg_freeresult($result);
		
		// Check if session has expired
		$now = time();
		$expiration = $exp_date + $exp_time - $now;
		logger("check_login: expiration=_|$expiration|_");
		logger("check_login: exp_date=_|$exp_date|_");
		logger("check_login: exp_time=_|$exp_time|_");
		logger("check_login: now=_|$now|_");
		
		// If session expired
		if ($expiration < 0)
		{
			// Expire cookie
			setcookie("Biscuit", "", time() - 3600);
			// Destroy session
			session_destroy();
			// Return a "GUEST" flag, since the user is now disconnected
			return "GUEST";
		}
		
		// Session has not expired and user is active on the page. Update session time.
		pg_query("UPDATE tSession SET DateTouched = ".$now." WHERE session_id = '".$sessid."';");
		
		// Get user ID and log status from session data
		$uid = decrypt(getParam($session_data, 'uid'), $randkey);
		$logged = getParam($session_data, 'LOGGED');
		
		// User is logged, so return the user's ID
		if ($logged)
		{
			return $uid;
		}
		// User is not logged in, so expire cookie, destroy session and return "GUEST" flag
		else
		{
			setcookie("Biscuit", "", time() - 3600);
			session_destroy();
			return "GUEST";
		}
	}
	// We did not find a session
	else
	{
		// Expire cookie
		setcookie("Biscuit", "", time() - 3600);
		// Destroy session
		session_destroy();
		// Return null user ID
		return null;
	}
}

// Show a pop up message for login
function login_msg($uid)
{
	if ($uid == "GUEST")
	{
		echo "<script type='text/javascript'>
		jNotify('Welcome guest! Please log in!');
		</script>";
	}
	else if ($uid == null)
	{
		echo "<script type='text/javascript'>
		jNotify('Session has expired! Please log in!');
		</script>";
	}
	/*
	else
	{
		echo "<script type='text/javascript'>
		jSuccess('uid = $uid');
		</script>";
	}
	*/
}

function user_session($time, $cookie)
{
	require_once (realpath($_SERVER["DOCUMENT_ROOT"])."/venues/session.php");
	
	// prepare statements
	prep_qrys($conn);

	$uid = check_login($conn, 600, $cookie);
	
	return $uid;
}
?>