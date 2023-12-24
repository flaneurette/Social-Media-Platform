<?php

	session_start();

	require("../resources/PHP/db.class.php");
	require("../resources/PHP/social.class.php");

	$db 	= new sql();
	$social = new social();

	// login check
	if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != '' && $_REQUEST['uid'] !='' && $_REQUEST['status'] !='') {
		$statuslogged = "Log out";
		$statusloggedurl = "../logout/";
		$uid = $db->intcast($_REQUEST['uid']);
		$statusid = $db->intcast($_REQUEST['status']);
		} else {
		$statuslogged = "Log in";
		$statusloggedurl = "../login/";
		header("Location: ../");
		exit;
	}

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

	// get and set a proper token for our instance.
	if(!isset($_SESSION['token']) || empty($_SESSION['token']) ) {
		$csrf = $db->getToken();
		$_SESSION['token'] = $csrf;
	}  else {
		$csrf = $db->clean($_SESSION['token'],'encode');
	}

	$timeline 	= $db->select('timeline','*','tid',$statusid);

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

	$stats_followers = $db->query("SELECT COUNT(*) AS followers FROM friends where uid = '".$db->intcast($uid)."'");
	$stats_following = $db->query("SELECT COUNT(*) AS following FROM friends where fid = '".$db->intcast($uid)."'");

	// count number of times from timeline
	$numberoftimelines = $db->intcast(count($timeline));

	if($numberoftimelines < 1) {
		$numberoftimelines = 0;
	}

?>

