<?php
function get_points($dbconn, $uid, $show_json=FALSE)
{
	// Connecting, selecting database
	//$dbconn = pg_connect("host=localhost dbname=steki_db user=postgres password=password")
	//	or die('Could not connect: ' . pg_last_error());
	
	// Performing SQL query
	if ($uid == 'GUEST')
	{
		$uid = 0;
	}
	$query = 'SELECT array_to_json(array_agg(row_to_json(t))) as tvenue_json
    FROM (
		SELECT 
			v.vid as id, 
			v.latlong[0] as latitude, 
			v.latlong[1] as longitude, 
			v.vname as name,
			avg(r.rating) as rating 
		FROM 
			tVenue v,
			tVenueCategoryType vct,
			tRating r
		WHERE
			v.vctid = vct.vctid
		AND	v.vid = r.vid
		GROUP BY id, latitude, longitude, name
    ) t';
	//$query = 'SELECT row_to_json("'.$table_name.'") as '.$table_name.'_json FROM "'.$table_name.'"';
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

	// Closing connection
	//pg_close($dbconn);

	//return $json;
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