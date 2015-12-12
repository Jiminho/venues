<?php
	$root = "/venues";
	require_once (realpath($_SERVER["DOCUMENT_ROOT"])."/venues/session.php");
	$uid = check_login($conn, 600, 'Biscuit');
	
	if ($uid == 'GUEST' || $uid == null) $uid = 0;

	if (isset($_POST['uid']) && !empty($_POST['uid'])
		&& isset($_POST['vid']) && !empty($_POST['vid']))
	{
		if ($uid != $_POST['uid']) $uid = 0;
		$vid = $_POST['vid'];
	}
	else
	{
		$uid = 0;
		$vid = 0;
		header('Location: ' . $root . '/index.php');
	}
	//$uid = 0;
	//$vid = 333;
	//echo "<script>console.log( 'uid: " . $uid . "<br />vid: " . $vid . "<br />' );</script>";
	//echo "<script>alert( 'uid: " . $uid . "<br />vid: " . $vid . "<br />' );</script>";
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
?>