<?php

	session_start();

	require("../resources/PHP/db.class.php");
	require("../resources/PHP/social.class.php");

	$db 	= new sql();
	$social = new social();
	$host 	= 'https://www.twigpage.com/';
	$toid 	= '';

	// login check
	if(isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != '') {
		$uid = $db->intcast($_SESSION['uid']);
		} else {
		header("Location: ../");
		exit;
	}

	// get and set a proper token for our instance.
	if(!isset($_SESSION['token']) || empty($_SESSION['token']) ) {
		$csrf = $db->getToken();
		$_SESSION['token'] = $csrf;
	}  else {
		$csrf = $db->clean($_SESSION['token'],'encode');
	}

	$friend_list = '';

	$selectfriends = $db->query("SELECT * FROM friends WHERE uid = '".$db->intcast($uid)."' ORDER BY RAND() LIMIT 5");
	$countfriends = count($selectfriends);
	
	if($countfriends  >=1) {
		
		for($j=0; $j<$countfriends; $j++) {
			
			$userprofiles = $db->query("SELECT id,username,photo FROM profile WHERE active = '1' AND id = '".$db->intcast($selectfriends[$j]['fid'])."' ORDER BY RAND() LIMIT 10");
			$count = count($userprofiles);

			if($count >=1) {
				
				for($i=0;$i<$count;$i++) {
					$friend_list .= "<div class='messenger-friend-item'>";
					$friend_list .= "<div class='messenger-friend-item-name'>";
					$friend_list .= "<span class='messenger-image-follow' alt=\"".ucfirst($db->clean($userprofiles[$i]['username'],'encode'))."\" title=\"".ucfirst($db->clean($userprofiles[$i]['username'],'encode'))."\" style=\"background:url('".$host.$db->clean($userprofiles[$i]['photo'],'encode')."') !important; background-size: cover!important;\"></span>";
					$friend_list .= "</div></div>";
				}
			}
		}
	} else {
		$friend_list .= "<div id=\"messenger-no-friends\">No friends yet, start making new friends.</div>";
	}
?>

<!DOCTYPE html>
<html>
	<head>
	<title>Twigpage - Social Timelines.</title>	<meta charset="utf-8">
	<meta name="description" content="Twigpage is a new social media platform.">
	<meta name="keywords" content="twigpage, social media, facebook, twitter, twitter alternative, mastodon alternative">
	<meta name="author" content="Twigpage">
	<meta name="Pragma" content="no-cache">
	<meta name="Cache-Control" content="no-cache">
	<meta name="Expires" content="-1">
	<meta name="revisit-after" content="3 days">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link href="https://www.twigpage.com/resources/style/themes/default/reset.css?rev=1.6.41" rel="stylesheet">
	<link href="https://www.twigpage.com/resources/style/themes/default/style.css?rev=1.6.41" rel="stylesheet">
	<link href="https://www.twigpage.com/resources/style/themes/default/mobile.css?rev=1.6.41" rel="stylesheet">
	<style>
	
	body {
		background-color:#eee;
	}
	
	
	#messenger-menu {
		background-color: lightblue;
		height: 40px;
		padding:20px;
	}

	#messenger {
		height:90vh;
		overflow:scroll;
		overflow-x: hidden;
	}
	
	.messenger-image-follow {
		width: 46px;
		height: 46px;
		float: left;
		border-radius: 50%;
		margin-top: -3px;
		margin-right: 10px;
		border: 1px solid #ded;
	}
	
	.messenger-friend-item {
		float:left;
	}
	
	.messenger-friend-item-name {
		float:left;
	}
	
	#messenger-no-friends {
		float:left;
	}

	.messenger-chat {
		height: 50px;
		padding:20px;
		margin: 15px;
		font-size: 14px;
		border-radius: 5px;
		clear: both;
		border: 1px solid #ddd;
		box-shadow: 1px 1px 5px 0px #ddd;
		background-color: #fff;
		border-radius: 10px;
		cursor: pointer;
		opacity: 0.8;
	}

	.messenger-image {
		
		
	}
	
	#messenger-bubble {
		background-color: lightblue;
		padding: 20px;
		position: absolute;
		right: 30px;
		bottom: 30px;
		width: 30px;
		height:30px;
		border-radius: 50%;
		box-shadow: 1px 1px 5px 0px #999;
		z-index:5;
	}

	#messenger-start {
		padding: 20px;
		position: absolute;
		right: 10px;
		bottom: 10px;
		height: 30px;
		width: 30px;
		position: absolute;
		left: 10px;
		bottom: 10px;
		width:90vw;
		float:left;
	}
	
	#messenger-input {
		width:80%;
		border-radius:30px;
		float:left;
	}
	
	#messenger-input:hover {
		background-color: #fff;
	}
	
	#messenger-send {
		border-radius: 30px;
		height: 42px;
		width: 42px;
		float: left;
		display: block;
		font-weight: 400;
		line-height: 1.5;
		color: #495057;
		background-color: lightblue;
		border-radius: 50%;
		padding: 10px;
		font-size: 15px;
		font-family: "Segoe UI", Roboto, Arial, sans-serif;
		margin-top: 6px;
		margin-bottom: 4px;
		margin-left: -42px;
	}	
	
	.messenger-photo {
		float:left;
		width: 50px;
		height: 50px;
		border: 1px solid #ded;
		border-radius:50%;
	}
	
	.messenger-text {
		float:left;
		margin-left: 15px;
	}
	
	</style>
	</head>
	<body>
	
		<div id="messenger">
			
			<div id="messenger-menu"><?php echo $friend_list;?></div>
			
			<?php
			
				$messages = [];		
				$uid = '41';
				$toid = '';
				$stmt = $mysqli->prepare("SELECT * FROM messenger where uid = ? AND toid = ?");
				$stmt->bind_param("ii", $uid,$toid);
				$stmt->execute();
				$query = $stmt->get_result();

				while($row = $query->fetch_array(MYSQLI_ASSOC)) {
					$messages[] = $row;
				}
				
				$stmt->close();
				
			for($i=0; $i < count($messages); $i++) { 
			?>
			<div class="messenger-chat">
				<div class="messenger-photo">
				<div class="messenger-image" style="background:url('<?php echo $host . $db->clean($profile[0]["photo"],'encode');?>') !important; background-size: cover!important; background-color: #fff!important;"></div>
				</div>
				<div class="messenger-text"><?php echo $messages[$i]['message'];?></div>
			</div>
			<?php
			}
			?>

		</div>
		
		<div id="messenger-start"><input type="text" id="messenger-input" /><input type="button" onclick="Social.postMessenger('messenger-input','<?php echo $uid;?>','<?php echo $toid;?>','<?php echo $csrf;?>');" name="submit" value="S" id="messenger-send" /></div>
		<div id="messenger-bubble">3</div>
	<script src="https://www.twigpage.com/resources/js/main.js?rev=1.5.6<?php time();?>" type="text/javascript"></script>
	<script>
	Social.scroller('bottom','messenger');
	</script>
	</body>
	</html>