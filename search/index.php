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

// search
$pieces = explode('=',$_SERVER["REDIRECT_QUERY_STRING"]);
$search = $db->clean($pieces[1],'encode');
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
						<div id="friends"><?php include("../resources/PHP/friends.php"); ?></div><div id="who-to-follow"><?php include("../resources/PHP/whotofollow.php"); ?></div>
					</div>
					
					<div id="nav-center">
						<div id="timeline-username">Searching...</div>
						<div id="timeline">
						<?php
						
						$search = str_replace('#','',$search);
						$hashtag = "%#".$db->clean($search,'search')."%"; 
						
						$sql = "SELECT * FROM timeline,profile WHERE timeline.post LIKE ?  AND profile.id = timeline.uid ORDER BY timeline.tid DESC LIMIT 255";
						$stmt = $mysqli->prepare($sql); 
						$stmt->bind_param("s", $hashtag);
						$stmt->execute();
						$result = $stmt->get_result(); 
						$rows = $result->fetch_all(MYSQLI_ASSOC); 

						for($k=0; $k < count($rows); $k++) { 

							?>
							<div class="timeline-post">
							<div id="timeline-photo"><img class="timeline-photo" src="<?php echo $host . $db->clean($rows[$k]['photo'],'encode');?>" width="50"/></div>
							<a href=""><div class="timeline-post-text">
							<a class="profile-link" href="<?php echo $host . '@' .ucfirst($db->clean($rows[$k]['username'],'encode'));?>" class="profile-link">@<?php echo $db->clean($rows[$k]['username'],'encode');?></a>
							<span class="post-date"><?php echo date("M jS, Y", $rows[$k]['created']); ?></span>
							<br />
							<div class="twig"><?php echo $social->prepareHTML($rows[$k]['post']);?></div>
							</div></a>
							<div class="timeline-options">
						<span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/comment.png" onclick="Social.showcommentbar('timeline-post-bar-<?php echo $i;?>');" /></span>
						<span class="tl-opt-num"><?php echo $num_hearts;?></span><span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/heart.png"  onclick="Social.timelineoptions('<?php echo $host;?>','heart','<?php echo $db->intcast($_SESSION['uid']);?>','<?php echo $db->intcast($rows[$k]['id']);?>','<?php echo $db->clean($csrf,'encode');?>')" /></span>
						<span class="tl-opt-num"><?php echo $num_stars;?></span><span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/star.png" onclick="Social.timelineoptions('<?php echo $host;?>','star','<?php echo $db->intcast($_SESSION['uid']);?>','<?php echo $db->intcast($rows[$k]['id']);?>','<?php echo $db->clean($csrf,'encode');?>')"/></span>
						<!-- <span class="tl-opt-num"><?php echo $num_shares;?></span><span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/share.png" onclick="Social.timelineoptions('<?php echo $host;?>','share','<?php echo $db->intcast($_SESSION['uid']);?>','<?php echo $db->intcast($rows[$k]['id']);?>','<?php echo $db->clean($csrf,'encode');?>')"/></span> -->
							</div>
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