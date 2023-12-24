<br /><?php

	$userprofiles = $db->query("SELECT id,username,photo FROM profile WHERE active = '1' ORDER BY RAND() LIMIT 10");
?>
<div>
	<strong>Who to follow...</strong>
</div>
<div class="friend-list">
<?php

	$count = count($userprofiles);
	if($count >=1) {
		for($i=0;$i<$count;$i++) {
		echo "<div class=\"friend-item\">";
		echo "<div class='friend-item-name'><center>".ucfirst($db->clean($userprofiles[$i]['username'],'encode'))."</center></div>";
		echo "<div><a href='".$host.'@'.$db->clean($userprofiles[$i]['username'],'encode')."'><img src='".$host .$db->clean($userprofiles[$i]['photo'],'encode')."' width='50' class='friend-photo'/></a></div>";
		echo "</div>";
		}
	}
?>
</div>
<br />