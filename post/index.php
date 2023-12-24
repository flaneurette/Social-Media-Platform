<?php

session_start();

// set a timeout of 20 minutes for uploading files.
set_time_limit(1200);

require("../resources/PHP/db.class.php");

$db = new sql();

$location = '../timeline/';

if($_REQUEST['csrf']) {
		
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

	if(isset($_POST['post-message']) && $_POST['post-message'] != '') { 

		$message = $_POST["post-message"];
	
		$message = strip_tags($message,'<br><b><em><i><strong><code><blockquote>');
		
		if(strlen($message) <= 2) {
			header("Location: " . $location);
			exit;
		}	
		
		if(strlen($message) >= 1900) {
			header("Location: " . $location);
			exit;
		}
		
		if(strlen($message) > 1900) {
			$message = substr($message,0,1900);
		}
		
		$message = $db->clean($message,'encode');
		$message = utf8_encode($message);
		
		$searchtags = ['&lt;br&gt;','&lt;br /&gt;','&lt;em&gt;','&lt;/em&gt;','&lt;i&gt;','&lt;/i&gt;','&lt;b&gt;','&lt;/b&gt;','&lt;strong&gt;','&lt;/strong&gt;','&lt;code&gt;','&lt;/code&gt;','&lt;blockquote&gt;','&lt;/blockquote&gt;'];
		$replacetags = ['<br>','<br>','<em>','</em>','<i>','</i>','<b>','</b>','<b>','</b>','<code>','</code>','<blockquote>','</blockquote>'];
		$message = str_ireplace($searchtags,$replacetags,$message);
		
			$mixedmedia = '';
			
			// mixedmedia
			if($_FILES['mixedmedia']['tmp_name'][0] !='') {
					
					if($_FILES['mixedmedia']['error'][0] != 1) {
						
						$destination = '../media/';
						
						$seed  = time().mt_rand(0,0xffffff).mt_rand(0,0xffffff).mt_rand(0,0xffffff).mt_rand(0,0xffffff);
						$seed .= time().mt_rand(0,0xffffff).mt_rand(0,0xffffff).mt_rand(0,0xffffff).mt_rand(0,0xffffff);
						$seed .= microtime();
						$seed .= '-media';
						
						$mm = substr(strtolower($_FILES['mixedmedia']['name'][0]),strlen($_FILES['mixedmedia']['name'][0])-4,4);

						switch($mm) {
							case '.jpg':
							$mixedmedia = '.jpg';
							break;
							case 'jpeg':
							$mixedmedia = '.jpeg';
							break;
							case 'jfif':
							$mixedmedia = '.jfif';
							break;
							case '.pjp':
							$mixedmedia = '.pjp';
							break;
							case '.png':
							$mixedmedia = '.png';
							break;
							case '.gif':
							$mixedmedia = '.gif';
							break;
							case '.mp3':
							$mixedmedia = '.mp3';
							break;
							case '.ogg':
							$mixedmedia = '.ogg';
							break;
							case 'opus':
							$mixedmedia = '.opus';
							break;
							case '.oga':
							$mixedmedia = '.oga';
							break;
							default:
							$mixedmedia = false;
							break;
						}
						
						if($mixedmedia != false) { 
						
							@chmod($destination,0777);
							$upload_mixedmedia = move_uploaded_file($_FILES['mixedmedia']['tmp_name'][0], strtolower($destination).$seed.$mixedmedia);
							if($upload_mixedmedia) { 
								$mixedmedia = $db->clean(strtolower($destination).$seed.$mixedmedia,'dir');
								} else {
								$submit = false;
								$reason = "Mixedmedia failed to upload!";
							}
							
						} else { 
								$submit = false;
								$reason = "This type of file is not allowed, please choose a gif, jpg, png, ogg or mp3!";	
						}
						
					} else {
						$submit = false;
						$reason = "File cannot be empty";
					}
					
				} else {
				// $reason = "Error: This is not a link to a photo.";
			}
		
		$created = time();
		
		
		$stmt = $mysqli->prepare("INSERT INTO timeline (uid, created, post, mixedmedia) VALUES (?, ?, ?, ?)");
		$stmt->bind_param("iiss", $uid, $created, $message, $mixedmedia);
		$stmt->execute();
		$db->close();
		// reset the session token.
		$_SESSION['token'] = '';
	}

	header("Location: " . $location);
	exit;
}

header("Location: " . $location);
exit;
?>
If your browser does not redirect to your timeline, <a href="../timeline/">click here</a>