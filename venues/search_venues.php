<?php
function get_points($dbconn, $uid, $lat, $long, $show_json=FALSE)
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

function get_content($uid, $vid)
{
	// Performing SQL query
	if ($uid == 'GUEST')
	{
		$uid = 0;
	}
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
		GROUP BY id, latitude, longitude, brand, name, description
    ) t';
	
	$result = pg_query($query) or die('Query failed: ' . pg_last_error());
	
	// Printing results in HTML
	$json = pg_fetch_array($result, null, PGSQL_ASSOC);
	
	echo json_encode(array_values($json));
	
	// Free resultset
	pg_free_result($result);
}

if (isset($_POST['action']) && !empty($_POST['action']) 
	&& $_POST['uid'] && !empty($_POST['uid'])
	&& $_POST['vid'] && !empty($_POST['vid']))
{
    $action = $_POST['action'];
    
	if ($action == 'content')
	{
		get_content($uid, $vid);
    }
}
?>