<!DOCTYPE html>
<html>
	<head>
	<?php
	include("../resources/PHP/header.php");
	
	if(isset($profile[0]["bodycolor"]) != NULL && isset($profile[0]["textcolor"]) != NULL) { 

		if(strstr($profile[0]["bodycolor"],'#') && strstr($profile[0]["textcolor"],'#')) { 
			echo $social->css($profile[0]["bodycolor"],$profile[0]["textcolor"]);	
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
						<li><a href="../../../<?php echo $statusloggedurl;?>"><?php echo $statuslogged;?></a></li>
						<li><form name="search" action="<?php echo $host;?>search/" method="POST"><input type="hidden" name="csrf" value="<?php echo $db->clean($csrf,'encode');?>" /><input type="text" id="nav-search" name="search" size="30" value="search..." /></form></li>
						</ul>	
						<div id="friends"><?php include("../resources/PHP/friends.php"); ?></div><div id="who-to-follow"><?php include("../resources/PHP/whotofollow.php"); ?></div>
					</div>
					<div id="nav-center">
						<div id="timeline-username"><?php echo '@'.$db->clean(ucfirst($profile[0]["username"]),'encode');?></div> 
						<span id="bio-link"><?php echo  $social->prepareLINK($profile[0]["link"],'encode');?></span>
						<span id="times-count"><img src="../../../../resources/images/logo-18x18.png" alt="<?php echo $numberoftimelines;?> twigs" title="<?php echo $numberoftimelines;?> twigs" id="twig-count" /></span>
						
						<div id="timeline-header"  style="<?php if(isset($profile[0]["header"])) { echo "background:url('".$db->clean($host.$profile[0]["header"],'encode')."');"; } ?>background-size: cover;background-repeat:no-repeat;"></div>
							<div id="timeline-profile-picture-container"><div id="timeline-profile-picture" style="background:url('<?php echo $host . $db->clean($profile[0]["photo"],'encode');?>') !important; background-size: cover!important;"></div></div>
							<div id="timeline-profile">
							<div>
							<span id="bio-link-text"><?php echo $db->clean($profile[0]["bio"],'encode');?></span>
							<span id="follow-stats">
							<span><?php echo $db->clean($stats_followers[0]['followers'],'encode');?> Following</span>
							<span><?php echo $db->clean($stats_following[0]['following'],'encode');?> Followers</span>
							</span>
							<?php 
							if($followbutton == true) { 
							
								if(isset($_SESSION['loggedin'])) { 
								
									$selectfriend = $db->query("SELECT * FROM friends WHERE uid = '".$db->intcast($uid)."' and fid = '".$db->intcast($profile[0]["id"])."' LIMIT 1");
									$countfriend = count($selectfriend);
									
									if($countfriend >= 1) {
										 echo '<span id="post-timeline-link"><a href="#"  onclick="Social.unfollow(\''.$db->intcast($_SESSION['uid']).'\',\''.$db->intcast($profile[0]["id"]).'\',\''.$db->clean($csrf,'encode').'\')">UNFOLLOW</a></span>';
										} else {
										echo '<span id="post-timeline-link"><a href="#"  onclick="Social.follow(\''.$db->intcast($_SESSION['uid']).'\',\''.$db->intcast($profile[0]["id"]).'\',\''.$db->clean($csrf,'encode').'\')">FOLLOW</a></span>';
									}
								}
							} ?>
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
							
							
							if(strlen($timeline[$i]['sharedname']) >=1) {
								$sharedvia = '&nbsp;shared via: <a class="profile-link-shared" href="'.$host . '@' .ucfirst($db->clean($timeline[$i]['sharedname'],'encode')).'">@'. $db->clean($timeline[$i]['sharedname'],'encode').'</a>';
								} else {
								$sharedvia = '';
							}
						?>
						<div class="timeline-post">
						<div id="image-container"><div id="image" style="background:url('<?php echo $host . $db->clean($profile[0]["photo"],'encode');?>') !important; background-size: cover!important;"></div></div>
						<a href="status/<?php echo $db->intcast($timeline[$i]['id']);?>" id="status-id"><div class="timeline-post-text">
						<a class="profile-link" href="<?php echo $host . '@' .ucfirst($db->clean($profile[0]["username"],'encode'));?>" class="profile-link">@<?php echo ucfirst($db->clean($profile[0]["username"],'encode'));?></a> <?php echo $sharedvia;?>
						<span class="post-date"><?php echo date("M jS, Y", $db->intcast($timeline[$i]['created'])); ?></span>
						<br />
						<div class="twig"><?php echo $social->prepareHTML($timeline[$i]['post']);?> <?php echo $social->prepareMixedMedia($timeline[$i]['mixedmedia']); ?></div>
						
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
							<input type="hidden" name="at-user" value="@<?php echo $db->clean($profile[0]["username"],'encode');?>" />
							<textarea name="post-message" rows="3" draggable="false" id="timelinepost-textarea" placeholder="Reply to @<?php echo $db->clean($profile[0]["username"],'encode');?>" onkeydown="Social.timelinePost('timelinepost-textarea','charcounter');"></textarea>
							<span id="charcount"><span id="charcounter">255</span> characters left.</span>
							<input type="submit" name="post" value="Post back" style="float:none!important;"/>
							</form>
							<div class="timeline-options"></div>
						</div>
						<?php
						
						$tid = $db->intcast($timeline[$i]['tid']);

						}

						// select comments from users on post.
						$friendscomments = $db->query("SELECT * FROM timeline WHERE cid = ".$db->intcast($tid));
						$count = count($friendscomments);
						
						if($count >=1) { 
						
							for($c=0; $c < $count; $c++) {
							 
							$profile = $db->query("SELECT * FROM profile WHERE id = ".$db->intcast($friendscomments[$c]['uid']));
							
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
								<span class="post-date"><?php echo date("M jS, Y", $db->intcast($friendscomments[$c]['created'])); ?></span>
								<br />
							
								<div class="twig"><?php echo $social->prepareHTML($friendscomments[$c]['post']);?></div>
							
							</div>
							<div class="timeline-options">
								<span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/comment.png" onclick="Social.showcommentbar('timeline-post-bar-<?php echo $i;?>');" /></span>
								<span class="tl-opt-num"><?php echo $num_hearts;?></span><span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/heart.png"  onclick="Social.timelineoptions('<?php echo $host;?>','heart','<?php echo $db->intcast($timelines[$k][$i]['uid']);?>','<?php echo $db->intcast($timeline_pre[0]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>')" /></span>
								<span class="tl-opt-num"><?php echo $num_stars;?></span><span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/star.png" onclick="Social.timelineoptions('<?php echo $host;?>','star','<?php echo $db->intcast($timelines[$k][$i]['uid']);?>','<?php echo $db->intcast($timeline_pre[0]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>')"/></span>
								<!-- <span class="tl-opt-num"><?php echo $num_shares;?></span><span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/share.png" onclick="Social.timelineoptions('<?php echo $host;?>','share','<?php echo $db->intcast($timelines[$k][$i]['uid']);?>','<?php echo $db->intcast($timeline_pre[0]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>')"/></span> -->
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