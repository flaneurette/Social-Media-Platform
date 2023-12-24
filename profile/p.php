<?php

session_start();

require("../resources/PHP/db.class.php");
require("../resources/PHP/social.class.php");

$db 	= new sql();
$social = new social();

if(isset($_REQUEST['profileid'])) {
	$profile = $db->clean($_REQUEST['profileid'],'encode');
	$check 	= $db->select('profile','*','username',$profile);	
	$profileid = $db->intcast($check[0]['id']);

} else {
	$check 	= $db->select('profile','*','id',$uid);	
	$profileid = $db->intcast($check[0]['id']);
	
}

if($profileid == $db->intcast($_SESSION['uid'])) {
	$followbutton = false;
	} else {
	$followbutton = true;
}

$uid = $profileid;

// get database information on profile.
//$profile  = $db->select('profile','*','id',$uid);
$timeline 	= $db->select('timeline','*','uid',$uid);

$userprofile = [];
$profile = [];		
		
$stmt = $mysqli->prepare("SELECT * FROM profile where id = ? LIMIT 1");

$params = array("s",$uid);

foreach($params as $key => $value) $userprofile[$key] = &$params[$key];
call_user_func_array(array($stmt, 'bind_param'), $userprofile);
$stmt->execute();

if($stmt->error) {
	echo $stmt->error;
}

$query = $stmt->get_result();

while($row = $query->fetch_array(MYSQLI_ASSOC)) {
	$profile[] = $row;
}

$stmt->close();

$stats_followers = $db->query("SELECT COUNT(*) AS followers FROM friends where uid = '".$uid."'");
$stats_following = $db->query("SELECT COUNT(*) AS following FROM friends where fid = '".$uid."'");

// get and set a proper token for our instance.
if(!isset($_SESSION['token']) || empty($_SESSION['token']) ) {
	$csrf = $db->getToken();
	$_SESSION['token'] = $csrf;
}  else {
	$csrf = $db->clean($_SESSION['token'],'encode');
}

// count number of times from timeline
$numberoftimelines = count($timeline);

