<?php

	session_start();

	require("../resources/PHP/db.class.php");

	$db = new sql();

	$location = '../timeline/';

	if($_REQUEST['csrf']) {
		
		// login check
		if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != '') {
			$uid = $db->intcast($_SESSION['uid']);
			} else {
			header("Location: ". $location);
			exit;
		}

		if(isset($_SESSION['token']) != str_replace('/','',$_REQUEST['csrf'])) {
			header("Location: ". $location);
			exit;
			} else {
			$uid = $db->intcast($_SESSION['uid']);
		}

		if(isset($_POST['comment-id']) && $_POST['comment-id'] != '' && $_POST['uid-id'] !='' && $_POST['post-message'] !='' && $_POST['at-user'] !='') { 


			if(strlen($_POST['comment-id']) >= 16) {
				header("Location: ../");
				exit;
			}	

			if(strlen($_POST['uid-id']) >= 16) {
				header("Location: ../");
				exit;
			}	
			
			// initialize variables
			$cid 	 = $db->intcast($_POST['comment-id']);
			$toid 	 = $db->intcast($_POST['to-id']);
			$atuser  = $db->clean($_POST["at-user"],'encode');
			$message = $_POST['post-message'];
			
			if(!is_int($cid)) {
				header("Location: ". $location);
				exit;
			}

			if(!is_int($toid)) {
				header("Location: ". $location);
				exit;
			}
			
			if($message == 'Post...') {
				header("Location: ". $location);
				exit;
			}
			
			if($message == 'Reply...') {
				header("Location: ". $location);
				exit;
			}
			
			if(strlen($message) <= 2) {
				header("Location: ../");
				exit;
			}	
			
			if(strlen($message) >= 510) {
				header("Location: ". $location);
				exit;
			}
			
			if(!stristr($atuser,'@')) {
				header("Location: ". $location);
				exit;
			}
			
			if(strlen($atuser) >= 25) {
				header("Location: ". $location);
				exit;
			}

			if(strlen($message) > 255) {
				$message = substr($message,0,255);
			}
			
			$message = $atuser . ' '. $db->clean($message,'encode');
			
			$message = utf8_encode($db->clean($message,'encode'));
			
			$created = time();
			
			$stmt = $mysqli->prepare("INSERT INTO timeline (uid, created, cid, post, toid) VALUES (?, ?, ?, ?, ?)");
			$stmt->bind_param("iiisi", $uid, $created, $cid, $message, $toid);
			$stmt->execute();
			$db->close();
			// reset the session token.
			$_SESSION['token'] = '';
		}

		header("Location: ". $location);
		exit;
	}

	header("Location: ". $location);
	exit;
?>
If your browser does not redirect to your timeline, <a href="../timeline/">click here</a>