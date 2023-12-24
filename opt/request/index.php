<?php

session_start();

require("../../resources/PHP/db.class.php");

$db = new sql();

$profileid 		= $db->intcast($_REQUEST['profileid']);

if($profileid == $_SESSION['uid']) {
	echo 'false:1';
	exit;
}


if($_REQUEST['csrf']) {
		
	// login check
	if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != '') {
		$uid = $db->intcast($_SESSION['uid']);
		} else {
		echo 'false:2';
		exit;
	}

	if(isset($_SESSION['token']) != str_replace('/','',$_REQUEST['csrf'])) {
		echo 'false:3';
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
			$stmt = $mysqli->prepare("INSERT INTO stats (oid, uid, pid, likes) VALUES (?, ?, ?, ?)");
			} elseif($method == 'star') {
			$stmt = $mysqli->prepare("INSERT INTO stats (oid, uid, pid, starred) VALUES (?, ?, ?, ?)");
			} elseif($method == 'share') { 
			$stmt = $mysqli->prepare("INSERT INTO stats (oid, uid, pid, shares) VALUES (?, ?, ?, ?)");
		} else { }

		$checkstat = $db->query("SELECT * FROM stats where oid = '".$db->intcast($uid)."' AND uid = '".$db->intcast($profileid)."' and pid = '".$db->intcast($postid)."' LIMIT 1");

		if(isset($stmt)) { 
		
			if(count($checkstat) == 0) {
				// insert
				$stmt->bind_param("iiii", $uid, $profileid, $postid, $insertvalue);
				$stmt->execute();
			} 
		} 
		
	} else {
		echo 'false:4';
		exit;
	}
	

	if($method == 'hide' && isset($_REQUEST['profileid']) && $_REQUEST['profileid'] != '' && isset($_REQUEST['postid']) && $_REQUEST['postid'] != '') {
		$postid  		= $db->intcast($_REQUEST['postid']);
		$uid 			= $db->intcast($_SESSION['uid']);
		$insertvalue 	= 1;
		$stmt = $mysqli->prepare("INSERT INTO flagged (flaggedby, postid, hide) VALUES (?, ?, ?)");
		$stmt->bind_param("iii", $uid, $postid, $insertvalue);
		$stmt->execute();
	}

	if($method == 'hidechat' && isset($_REQUEST['profileid']) && $_REQUEST['profileid'] != '' && isset($_REQUEST['postid']) && $_REQUEST['postid'] != '') {
		$postid  		= $db->intcast($_REQUEST['postid']);
		$uid 			= $db->intcast($_SESSION['uid']);
		$insertvalue 	= 1;
		$stmt = $mysqli->prepare("INSERT INTO flagged (flaggedby, chatid, hide) VALUES (?, ?, ?)");
		$stmt->bind_param("iii", $uid, $postid, $insertvalue);
		$stmt->execute();
	}
	
	if($method == 'block' && isset($_REQUEST['profileid']) && $_REQUEST['profileid'] != '' && isset($_REQUEST['postid']) && $_REQUEST['postid'] != '') {
		$uid 			= $db->intcast($_SESSION['uid']);
		$profileid 		= $db->intcast($_REQUEST['profileid']);
		$insertvalue 	= 1;
		$stmt = $mysqli->prepare("UPDATE friends SET blk = ? WHERE uid = ? AND fid = ?");
		$stmt->bind_param("iii", $insertvalue, $uid, $profileid);
		$stmt->execute();
	}
	
	if($method == 'heart') {
		$numbersopt = $db->query("SELECT COUNT(likes) FROM stats where uid = '".$db->intcast($profileid)."' and pid = '".$db->intcast($postid)."'");
		$number = $numbersopt[0]["COUNT(likes)"];
	}
	
	if($method == 'star') {
		$numbersopt = $db->query("SELECT COUNT(starred) FROM stats where uid = '".$db->intcast($profileid)."' and pid = '".$db->intcast($postid)."'");
		$number = $numbersopt[0]["COUNT(starred)"];
	}
	
	// reset token and destroy session, to prevent replays.
	$_SESSION['csrf']  = '';
	$_SESSION['token'] = '';
	echo 'true:1:'.$db->clean($method,'encode').':'.$db->intcast($number);
	$db->close();
	exit;
}

?>