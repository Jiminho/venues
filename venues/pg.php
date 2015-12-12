<?php
function get_points($table_name, $show_json=FALSE)
{
	// Connecting, selecting database
	$dbconn = pg_connect("host=localhost dbname=steki_db user=postgres password=password")
		or die('Could not connect: ' . pg_last_error());

	// Performing SQL query
	$query = 'SELECT array_to_json(array_agg(row_to_json(t))) as '.$table_name.'_json
    FROM (
      SELECT id, latlong[0] as latitude, latlong[1] as longitude, brand, rating, votes, nam as name, descr as description FROM "'.$table_name.'"
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
	pg_close($dbconn);

	//return $json;
}
?>