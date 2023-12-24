<?php

session_start();

require("../resources/PHP/db.class.php");
require("../resources/PHP/social.class.php");

$db 	= new sql();
$social = new social();

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
	$uid = $db->intcast($_SESSION['uid']);
}

// get database information on profile.

// get and set a proper token for our instance.
if(!isset($_SESSION['token']) || empty($_SESSION['token']) ) {
	$csrf = $db->getToken();
	$_SESSION['token'] = $csrf;
}  else {
	$csrf = $db->clean($_SESSION['token'],'encode');
}

if(isset($uid) && $uid != '0') {
	$db->q("UPDATE timeline SET readit = '1' WHERE toid = '".$uid."'");
}
?>

<!DOCTYPE html>
<html>
	<head>
	<?php
	include("../resources/PHP/header.php");
	?>
		<div id="wrapper">
					<div id="nav-left">
						<ul>
						<li><a href="<?php echo $host;?>profile/">Profile</a></li>
						<li><a href="<?php echo $host;?>timeline/">Timeline</a></li>
						
						<li><a href="<?php echo $host;?>mentions/">Mentions <?php if($count_mentions >=1) { echo "<span class=\"messages-alert\">" . $count_mentions ."</span>"; } ?></span></a></li>
						<li class="mobile-block"><a href="<?php echo $statusloggedurl;?>"><?php echo $statuslogged;?></a></li>
						<li><form name="search" action="<?php echo $host;?>search/" method="POST"><input type="hidden" name="csrf" value="<?php echo $db->clean($csrf,'encode');?>" /><input type="text" id="nav-search" name="search" size="30" value="search..." /></form></li>
						</ul>	
						<div id="friends"><?php include("../resources/PHP/friends.php"); ?></div><div id="who-to-follow"><?php include("../resources/PHP/whotofollow.php"); ?></div>
					</div>
					
					<div id="nav-center">
						<div id="timeline-username">Messages</div>
						
						<?php


						$timelines = [];
						
						$timeline  = $db->query("SELECT * FROM timeline,profile  WHERE profile.id = timeline.uid AND timeline.post LIKE '%@".$db->clean($_SESSION["profile"]["username"],'search')."%' ORDER BY timeline.tid  DESC");
						array_push($timelines,$timeline);

						for($k=0; $k < count($timelines); $k++) { 
				
							for($i=0; $i < count($timelines[$k]); $i++) {

									$timeline_pre = $db->query("SELECT * FROM timeline WHERE uid = '".$db->intcast($timelines[$k][$i]['uid'])."' and post = '".$db->clean($timelines[$k][$i]['post'],'encode')."' and created = '".$timelines[$k][$i]['created']."'");
							
									$stats = $db->query("SELECT * FROM stats WHERE uid = ".$db->intcast($timelines[$k][$i]['uid'])." and pid = ".$db->intcast($timeline_pre[0]['tid']));
								
									$num_views  = $db->intcast($stats[0]["views"]);
									$num_shares = $db->intcast($stats[0]["shares"]);
									$num_stars  = $db->intcast($stats[0]["starred"]);
									$num_hearts = $db->intcast($stats[0]["likes"]);
							?>
						<div class="timeline-post">	<div id="timeline-photo"><img class="timeline-photo" src="<?php echo $host . $db->clean($timelines[$k][$i]['photo'],'encode');?>" width="50"/></div>
						
							<div class="timeline-post-text"  onclick="document.location='<?php echo $host . '@' .ucfirst($db->clean($timelines[$k][$i]['username'],'encode'));?>/<?php echo $db->intcast($timelines[$k][$i]['uid']);?>/status/<?php echo $db->intcast($timeline_pre[0]['tid']);?>/';">
								<a class="profile-link" href="<?php echo $host . '@' .ucfirst($db->clean($timelines[$k][$i]['username'],'encode'));?>" class="profile-link">@<?php echo $db->clean($timelines[$k][$i]['username'],'encode');?></a>
								<span class="post-date"><?php echo date("M jS, Y", $timelines[$k][$i]['created']); ?></span>
								<br />
								<div class="twig"><?php echo $social->prepareHTML($timelines[$k][$i]['post']);?></div>
							</div>
								<div class="timeline-options">
									<span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/comment.png" onclick="Social.showcommentbar('timeline-post-bar-<?php echo $i;?>');" /></span>
									<span class="tl-opt-num"><?php echo $num_hearts;?></span><span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/heart.png"  onclick="Social.timelineoptions('<?php echo $host;?>','heart','<?php echo $db->intcast($timelines[$k][$i]['uid']);?>','<?php echo $db->intcast($timeline_pre[0]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>')" /></span>
									<span class="tl-opt-num"><?php echo $num_stars;?></span><span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/star.png" onclick="Social.timelineoptions('<?php echo $host;?>','star','<?php echo $db->intcast($timelines[$k][$i]['uid']);?>','<?php echo $db->intcast($timeline_pre[0]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>')"/></span>
								<!-- <span class="tl-opt-num"><?php echo $num_shares;?></span><span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/share.png" onclick="Social.timelineoptions('<?php echo $host;?>','share','<?php echo $db->intcast($timelines[$k][$i]['uid']);?>','<?php echo $db->intcast($timeline_pre[0]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>')"/></span> -->
								</div>
							</div>
							<div class="" id="timeline-post-bar-<?php echo $i;?>" style="display:none;clear:both;width:100%;">
								<form name="post" action="../postback/" method="POST">
								<input type="hidden" name="csrf" value="<?php echo $db->clean($csrf,'encode');?>" />
								<input type="hidden" name="comment-id" value="<?php echo $db->intcast($timeline_pre[0]['tid']);?>" />
								<input type="hidden" name="uid-id" value="<?php echo $db->intcast($timelines[$k][$i]['uid']);?>" />
								<input type="hidden" name="at-user" value="@<?php echo $db->clean($timelines[$k][$i]['username'],'encode');?>" />
								<textarea name="post-message" rows="3" draggable="false" id="timelinepost-textarea-<?php echo $i;?>" placeholder="Reply to @<?php echo $db->clean($timelines[$k]['username'],'encode');?>" onkeydown="Social.timelinePost('timelinepost-textarea-<?php echo $i;?>','charcounter-<?php echo $i;?>');"></textarea>
								<span id="charcount"><span id="charcounter-<?php echo $i;?>">255</span> characters left.</span>
								<input type="submit" name="post" value="Post back" style="float:none!important;"/>
								</form>
								<div class="timeline-options"></div>
							</div>
							<?php
							}
						}
						?>
					</div>
		<?php 
		include("../resources/PHP/rightnavigation.php");
		?>
		</div>

		<?php
		include("../resources/PHP/postform.php");
		include("../resources/PHP/footer.php");
		?>
	</body>
</html>
<?php
$db->close();
?>