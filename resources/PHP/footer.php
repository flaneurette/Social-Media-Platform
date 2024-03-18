
<script src="<?php echo $host;?>resources/js/main.js?rev=1.7.1" type="text/javascript"></script>
<script src="<?php echo $host;?>resources/js/Emojis.js?rev=1.7.1" type="text/javascript"></script>
<?php

$showbubblewarning = '';
$mobile = '';

	if(isset($_SESSION['uid'])) {
		
		$stmt = $mysqli->prepare("SELECT COUNT(message) FROM messenger where toid = ? AND readit != ?");
		$uid = $db->intcast($_SESSION['uid']);
		$readit = 1;
		$stmt->bind_param("ii", $uid, $readit);
		$stmt->execute();
		$query = $stmt->get_result();

		while($row = $query->fetch_array(MYSQLI_ASSOC)) {
			$messages[] = $row;
		}
		
		if($messages[0]["COUNT(message)"] >=1) {
			$newmessenger = $messages["COUNT(message)"];
			$showbubblewarning = true;
		}
	}

if(isset($_REQUEST['mobile']) == true || $mobile == true) {
	
} else { 
?>
<a href="#" onclick="Social.show('messenger-frame');">
<div id="messenger-bubble"></div>
<?php
	if($showbubblewarning == true) { 
		echo '<div id="messenger-unread">'.$newmessenger.'</div>';
	}
	echo "</a>";
}
?>


<div id="messenger-frame"><iframe src="/messenger/" frameborder="0" id="messenger-iframe"></iframe></div>

<div id="footer"> 
	<a href="<?php echo $host;?>contents/privacy/">Privacy Policy</a> &amp; <a href="<?php echo $host;?>contents/terms/">Terms</a>
</div>
