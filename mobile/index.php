<?php

	session_start();

	require("../resources/PHP/db.class.php");
	require("../resources/PHP/social.class.php");

	$db 	= new sql();
	$social = new social();
	$host 	= '<?php echo $host;?>';
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

	$alreadyfriends = [];
	$friend_list = '';

	$selectfriends = $db->query("SELECT * FROM friends WHERE uid = '".$db->intcast($uid)."' and blk != '1' LIMIT 50");
	$countfriends = count($selectfriends);
	
	if($countfriends  >=1) {
		
		for($j=0; $j<$countfriends; $j++) {
			
			array_push($alreadyfriends,$selectfriends[$j]['fid']);
			
			$userprofiles = $db->query("SELECT id,username,photo FROM profile WHERE id = '".$db->intcast($selectfriends[$j]['fid'])."'");
			$count = count($userprofiles);
			
			if($count >=1) {
				
				for($i=0;$i<$count;$i++) {
					
					$active = '';

					if($toid == $userprofiles[$i]['id']) {
						$active = '-active';
					}
					
							$showbubblealert = 'messenger-image-follow-list';
							$messages = [];
							$stmt = $mysqli->prepare("SELECT COUNT(message) FROM messenger where toid = ? AND uid = ? AND readit != ?");
							$uid = $db->intcast($_SESSION['uid']);
							$readit = 1;
							$fromid = $userprofiles[$i]['id'];
							$stmt->bind_param("iii", $uid, $fromid, $readit);
							$stmt->execute();
							$query = $stmt->get_result();

							while($row = $query->fetch_array(MYSQLI_ASSOC)) {
								$messages[] = $row;
							}
							
							if($messages[0]["COUNT(message)"] >=1) {

								$showbubblealert = 'messenger-image-follow-list-new';
							}
		
					$friend_list .= "<a href='../@".ucfirst($db->clean($userprofiles[$i]['username'],'encode'))."'><div class='messenger-friend-item'>";
					$friend_list .= "<div class='messenger-friend-item-name-list'>";
					$friend_list .= "<span class='".$showbubblealert.$active."' alt=\"".ucfirst($db->clean($userprofiles[$i]['username'],'encode'))."\" title=\"".ucfirst($db->clean($userprofiles[$i]['username'],'encode'))."\" style=\"background:url('".$host.$db->clean($userprofiles[$i]['photo'],'encode')."') !important; background-size: cover!important;\"></span>";
					$friend_list .= "<span class='messenger-name-profile'><center>".$db->clean($userprofiles[$i]['username'],'encode')."</center></span>";
					$friend_list .= "</div></div></a>";
				}
			}
		}
	} else {
		$friend_list .= "<div id=\"messenger-no-friends\">No friends yet, start making new friends.</div>";
	}
	
	$profile = $db->query("SELECT id,username,photo FROM profile WHERE id = '".$db->intcast($uid)."'");
	
	
	$follow_list = '';
	$userprofiles = $db->query("SELECT * FROM profile ORDER BY RAND() LIMIT 50");
	
	for($i=0;$i<count($userprofiles);$i++) {
		
		if(!in_array($userprofiles[$i]['id'],$alreadyfriends)) { 
		$follow_list .= "<a href='../@".ucfirst($db->clean($userprofiles[$i]['username'],'encode'))."'><div class='messenger-friend-item'>";
		$follow_list .= "<div class='messenger-friend-item-name-list'>";
		$follow_list .= "<span class='messenger-image-follow-list' alt=\"".ucfirst($db->clean($userprofiles[$i]['username'],'encode'))."\" title=\"".ucfirst($db->clean($userprofiles[$i]['username'],'encode'))."\" style=\"background:url('".$host.$db->clean($userprofiles[$i]['photo'],'encode')."') !important; background-size: cover!important;\"></span>";
		$follow_list .= "<span class='messenger-name-profile'><center>".$db->clean($userprofiles[$i]['username'],'encode')."</center></span>";
		$follow_list .= "</div></div></a>";
		}
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

	<link href="<?php echo $host;?>style/themes/default/reset.css?rev=1.6.41<?php echo time();?>" rel="stylesheet">
	<link href="<?php echo $host;?>style/themes/default/style.css?rev=1.6.41<?php echo time();?>" rel="stylesheet">
	<link href="<?php echo $host;?>style/themes/default/mobile.css?rev=1.6.41<?php echo time();?>" rel="stylesheet">
	<link href="<?php echo $host;?>style/themes/default/messenger.css?rev=1.6.41<?php echo time();?>" rel="stylesheet">
	</head>
	<body>
	

		<div class="mobile-index">
		<strong>Who to follow...</strong>
			<div><?php echo $follow_list;?></div>
		</div>

		<hr />
		<div class="mobile-index">
		<strong>Friends</strong>
			<div><?php echo $friend_list;?></div>
		</div>
		
	</body>
	</html>