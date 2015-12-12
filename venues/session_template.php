 <?php
    function sess_open($sess_path, $sess_name) {
        print "Session opened.<br />";
        print "Sess_path: $sess_path<br />";
        print "Sess_name: $sess_name<br /><br />";
        return true;
    }

    function sess_close() {
        print "Session closed.<br />";
        return true;
    }

    function sess_read($sess_id) {
        print "Session read.<br />";
        print "Sess_ID: $sess_id<br />";
        return '';
    }

    function sess_write($sess_id, $data) {
        print "Session value written.<br />";
        print "Sess_ID: $sess_id<br />";
        print "Data: $data<br /><br />";
        return true;
    }

    function sess_destroy($sess_id) {
        print "Session destroy called.<br />";
        return true;
    }

    function sess_gc($sess_maxlifetime) {
        print "Session garbage collection called.<br />";
        print "Sess_maxlifetime: $sess_maxlifetime<br />";
        return true;
    }

    session_set_save_handler("sess_open", "sess_close", "sess_read", "sess_write", "sess_destroy", "sess_gc");
    session_start();

    print "uid: {" . $_SESSION['uid'] . "}";
?> 