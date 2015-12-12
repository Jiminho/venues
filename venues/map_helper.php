<?php
	$root = "/venues";
	require_once (realpath($_SERVER["DOCUMENT_ROOT"])."/venues/session.php");
	$uid = check_login($conn, 600, 'Biscuit');
	
	if ($uid == 'GUEST' || $uid == null) $uid = 0;
	
	if (is_ajax()) {
		if ((isset($_POST["lat"]) && !empty($_POST["lat"])) && (isset($_POST["lng"]) && !empty($_POST["lng"])))
		{
			$lat = $_POST["lat"];
			$lng = $_POST["lng"];
			//echo '<script>alert("lat: '.$lat.', lng: '.$lng.'")</script>';
			//echo '<script>console.log("lat: '.$lat.', lng: '.$lng.'")</script>';
		}
		else
		{
			//echo '<script>alert("ERRRRRRRORRRRRRR")</script>';
			//echo '<script>console.log("ERRRRRRRORRRRRRR")</script>';
			exit();
		}
		
		if (isset($_POST["action"]) && !empty($_POST["action"])) 
		{ //Checks if action value exists
			$action = $_POST["action"];
			switch($action)
			{ //Switch case for value of action
				case "points": get_area_points($conn, $uid, $lat, $lng, TRUE); break;
			}
		}
	}
	else
	{
		//echo '<script>alert("NOT AJAX")</script>';
		//echo '<script>console.log("NOT AJAX")</script>';
		exit();
	}

	//Function to check if the request is an AJAX request
	function is_ajax()
	{
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}
	
	
	function get_area_points($dbconn, $uid, $lat, $long, $show_json=FALSE)
	{
		// Performing SQL query
		if ($uid == 'GUEST')
		{
			$uid = 0;
		}
		$query = 'SELECT array_to_json(array_agg(row_to_json(t))) as tvenue_json
		FROM (
			SELECT
				id, 
				name, 
				latitude, 
				longitude, 
				rating, 
				distance
			FROM (
				SELECT 
					v.vid as id, 
					v.vname as name, 
					v.latlong[0] as latitude, 
					v.latlong[1] as longitude,
					avg(r.rating) as rating,
					( 6371 * acos( cos( radians('.$lat.') ) * cos( radians( latlong[0] ) ) * cos( radians( latlong[1] ) - radians('.$long.') ) + sin( radians('.$lat.') ) * sin( radians( latlong[0] ) ) ) ) distance 
				FROM 
					tVenue v,
					tRating r
				WHERE
					v.vid = r.vid
				GROUP BY id, name, latitude, longitude
			) as dt
			WHERE distance < 5.0
			ORDER BY name, rating ASC
		) t';
		
		$result = pg_query($query) or die('Query failed: ' . pg_last_error());
		
		// Printing results in HTML
		$json = pg_fetch_array($result, null, PGSQL_ASSOC);
		if($show_json)
		{
			//echo '<script>console.log('.json_encode(array_values($json)).')</script>';
			echo json_encode(array_values($json));
		}
		else
		{
			$str = implode("*",$json);
			$out = htmlspecialchars($str);
			echo $out;
		}
		// Free resultset
		pg_free_result($result);
	}
?>