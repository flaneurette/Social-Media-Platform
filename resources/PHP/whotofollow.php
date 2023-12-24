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

	$userprofiles = $db->query("SELECT id,username,photo FROM profile WHERE active = '1' ORDER BY RAND() LIMIT 10");
?>
<span>
	<strong>Who to follow...</strong>
</span>
<div class="friend-list">
<?php

	$count = count($userprofiles);
	if($count >=1) {
		for($i=0; $i<$count; $i++) {
		echo "<div class=\"friend-item\">";
		echo "<div class='friend-item-name'><center>".ucfirst($db->clean($userprofiles[$i]['username'],'encode'))."</center></div>";
		echo "<div><a href='".$host.'@'.$db->clean($userprofiles[$i]['username'],'encode')."'><span class=\"image-follow\" style=\"background:url('".$host.$db->clean($userprofiles[$i]['photo'],'encode')."') !important; background-size: cover!important;\"></span></a></div>";
		echo "</div>";
		}
	}
?>
</div>