if($numberoftimelines < 1) {
	$numberoftimelines = 0;
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
						<li><a href="<?php echo $host;?>settings/">Settings</a></li>
						<li class="mobile-block"><a href="<?php echo $statusloggedurl;?>"><?php echo $statuslogged;?></a></li>
						<li><form name="search" action="<?php echo $host;?>search/" method="POST"><input type="hidden" name="csrf" value="<?php echo $db->clean($csrf,'encode');?>" /><input type="text" id="nav-search" name="search" size="30" value="search..." /></form></li>
						</ul>	
					</div>
					<div id="nav-center">
						<div id="timeline-username"><?php echo '@'.$db->clean(ucfirst($profile[0]["username"]),'encode');?></div> <span id="times-count"><?php echo $numberoftimelines;?> <img src="../resources/images/logo-18x18.png" /></span>
						
						<div id="timeline-header"><!-- photo --></div>
							<span><img id="timeline-profile-picture" src="<?php echo $host . $db->clean($profile[0]["photo"],'encode');?>"></span>
							<div id="timeline-profile">
							
							<div>
							<?php 
							if($followbutton == true) { 
							
								if(isset($_SESSION['loggedin'])) { 
							?>
							<span id="post-timeline-link"><a href="#"  onclick="Social.follow('<?php echo $db->intcast($_SESSION['uid']);?>','<?php echo $profile[0]["id"];?>','<?php echo $db->clean($csrf,'encode');?>')">FOLLOW</a></span>
							<?php 
								}
							} else { ?>
							<span id="post-timeline-link"><a href="#"  onclick="Social.dom('float','display','block')">POST</a></span>
							<?php 
							}
							?>
							</div>
							
							<div>
							<span><?php echo  $social->prepareLINK($profile[0]["link"],'encode');?></span>
							</div>
							<div>
							<span><?php echo $db->clean($profile[0]["bio"],'encode');?></span>
							</div>
							<div>
							<span><?php echo $db->clean($profile[0]["accounttype"],'encode');?>,</span>
							<span><?php echo $db->clean($profile[0]["location"],'encode');?>,</span>
							<span>Member since: <?php echo $db->clean($profile[0]["joined"],'encode');?></span>
							</div>

							<div>
							<span><?php echo $db->clean($stats_followers[0]['followers'],'encode');?> Following</span>
							<span><?php echo $db->clean($stats_following[0]['following'],'encode');?> Followers</span>
							</div>

							</div>
						<div id="timeline">
						<?php
						
						$times = count($timeline);
						
						if($times > 250) {
							$times = 250;
						} 
						for($i=0; $i < $times; $i++) {
							
						
							$stats = $db->query("SELECT * FROM stats WHERE uid = ".$db->intcast($profile[0]["id"])." and pid = ".$db->intcast($timeline[$i]['tid']));
							
							if(!empty($stats)) { 
								$num_views  = $db->intcast($stats[0]["views"]);
								$num_shares = $db->intcast($stats[0]["shares"]);
								$num_stars  = $db->intcast($stats[0]["starred"]);
								$num_hearts = $db->intcast($stats[0]["likes"]);
								} else {
								$num_views = '&nbsp;';
								$num_shares = '&nbsp;';
								$num_stars  = '&nbsp;';
								$num_hearts = '&nbsp;';	
							}

						?>
						<div class="timeline-post">
						<div id="timeline-photo"><img class="timeline-photo" src="<?php echo $host . $db->clean($profile[0]["photo"],'encode');?>" width="50"/></div>
						<a href="status/<?php echo $db->intcast($timeline[$i]['id']);?>" id="status-id"><div class="timeline-post-text">
						<a class="profile-link" href="<?php echo $host . '@' .ucfirst($db->clean($profile[0]["username"],'encode'));?>" class="profile-link">@<?php echo ucfirst($db->clean($profile[0]["username"],'encode'));?></a>
						<span class="post-date"><?php echo date("M jS, Y", $db->intcast($timeline[$i]['created'])); ?></span>
						<br />
						<div class="twig"><?php echo $social->prepareHTML($timeline[$i]['post']);?></div>
						</div></a>
						<div class="timeline-options">
						<span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/comment.png" onclick="Social.showcommentbar('timeline-post-bar-<?php echo $db->intcast($id);?>');" /></span>
						<span class="tl-opt-num"><?php echo $num_hearts;?></span><span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/heart.png"  onclick="Social.timelineoptions('<?php echo $host;?>','heart','<?php echo $db->intcast($_SESSION['uid']);?>','<?php echo $db->intcast($timeline[$i]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>')" /></span>
						<span class="tl-opt-num"><?php echo $num_stars;?></span><span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/star.png" onclick="Social.timelineoptions('<?php echo $host;?>','star','<?php echo $db->intcast($_SESSION['uid']);?>','<?php echo $db->intcast($timeline[$i]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>')"/></span>
						<!-- <span class="tl-opt-num"><?php echo $num_shares;?></span><span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/share.png" onclick="Social.timelineoptions('<?php echo $host;?>','share','<?php echo $db->intcast($_SESSION['uid']);?>','<?php echo $db->intcast($timeline[$i]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>')"/></span> -->
						</div>
						</div>
						<div id="timeline-post-bar-<?php echo $db->intcast($id);?>" style="display:none;clear:both;width:100%;">
							<form name="profile-post-back" action="<?php echo $host;?>postback/" method="post">
							<input type="hidden" name="csrf" value="<?php echo $db->clean($csrf,'encode');?>" />
							<input type="hidden" name="comment-id" value="<?php echo $db->intcast($timeline[$i]['tid']);?>" />
							<input type="hidden" name="uid-id" value="<?php echo $db->intcast($timeline[$i]['uid']);?>" />
							<input type="hidden" name="to-id" value="<?php echo $db->intcast($profile[0]['id']);?>" />
							<input type="hidden" name="at-user" value="@<?php echo $db->clean($profile[0]["username"],'encode');?>" />
							<textarea name="post-message" rows="3" draggable="false" id="timelinepost-textarea" placeholder="Reply to @<?php echo $db->clean($profile[0]["username"],'encode');?>" onkeydown="Social.timelinePost('timelinepost-textarea','charcounter');"></textarea>
							<span id="charcount"><span id="charcounter">255</span> characters left.</span>
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