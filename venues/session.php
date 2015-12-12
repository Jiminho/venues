<?php
	// Get helper functions
	require_once (realpath($_SERVER["DOCUMENT_ROOT"])."/venues/helper.php");
	
	// Connect to database
	$conn_string = "host=localhost port=5432 dbname=venues_db user=postgres password=password";
	$conn = pg_pconnect($conn_string) or die('Could not connect to database: ' . pg_last_error());
	logger( "Connected ($conn)." );
	
	// Set session cookie name
	session_name('Biscuit');
	
	// Open session
	function sess_open($sess_path, $sess_name) {
		logger( "Session opened." );
        //logger( "Sess_path: $sess_path" );
        logger( "Sess_name: $sess_name" );
		return true;
	}
	
	// Close session
	function sess_close() {
		logger( "Session closed." );
		return true;
	}
	
	// Get session
	function sess_read($sess_id) {
		logger( "Session read." );
        logger( "Sess_ID: $sess_id" );
		
		$result = pg_query("SELECT sdata FROM tSession WHERE session_id = '$sess_id';") or die("Session read error ($result).<br />");
		logger( "result ($result)." );
		
		$CurrentTime = time();
		if (!pg_num_rows($result)) {
			pg_query("INSERT INTO tSession (session_id, DateTouched) VALUES ('$sess_id', $CurrentTime);");
			return '';
		} else {
			extract(pg_fetch_array($result), EXTR_PREFIX_ALL, 'sess');
			//pg_query("UPDATE tSession SET DateTouched = $CurrentTime WHERE session_id = '$sess_id';");
			return $sess_sdata;
		}
	}
	
	// Set session
	function sess_write($sess_id, $data) {
		logger( "Session value written." );
        logger( "Sess_ID: $sess_id" );
        logger( "Data: $data" );
		
		$CurrentTime = time();
		pg_query("UPDATE tSession SET sdata = '$data', DateTouched = $CurrentTime WHERE session_id = '$sess_id';");
		return true;
	}
	
	// Destroy session
	function sess_destroy($sess_id) {
		logger( "Session destroy called." );
		pg_query("DELETE FROM tSession WHERE session_id = '$sess_id';");
		return true;
	}
	
	// Session garbage collector
	function sess_gc($sess_maxlifetime) {
		logger( "Session garbage collection called." );
        logger( "Sess_maxlifetime: $sess_maxlifetime" );
		$CurrentTime = time();
		pg_query("DELETE FROM tSession WHERE DateTouched + $sess_maxlifetime < $CurrentTime;");
		return true;
	}
	
	// Session handler
	session_set_save_handler("sess_open", "sess_close", "sess_read", "sess_write", "sess_destroy", "sess_gc");
	
	// Start Session
	session_start();
?> 