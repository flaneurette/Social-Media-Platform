<?php

	session_start();

	require("../resources/PHP/db.class.php");

	$db = new sql();
	
	if(!isset($_POST['csrf'])) {
		echo 'false:1';
		exit;	
	}

	if(!isset($_POST['method'])) {
		echo 'false:2';
		exit;
	}
	
	$csrf 	= $db->clean(str_replace(',','',$_POST['csrf']),'encode');
	$method = $db->clean(str_replace(',','',$_POST['method']),'encode');
	$toid 	= $db->clean(str_replace(',','',$_POST['toid']),'encode');
	
	if($_SESSION['token'] != $db->clean($csrf,'encode')) {	
		echo 'false:3';
		exit;
	}

	if(!isset($_SESSION['uid'])) {
		echo 'false:4';
		exit;
		} else {
		$uid = $db->intcast($_SESSION['uid']);
	}

	if(!isset($toid)) {
		echo 'false:4';
		exit;
		} else {
		$toid = $db->intcast($toid);
	}
	
	if(!isset($_SESSION['loggedin'])) {
		echo 'false:5';
		exit;
	} 
	
	if($method == 'fetch') { 
	
		$messages = [];		
				
		$stmt = $mysqli->prepare("SELECT * FROM messenger where toid = ? AND uid = ? ORDER BY id DESC LIMIT 1");
		$stmt->bind_param("ii", $uid, $toid);
		$stmt->execute();
		$query = $stmt->get_result();

		while($row = $query->fetch_array(MYSQLI_ASSOC)) {
			$messages[] = $row;
		}
		
		$stmt->close();
		
		if(count($messages) >=1) { 

			$json = json_encode($messages);
				
			if($json) { 
				echo $json;
			}
		}
	}
?>