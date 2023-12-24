<?php

session_start();

require("../resources/PHP/db.class.php");

$db = new sql();

if($_REQUEST['csrf']) {
		
	// login check
	if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != '') {
		$uid = $db->intcast($_SESSION['uid']);
		} else {
		header("Location: ../../../../../");
		exit;
	}

	if(isset($_SESSION['token']) != str_replace('/','',$_REQUEST['csrf'])) {
		header("Location: ../../../../../");
		exit;
		} else {
		$uid = $db->intcast($_SESSION['uid']);
	}

	if(isset($_REQUEST['profileid']) && $_REQUEST['profileid'] != '' && isset($_REQUEST['postid']) && $_REQUEST['postid'] != '' && $_REQUEST['method'] != '') { 
	
		$postid  = $db->intcast($_REQUEST['postid']);
		$testdelete = $db->query("SELECT * FROM timeline WHERE uid = '".$db->intcast($uid)."' AND tid = '".$db->intcast($postid)."' LIMIT 1");
		
		if(count($testdelete) >=1) { 
			$deletevalue = $db->intcast($postid);
			$deletekey 	 = $db->intcast($uid);
			$delete = $mysqli->prepare("DELETE FROM timeline WHERE uid = ? AND tid = ?  LIMIT 1");
			$delete->bind_param("ii", $deletekey, $deletevalue);
			$delete->execute();
			$db->close();
		}
	}

} else {
	
	header("Location: ../../../../../timeline/");
	exit;	
}

	// reset token and destroy session, to prevent replays.
	$_SESSION['csrf']  = '';
	$_SESSION['token'] = '';
	 header("Location: ../../../../../timeline/");
	exit;

?>
If your browser does not redirect to your timeline, <a href="../../../../../timeline/">click here</a>