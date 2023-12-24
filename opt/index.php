<?php

session_start();

require("../resources/PHP/db.class.php");

$db = new sql();

$profileid 		= $db->intcast($_REQUEST['profileid']);

if($profileid == $_SESSION['uid']) {
	header("Location: ../../../../../");
	exit;
}


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
		
		if($method == 'heart') {
			$test = $db->query("SELECT * FROM stats WHERE uid = '".$db->intcast($profileid)."' AND pid = '".$db->intcast($postid)."' and likes >=1");
			
			if(count($test) <= 0) { 
				$stmt 		= $mysqli->prepare("INSERT INTO stats (uid, pid, likes) VALUES (?, ?, ?)");
				$stmt_up 	= $mysqli->prepare("UPDATE stats SET uid = ?, pid =?, likes=likes+1 WHERE id = ?");
			}
		} elseif($method == 'star') {
			$test = $db->query("SELECT * FROM stats WHERE uid = '".$db->intcast($profileid)."' AND pid = '".$db->intcast($postid)."' and starred >=1");
			if(count($test) <= 0) { 			
				$stmt 	    = $mysqli->prepare("INSERT INTO stats (uid, pid, starred) VALUES (?, ?, ?)");
				$stmt_up 	= $mysqli->prepare("UPDATE stats SET uid = ?, pid =?, starred=starred+1 WHERE id = ?");
			}
		} elseif($method == 'share') { 
			$test = $db->query("SELECT * FROM stats WHERE uid = '".$db->intcast($profileid)."' AND pid = '".$db->intcast($postid)."' and shares >=1");
			if(count($test) <= 0) { 
				$stmt 		= $mysqli->prepare("INSERT INTO stats (uid, pid, shares) VALUES (?, ?, ?)");
				$stmt_up 	= $mysqli->prepare("UPDATE stats SET uid = ?, pid =?, shares=shares+1 WHERE id = ?");
			}
		} else { }


		
		if(isset($stmt)) { 
		
			$checkstat = $db->query("SELECT * FROM stats where uid = '".$db->intcast($profileid)."' and pid = '".$db->intcast($postid)."'");

			if(count($checkstat) == 0) {
				// insert
				$stmt->bind_param("iii", $profileid, $postid, $insertvalue);
				$stmt->execute();
				$db->close();
			} else {
				// update
				$rid = $db->intcast($checkstat[0]['id']);
				$stmt_up->bind_param("iii", $profileid, $postid, $rid);
				$stmt_up->execute();	
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
	//session_regenerate_id();
	header("Location: ../../../../../timeline/");
	exit;
}


?>
If your browser does not redirect to your timeline, <a href="../../../../../timeline/">click here</a>