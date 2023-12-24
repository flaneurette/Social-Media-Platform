<?php

	if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != '' && !empty($_SESSION['uid'])) {
		$uid = $db->intcast($_SESSION['uid']);
		} else {
		header("Location: ../");
		exit;
	}
	
	if(!isset($_SESSION['token']) || empty($_SESSION['token']) ) {
		$csrf = $db->getToken();
		$_SESSION['token'] = $csrf;
		} else {
		$csrf = $db->clean($_SESSION['token'],'encode');
	}
?>
<span>
	<strong>Friends</strong>
</span>
<div class="friend-list">
<?php

	$selectfriends = $db->query("SELECT * FROM friends WHERE uid = '".$db->intcast($uid)."' ORDER BY RAND() LIMIT 10");
	$countfriends = count($selectfriends);
	
	if($countfriends  >=1) {
		
		for($j=0; $j<$countfriends; $j++) {
			
			$userprofiles = $db->query("SELECT id,username,photo FROM profile WHERE active = '1' AND id = '".$db->intcast($selectfriends[$j]['fid'])."' ORDER BY RAND() LIMIT 10");
			$count = count($userprofiles);

			if($count >=1) {
				
				for($i=0;$i<$count;$i++) {
				echo "<div class=\"friend-item\">";
				echo "<div class='friend-item-name'><center>".ucfirst($db->clean($userprofiles[$i]['username'],'encode'))."</center></div>";
				echo "<div><a href='".$host.'@'.$db->clean($userprofiles[$i]['username'],'encode')."'><span class=\"image-follow\" style=\"background:url('".$host.$db->clean($userprofiles[$i]['photo'],'encode')."') !important; background-size: cover!important;\"></span></a></div>";
				echo "</div>";
				}
			}
		}
	} else {
		echo "<div id=\"no-friends\">No friends yet, start making new friends.</div>";
	}
?>
</div>
<br />