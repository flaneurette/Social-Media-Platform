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
			echo $social->css($selectown[0]["bodycolor"],$selectown[0]["textcolor"]);
		}
	}
	?>
		<div id="wrapper">
					<div id="nav-left">
						<ul>
						<li><a href="<?php echo $host;?>profile/">Profile</a></li>
						<li><a href="<?php echo $host;?>timeline/">Timeline</a></li>
						<li class="selected"><a href="<?php echo $host;?>mentions/">Mentions <?php if($count_mentions >=1) { echo "<span class=\"messages-alert\">" . $count_mentions ."</span>"; } ?></span></a></li>
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

							$stats = $db->query("SELECT COUNT(likes),COUNT(starred),COUNT(views),COUNT(shares) FROM stats WHERE uid = ".$db->intcast($timelines[$k]['uid'])." and pid = ".$db->intcast($timelines[$k]['tid']));

							$num_views  = $db->intcast($stats[0]["COUNT(views)"]);
							$num_shares = $db->intcast($stats[0]["COUNT(shares)"]);
							$num_stars  = $db->intcast($stats[0]["COUNT(starred"]);
							$num_hearts = $db->intcast($stats[0]["COUNT(likes)"]);
									
							if($num_views == '0') {
								$num_views = '';
							}
									
							if($num_shares == '0') {
								$num_shares = '';
							}
									
							if($num_stars == '0') {
								$num_stars = '';
							}
									
							if($num_hearts == '0') {
								$num_hearts = '';
							}
							
						?>
						<div class="timeline-post">	<div id="image-container"><div id="image" style="background:url('<?php echo $host . $db->clean($timelines[$k]["photo"],'encode');?>') !important; background-size: cover!important; background-color: #fff!important;"></div></div>

						
							<div class="timeline-post-text"  onclick="document.location='<?php echo $host . '@' .ucfirst($db->clean($timelines[$k]['username'],'encode'));?>/<?php echo $db->intcast($timelines[$k]['uid']);?>/status/<?php echo $db->intcast($timelines[$k]['tid']);?>/';">
								<a class="profile-link" href="<?php echo $host . '@' .ucfirst($db->clean($timelines[$k]['username'],'encode'));?>" class="profile-link">@<?php echo $db->clean($timelines[$k]['username'],'encode');?></a>
								<span class="post-date"><?php echo date("M jS, Y", $timelines[$k]['created']); ?></span>
								<br />
								<div class="twig"><?php echo $social->prepareHTML($timelines[$k]['post']);?></div>
							</div>
								<div class="timeline-options">
									<span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/comment.png" onclick="Social.createCommentForm('timeline-post-bar-<?php echo $k;?>','../postback/','<?php echo $db->clean($csrf,'encode');?>','<?php echo $db->intcast($timelines[$k]['tid']);?>','<?php echo $db->intcast($timelines[$k]['uid']);?>', '<?php echo $db->intcast($timelines[$k]['id']);?>', '<?php echo $db->clean($timelines[$k]['username'],'encode');?>');" /></span>
									<span class="tl-opt-num" id="heart<?php echo $k;?>-num"><?php echo $num_hearts;?></span><span><img class="timeline-icon"  id="heart<?php echo $k;?>" src="<?php echo $host ;?>resources/images/icons/heart.png"  onclick="Social.timelineoptions('<?php echo $host;?>','heart','<?php echo $db->intcast($timelines[$k]['uid']);?>','<?php echo $db->intcast($timelines[$k]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>','heart<?php echo $k;?>')" /></span>
									<span class="tl-opt-num" id="star<?php echo $k;?>-num"><?php echo $num_stars;?></span><span><img class="timeline-icon"  id="star<?php echo $k;?>" src="<?php echo $host ;?>resources/images/icons/star.png" onclick="Social.timelineoptions('<?php echo $host;?>','star','<?php echo $db->intcast($timelines[$k]['uid']);?>','<?php echo $db->intcast($timelines[$k]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>','star<?php echo $k;?>')"/></span>
									<span class="tl-opt-num"><?php echo $num_shares;?></span><span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/share.png" onclick="Social.timelineoptions('<?php echo $host;?>','share','<?php echo $db->intcast($timelines[$k]['uid']);?>','<?php echo $db->intcast($timelines[$k]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>')"/></span>
								</div>
							</div>
							<div class="charcount-mentions" id="timeline-post-bar-<?php echo $k;?>" style="display:none;clear:both;width:100%;">
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