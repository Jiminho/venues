<?php
	$root = "/venues";
	require_once (realpath($_SERVER["DOCUMENT_ROOT"])."/venues/session.php");
	$uid = check_login($conn, 600, 'Biscuit');
	
	if ($uid == 'GUEST' || $uid == null) $uid = 0;
	
	if (is_ajax()) {
		if (isset($_POST["action"]) && !empty($_POST["action"])) 
		{ //Checks if action value exists
			$action = $_POST["action"];
			switch($action)
			{ //Switch case for value of action
				case "content": get_content(); break;
			}
		}
	}

	//Function to check if the request is an AJAX request
	function is_ajax()
	{
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}
	
	function get_content()
	{
		$return = $_POST;

		if ($return["uid"] == "") $uid = 0;
		else $uid = $return["uid"];
		
		if ($return["vid"] == "") $vid = 0;
		else $vid = $return["vid"];
		
		// Performing SQL query
		$query = 'SELECT array_to_json(array_agg(row_to_json(t))) as content_json
		FROM (
			SELECT 
				v.vid as id, 
				avg(r.rating) as rating,
				AVG(CASE 
					WHEN uid = ' . $uid . ' 
					THEN r.rating
					ELSE NULL
					END
					) AS user_rating,
				count(r.vid) / 4 as votes,
				v.vname as name, 
				v.vdetails as description 
			FROM 
				tVenue v,
				tVenueCategoryType vct,
				tRating r
			WHERE
				v.vid = ' . $vid . '
			AND	v.vid = r.vid
			AND v.vctid = vct.vctid
			GROUP BY id, name, description
		) t';
		
		$result = pg_query($query) or die('Query failed: ' . pg_last_error());
		
		// Printing results in HTML
		$json = pg_fetch_array($result, null, PGSQL_ASSOC);
		
		echo json_encode(array_values($json));
		
		// Free resultset
		pg_free_result($result);
		exit();
	}
	
	//$uid = 0;
	//$vid = 333;
	//echo "<script>console.log( 'uid: " . $uid . "<br />vid: " . $vid . "<br />' );</script>";
	//echo "<script>alert( 'uid: " . $uid . "<br />vid: " . $vid . "<br />' );</script>";
	
?>