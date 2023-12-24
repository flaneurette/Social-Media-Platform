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

		$profileid 		= $db->intcast($_REQUEST['profileid']);
		$postid  		= $db->intcast($_REQUEST['postid']);
		$method    		= $db->clean($_REQUEST['method'],'encode');
		$insertvalue 	= 1;

		// fetch record to insert as a share.
		$timeline 	= $db->query("SELECT * FROM timeline WHERE tid = ".$db->intcast($postid)." LIMIT 1");
		
		$message 	= $timeline[0]['post'];
		$mixedmedia = $timeline[0]['mixedmedia'];
		$sharedfrom = $db->intcast($timeline[0]['uid']);
		
		$profile_friend = $db->query("SELECT username FROM profile WHERE id = ".$db->intcast($sharedfrom)." LIMIT 1");
		
		$sharedname = $profile_friend[0]['username'];
		
		$created = time();
		
		$stmt = $mysqli->prepare("INSERT INTO timeline (uid, created, post, sharedfrom, sharedname, mixedmedia) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("iisiss", $uid, $created, $message, $sharedfrom, $sharedname, $mixedmedia);
		$stmt->execute();
		
		$test = $db->query("SELECT * FROM stats WHERE uid = '".$db->intcast($sharedfrom)."' AND pid = '".$db->intcast($postid)."' and shares >=1");
		if(count($test) <= 0) { 
			$stmt = $mysqli->prepare("INSERT INTO stats (uid, pid, shares) VALUES (?, ?, ?)");
			$stmt->bind_param("iii", $profileid, $postid, $insertvalue);
			$stmt->execute();
			
		} else {
			$rid = $db->intcast($test[0]['id']);
			$stmt_up = $mysqli->prepare("UPDATE stats SET uid = ?, pid =?, shares=shares+1 WHERE id = ?");
			$stmt_up->bind_param("iii", $profileid, $postid, $rid);
			$stmt_up->execute();
		}
				
		$db->close();
		$_SESSION['token'] = '';
	}

	header("Location: ../../../../../timeline/");
	exit;
}


?>
If your browser does not redirect to your timeline, <a href="../timeline/">click here</a>