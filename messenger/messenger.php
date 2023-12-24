<?php

	session_start();

	require("../resources/PHP/db.class.php");
	require("../resources/PHP/social.class.php");

	$db 	= new sql();
	$social = new social();
	$host 	= 'https://www.twigpage.com/';
	$toid 	= $db->intcast($_REQUEST['toid']);

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
	
	$profile_friend = $db->query("SELECT id,username,photo FROM profile WHERE id = '".$db->intcast($toid)."'");
	$friend_name = $profile_friend[0]["username"];
				
	$friend_list .= "<a href='/messenger/".$uid."/".$profile_friend[0]['id']."/'><div class='messenger-friend-item'>";
	$friend_list .= "<div class='messenger-friend-item-name'>";
	$friend_list .= "<span class='messenger-image-follow-active' alt=\"".ucfirst($db->clean($profile_friend[0]['username'],'encode'))."\" title=\"".ucfirst($db->clean($profile_friend[0]['username'],'encode'))."\" style=\"background:url('".$host.$db->clean($profile_friend[0]['photo'],'encode')."') !important; background-size: cover!important;\"></span>";
	$friend_list .= "</div></div></a>";

	$selectfriends = $db->query("SELECT * FROM friends WHERE uid = '".$db->intcast($uid)."' and fid != '".$db->intcast($profile_friend[0]['id'])."' and blk != '1' LIMIT 4");
	$countfriends = count($selectfriends);
	
	if($countfriends  >=1) {
		
		for($j=0; $j<$countfriends; $j++) {
			
			$userprofiles = $db->query("SELECT id,username,photo FROM profile WHERE id = '".$db->intcast($selectfriends[$j]['fid'])."'");
			$count = count($userprofiles);
			
			if($count >=1) {
				
				for($i=0;$i<$count;$i++) {
					
					$active = '';

					if($toid == $userprofiles[$i]['id']) {
						$active = '-active';
					}
					
					$friend_list .= "<a href='/messenger/".$uid."/".$userprofiles[$i]['id']."/'><div class='messenger-friend-item'>";
					$friend_list .= "<div class='messenger-friend-item-name'>";
					$friend_list .= "<span class='messenger-image-follow".$active."' alt=\"".ucfirst($db->clean($userprofiles[$i]['username'],'encode'))."\" title=\"".ucfirst($db->clean($userprofiles[$i]['username'],'encode'))."\" style=\"background:url('".$host.$db->clean($userprofiles[$i]['photo'],'encode')."') !important; background-size: cover!important;\"></span>";
					$friend_list .= "</div></div></a>";
				}
			}
		}
	} else {
		$friend_list .= "<div id=\"messenger-no-friends\">No friends yet, start making new friends.</div>";
	}
	
	$profile = $db->query("SELECT id,username,photo FROM profile WHERE id = '".$db->intcast($uid)."'");
	
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

	<link href="https://www.twigpage.com/resources/style/themes/default/reset.css?rev=1.6.41<?php echo time();?>" rel="stylesheet">
	<link href="https://www.twigpage.com/resources/style/themes/default/style.css?rev=1.6.41<?php echo time();?>" rel="stylesheet">
	<link href="https://www.twigpage.com/resources/style/themes/default/mobile.css?rev=1.6.41<?php echo time();?>" rel="stylesheet">
	<link href="https://www.twigpage.com/resources/style/themes/default/messenger.css?rev=1.6.41<?php echo time();?>" rel="stylesheet">
	</head>
	<body>
	
		<div id="messenger">
			
			<div id="messenger-menu"><?php echo $friend_list;?></div>
			
			<?php
					
				$messages = [];	
				

				$stmt = $mysqli->prepare("SELECT * FROM messenger where toid = ? and uid = ?");
				$stmt->bind_param("ii", $uid, $toid);
				$stmt->execute();
				$query = $stmt->get_result();

				while($row = $query->fetch_array(MYSQLI_ASSOC)) {
					
					$flaggedlist = $db->query("SELECT * FROM flagged where flaggedby = '".$db->intcast($uid)."' and chatid = '".$row['id']."'");
					if(count($flaggedlist) == 0) {
						$messages[] = $row;
					}
				
				}
				
				$stmt->close();
				
				$stmt = $mysqli->prepare("SELECT * FROM messenger where uid = ? AND toid = ?");
				$stmt->bind_param("ii", $uid,$toid);
				$stmt->execute();
				$query = $stmt->get_result();

				while($row = $query->fetch_array(MYSQLI_ASSOC)) {
					$flaggedlist = $db->query("SELECT * FROM flagged where flaggedby = '".$db->intcast($uid)."' and chatid = '".$row['id']."'");
					if(count($flaggedlist) == 0) {
						$messages[] = $row;
					}
				}
				
				$stmt->close();
		
				array_multisort(
					array_column($messages, 'id'),
					$messages
				);
				
			for($i=0; $i < count($messages); $i++) { 
				if($messages[$i]['uid'] != $uid)  {
					$friend_uid = $db->intcast($messages[$i]['uid']);
					$friend_toid = $db->intcast($messages[$i]['toid']);
					$friend_photo = '/'.$db->clean($profile_friend[0]["photo"],'encode');
					$photo = $db->clean($profile_friend[0]["photo"],'encode');
					} else {
					$photo = $db->clean($profile[0]["photo"],'encode');
				}
				
			?>
			<div class="messenger-chat" id="messenger-chat<?php echo $messages[$i]['id'];?>">
				<div class="messenger-photo">
				<div class="messenger-image" style="background:url('<?php echo $host . $photo;?>') !important; background-size: cover!important; background-color: #fff!important;"></div>
				</div>
				<div class="messenger-text"><?php echo $messages[$i]['message'];?>
				<?php if($messages[$i]['uid'] != $uid)  { ?>
				<span class="options-opt-icon"><img class="options-opt-timeline" onclick="Social.showTimelineOptions('timeline-options-box-<?php echo $i;?>');" src="<?php echo $host ;?>resources/images/icons/opt.png"/></span>
					<div id="timeline-options-box-<?php echo $i;?>" class="timeline-options-box-chat">
							<img src="/resources/images/icons/close.png" class="timeline-options-box-close" onclick="Social.hide('timeline-options-box-<?php echo $i;?>');"/>
							<ul>
							<li onclick="Social.timelineoptions('<?php echo $host;?>','hidechat','<?php echo $db->intcast($messages[$i]['id']);?>','<?php echo $db->intcast($messages[$i]['id']);?>','<?php echo $db->clean($csrf,'encode');?>'); Social.hide('messenger-chat<?php echo $messages[$i]['id'];?>')"><span class="timeline-options-box-icon-hide"></span>hide this chat</li>
							<li onclick="Social.timelineoptions('<?php echo $host;?>','flag','<?php echo $db->intcast($messages[$i]['id']);?>','<?php echo $db->intcast($friend_uid);?>','<?php echo $db->clean($csrf,'encode');?>'); Social.hide('messenger-chat<?php echo $messages[$i]['id'];?>')"><span class="timeline-options-box-icon-flag"></span>flag this chat</li>
							<li onclick="Social.timelineoptions('<?php echo $host;?>','block','<?php echo $db->intcast($friend_uid);?>','<?php echo $db->intcast($friend_uid);?>','<?php echo $db->clean($csrf,'encode');?>'); Social.hide('messenger-chat<?php echo $messages[$i]['id'];?>')"><span class="timeline-options-box-icon-block"></span>block this user</li>
							</ul>
						</div>
				<?php 
				} 
				?>
				</div>
			</div>
			<?php
			
				if($messages[$i]['readit'] != '1' && $messages[$i]['toid'] == $uid) { 
					$stmt = $mysqli->prepare("UPDATE messenger SET readit = ? WHERE id = ?");
					$read = 1;
					$readid = $messages[$i]['id'];
					$stmt->bind_param("ii", $read, $readid);
					$stmt->execute();
					$stmt->close();
				}
			}
			?>

		</div>
		
	<div id="messenger-start">
	<input type="text" id="messenger-input" onfocus="Social.pingMessenger('typing');" placeholder="Send a message to <?php echo $friend_name;?>" autocomplete="off" spellcheck="false"/><input type="button" onclick="Social.postMessenger('post','messenger-input','<?php echo $uid;?>','<?php echo $toid;?>','<?php echo $csrf;?>','<?php echo $db->clean($profile[0]["photo"],'encode');?>');" name="submit" value="" id="messenger-send" /></div>
	<script src="https://www.twigpage.com/resources/js/main.js?rev=1.5.6<?php time();?>" type="text/javascript"></script>
	<script>
	Social.scroller('bottom','fetch','messenger','<?php echo $friend_uid;?>','<?php echo $friend_uid;?>','<?php echo $csrf;?>','<?php echo $friend_photo;?>');
	document.getElementById('messenger-input').focus();
	</script>
	</body>
	</html>