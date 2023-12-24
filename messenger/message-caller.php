<?php

	session_start();

	require("../resources/PHP/db.class.php");

	$db = new sql();
	
	$postid = $db->intcast(str_replace(',','',$_POST['toid']));
	$csrf 	= $db->clean(str_replace(',','',$_POST['csrf']),'encode');
	$method = $db->clean(str_replace(',','',$_POST['method']),'encode');
	$uid 	= $db->intcast($_SESSION['uid']);
	$post = $db->clean($_POST['postmessage'],'encode');
	$postmessage = substr($post,0,(strlen($post)-1));
	
	if(!isset($_SESSION['loggedin'])) {
		echo 'false:1';
		exit;
		} else {
		$uid = $db->intcast($_SESSION['uid']);
	}

	if(!isset($postid)) {
		echo 'false:2';
		exit;	
	}
	
	if($_SESSION['token'] != $db->clean($csrf,'encode')) {	
		echo 'false:3';
		exit;
	}
	
	if($method == 'post') {
		
		$insert = true;
		
		if(!isset($uid)) {
			$insert = false;
		}

		if(!isset($postid) || strlen($postid) > 11) {
			$insert = false;
		}

		if(!isset($postmessage) || strlen($postmessage) <= 1) {
			$insert = false;
		}		
		
		if($insert != false) { 
			$stmt = $mysqli->prepare("INSERT into messenger SET uid = ?, toid = ?, message = ?");
			$userid 	= $db->intcast($uid);
			$toid 		= $db->intcast($postid);
			$message 	= $db->clean($postmessage,'encode');
			$stmt->bind_param("iis", $userid, $toid, $message);
			$stmt->execute();
		}
		
		if($insert == true) {

			$messages = [];		
			$stmt = $mysqli->prepare("SELECT * FROM messenger where uid = ? AND toid = ? ORDER by id DESC LIMIT 1");
			$stmt->bind_param("ii", $uid,$toid);
			$stmt->execute();
			$query = $stmt->get_result();

			while($row = $query->fetch_array(MYSQLI_ASSOC)) {
				$messages[] = $row;
			}
			
			$stmt->close();
			
			$json = json_encode($messages);
			
			if($json) { 
				echo $json;
			}
		}
	}
	
?>