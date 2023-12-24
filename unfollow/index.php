<?php

	session_start();

	require("../resources/PHP/db.class.php");

	$location = '../../../../timeline/';

	$db = new sql();

	// login check
	if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != '') {
		$uid = $db->intcast($_SESSION['uid']);
		} else {
		header("Location: " . $location);
		exit;
	}

	if(isset($_SESSION['token']) != str_replace('/','',$_REQUEST['csrf'])) {
		header("Location: " . $location);
		exit;
		} else {
		$uid = $db->intcast($_SESSION['uid']);
	}

	if(isset($_REQUEST['profileid']) && $_REQUEST['profileid'] != '' && isset($_REQUEST['friendid']) && $_REQUEST['friendid'] != '') { 

		$profileid = $db->intcast($_REQUEST['profileid']);
		$friendid  = $db->intcast($_REQUEST['friendid']);
		$created = time();

		$testdelete = $db->query("SELECT * FROM friends where uid = '".$profileid."' and fid = '".$friendid."' LIMIT 1");
		
		if(count($testdelete) >=1) { 
			$deletevalue = $db->intcast($postid);
			$deletekey 	 = $db->intcast($uid);
			$delete = $mysqli->prepare("DELETE FROM friends WHERE uid = ? AND fid = ? LIMIT 1");
			$delete->bind_param("ii", $profileid, $friendid);
			$delete->execute();
			$db->close();
		}
		
	} else {
		header("Location: " . $location);
		exit;
	}

	// reset token and destroy session, to prevent replays.
	$_SESSION['token'] = '';

	header("Location: " . $location);
	exit;


?>
If your browser does not redirect to your timeline, <a href=" ../../../../timeline/">click here</a>