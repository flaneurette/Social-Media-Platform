<?php

session_start();

require("../resources/PHP/db.class.php");

$db = new sql();

// login check
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != '') {
	$statuslogged = "Log out";
	$statusloggedurl = "../logout/";
	$uid = $db->intcast($_SESSION['uid']);
	} else {
	$statuslogged = "Log in";
	$statusloggedurl = "login/";
	$statusloggedurl = "../login/";
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

if(isset($uid) && $uid != '0') {
	$db->q("UPDATE timeline SET readit = '1' WHERE toid = '".$db->intcast($uid)."'");
}
?>

<!DOCTYPE html>
<html>
	<head>
	<?php
	include("../resources/PHP/header.php");
	
	if(isset($uid)) {
		$selectown = $db->query("SELECT * FROM profile WHERE id = '".$db->intcast($uid)."'");
		$countown = count($selectown);
	}
						
	if(isset($selectown[0]["bodycolor"]) != NULL && isset($selectown[0]["textcolor"]) != NULL) { 

		if(strstr($selectown[0]["bodycolor"],'#') && strstr($selectown[0]["textcolor"],'#')) { 
		
			$bodycolor = substr($db->clean($selectown[0]["bodycolor"],'encode'),0,7);
			$textcolor = substr($db->clean($selectown[0]["textcolor"],'encode'),0,7);
			
			echo PHP_EOL;
			echo "<style>";
			echo "body { background-color:".$bodycolor."; color:".$textcolor."; } ";
			echo "* { color:".$textcolor."; } ";
			echo "a:link, a:visited { color:".$textcolor."; } ";
			echo ".timeline-post { border-top: 1px solid ".$bodycolor."; border-bottom: 1px solid ".$bodycolor."; }";
			echo "#timeline-profile-picture { border: 2px solid ".$bodycolor."; }";
			echo ".timeline-photo { border: 5px solid ".$bodycolor.";";
			echo ".timeline-post:hover { background-color:".$bodycolor."80; }";
			echo " input, textarea, #timelinepost-textarea, .timelinepost-textarea { background-color:".$bodycolor."40!important; color:".$textcolor."!important; border: 1px solid ".$textcolor."!important; }";
			echo "#nav-left, #nav-left li a:link, #nav-left li a:visited { color: ".$textcolor."; }  ";
			echo "#nav-left, #nav-left li a:hover {color: ".$textcolor."40; } ";
			echo "</style>";
			echo PHP_EOL;
			
		}
	}
	?>
		<div id="wrapper">
					<div id="nav-left">
						<ul>
						<li><a href="<?php echo $host;?>profile/">Profile</a></li>
						<li><a href="<?php echo $host;?>timeline/">Timeline</a></li>
						<li><a href="<?php echo $host;?>mentions/">Mentions <?php if($count_mentions >=1) { echo "<span class=\"messages-alert\">" . $count_mentions ."</span>"; } ?></span></a></li>
						<li><a href="<?php echo $host;?>settings/">Settings</a></li>
						<li class="mobile-block"><a href="<?php echo $statusloggedurl;?>"><?php echo $statuslogged;?></a></li>
						<li><form name="search" action="<?php echo $host;?>search/" method="POST"><input type="hidden" name="csrf" value="<?php echo $db->clean($csrf,'encode');?>" /><input type="text" id="nav-search" name="search" size="30" value="search..." /></form></li>
						</ul>	
						<div id="friends"><?php include("../resources/PHP/friends.php"); ?></div><div id="who-to-follow"><?php include("../resources/PHP/whotofollow.php"); ?></div>
					</div>
					
					<div id="nav-center">
						<div id="timeline-username">Mentions</div>
						
						<?php

						$search = [];
						$timelines = [];
						
						$searchfor = "%@".$db->clean($_SESSION["profile"]["username"],'search')."%";
						$stmt = $mysqli->prepare("SELECT * FROM timeline,profile  WHERE profile.id = timeline.uid AND timeline.post LIKE ? ORDER BY timeline.tid  DESC");
						$params = array("s",$searchfor);
						foreach($params as $key => $value) $search[$key] = &$params[$key];
						call_user_func_array(array($stmt, 'bind_param'), $search);
						$stmt->execute();
						$query = $stmt->get_result();
						
						while($row = $query->fetch_array(MYSQLI_ASSOC)) {
							$timelines[] = $row;
						}

						$stmt->close();

						for($k=0; $k < count($timelines); $k++) { 

						?>
						<div class="timeline-post">	<div id="timeline-photo"><img class="timeline-photo" src="<?php echo $host . $db->clean($timelines[$k]['photo'],'encode');?>" width="50"/></div>
						
							<div class="timeline-post-text"  onclick="document.location='<?php echo $host . '@' .ucfirst($db->clean($timelines[$k]['username'],'encode'));?>/<?php echo $db->intcast($timelines[$k]['uid']);?>/status/<?php echo $db->intcast($timelines[$k]['tid']);?>/';">
								<a class="profile-link" href="<?php echo $host . '@' .ucfirst($db->clean($timelines[$k]['username'],'encode'));?>" class="profile-link">@<?php echo $db->clean($timelines[$k]['username'],'encode');?></a>
								<span class="post-date"><?php echo date("F j, Y", $timelines[$k]['created']); ?></span>
								<br />
								<div class="twig"><?php echo $db->prepareHTML($timelines[$k]['post']);?></div>
							</div>
								<div class="timeline-options">
									<span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/comment.png" onclick="Social.showcommentbar('timeline-post-bar-<?php echo $i;?>');" /></span>
									<span class="tl-opt-num"></span><span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/heart.png"  onclick="Social.timelineoptions('<?php echo $host;?>','heart','<?php echo $db->intcast($timelines[$k]['uid']);?>','<?php echo $db->intcast($timelines[$k]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>')" /></span>
									<span class="tl-opt-num"></span><span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/star.png" onclick="Social.timelineoptions('<?php echo $host;?>','star','<?php echo $db->intcast($timelines[$k]['uid']);?>','<?php echo $db->intcast($timelines[$k]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>')"/></span>
									<span class="tl-opt-num"></span><span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/share.png" onclick="Social.timelineoptions('<?php echo $host;?>','share','<?php echo $db->intcast($timelines[$k]['uid']);?>','<?php echo $db->intcast($timelines[$k]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>')"/></span>
								</div>
							</div>
							<div class="" id="timeline-post-bar-<?php echo $i;?>" style="display:none;clear:both;width:100%;">
								<form name="post" action="../postback/" method="POST">
								<input type="hidden" name="csrf" value="<?php echo $db->clean($csrf,'encode');?>" />
								<input type="hidden" name="comment-id" value="<?php echo $db->intcast($timelines[$k]['tid']);?>" />
								<input type="hidden" name="uid-id" value="<?php echo $db->intcast($timelines[$k]['uid']);?>" />
								<input type="hidden" name="at-user" value="@<?php echo $db->clean($timelines[$k]['username'],'encode');?>" />
								<textarea name="post-message" rows="3" draggable="false" id="timelinepost-textarea-<?php echo $i;?>" placeholder="Reply to @<?php echo $db->clean($timelines[$k]['username'],'encode');?>" onkeydown="Social.timelinePost('timelinepost-textarea-<?php echo $i;?>','charcounter-<?php echo $i;?>');"></textarea>
								<span id="charcount"><span id="charcounter-<?php echo $i;?>">255</span> characters left.</span>
								<input type="submit" name="post" value="Post back" style="float:none!important;"/>
								</form>
								<div class="timeline-options"></div>
							</div>
							<?php
							
						}
						?>
					</div>
		<?php 
		include("../resources/PHP/rightnavigation.php");
		echo "</div>";
		include("../resources/PHP/postform.php");
		include("../resources/PHP/footer.php");
		?>
	</body>
</html>
<?php
$db->close();
?>