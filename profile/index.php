<?php

	if($_SERVER["REQUEST_URI"] == "/profile/?mobile=true") {
		header("location: ../mobile/");
		exit;
	}

	session_start();

	require("../resources/PHP/db.class.php");
	require("../resources/PHP/social.class.php");

	$db 	= new sql();
	$social = new social();
	
	// login check
	if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != '' && $_SESSION['uid'] !='') {
		$statuslogged = "Log out";
		$statusloggedurl = "../logout/";
		$uid = $db->intcast($_SESSION['uid']);
		} else {
		$statuslogged = "Log in";
		$statusloggedurl = "../login/";
		$uid = $db->intcast($_SESSION['uid']);
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

	if($uid < 1) {
		header("Location: ../");
		exit;
	}

	// photo editor
	if(isset($_POST['photo-editor-result'])) {
		
		if($_SESSION['token'] == $db->clean($_POST['csrf'],'encode')) {
			
			// update database with css.
			$stmt = $mysqli->prepare("UPDATE profile SET headerfilter = ? WHERE id = ?");
			$headerfilter = $db->clean($_POST['photo-editor-result'],'encode');
			$userid = $uid;
			$stmt->bind_param("si", $headerfilter, $userid);
			$stmt->execute();
		}
	}
	
	// get database information on profile.
	$timeline 	= $db->query("SELECT * FROM timeline WHERE uid = ".$db->intcast($uid)." ORDER BY tid DESC");

	$userprofile = [];
	$profile = [];		
			
	$stmt = $mysqli->prepare("SELECT * FROM profile where id = ? LIMIT 1");

	$params = array("s",$uid);

	foreach($params as $key => $value) $userprofile[$key] = &$params[$key];
	call_user_func_array(array($stmt, 'bind_param'), $userprofile);
	$stmt->execute();
	$query = $stmt->get_result();

	while($row = $query->fetch_array(MYSQLI_ASSOC)) {
		$profile[] = $row;
	}

	$stmt->close();

	$stats_followers = $db->query("SELECT COUNT(*) AS followers FROM friends where uid = '".$db->intcast($uid)."'");
	$stats_following = $db->query("SELECT COUNT(*) AS following FROM friends where fid = '".$db->intcast($uid)."'");

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
	
	if(isset($profile[0]["bodycolor"]) != NULL && isset($profile[0]["textcolor"]) != NULL) { 

		if(strstr($profile[0]["bodycolor"],'#') && strstr($profile[0]["textcolor"],'#')) {
			if($profile[0]["background"] !='') {
				echo $social->css($profile[0]["bodycolor"],$profile[0]["textcolor"],$profile[0]["background"]);
				} else {
				echo $social->css($profile[0]["bodycolor"],$profile[0]["textcolor"],false);
			}			
		}
	}
	?>
	
		<form name="photo-editor" method="post" action="">
		<div id="photo-editor">
		
		<div id="photo-editor-main">
			<div id="photo-editor-close" onclick="Social.hide('photo-editor');"></div>
			<div id="photo-editor-photo"><img id="image-edit" src="<?php echo $db->clean($host.$profile[0]["header"],'encode');?>" />
			<div id="photo-editor-functions">
			<input type="button" value="Reset" class="photo-editor-button" onclick="filter.resetFilters('image-edit');" />
			<input type="submit" value="Save"  class="photo-editor-button"/>
			</div>
			</div>
			<div id="photo-editor-controls"></div>
			<input type="hidden" name="photo-editor-result" id="result"/>
			<input type="hidden" name="csrf" value="<?php echo $csrf ;?>" />
		</div>
		</div>
		</form>
		
		<div id="wrapper">
					<div id="nav-left">
						<ul>
						<li class="selected"><a href="<?php echo $host;?>profile/">Profile</a></li>
						<li><a href="<?php echo $host;?>timeline/">Timeline</a></li>
						<li><a href="<?php echo $host;?>mentions/">Mentions <?php if($count_mentions >=1) { echo "<span class=\"messages-alert\">" . $count_mentions ."</span>"; } ?></span></a></li>
						<li><a href="<?php echo $host;?>settings/">Settings</a></li>
						<li class="mobile-block"><a href="<?php echo $statusloggedurl;?>"><?php echo $statuslogged;?></a></li>
						<li><form name="search" action="<?php echo $host;?>search/" method="POST"><input type="hidden" name="csrf" value="<?php echo $db->clean($csrf,'encode');?>" /><input type="text" id="nav-search" name="search" size="30" value="search..." /></form></li>
						</ul>	
						
						<?php if(isset($_SESSION['loggedin'])) { ?>
						<div id="friends"><?php include("../resources/PHP/friends.php"); ?></div><div id="who-to-follow"><?php include("../resources/PHP/whotofollow.php"); ?></div>
						<?php } else { ?> <div id="who-to-follow"> <?php include("../resources/PHP/promobarindex.php"); }  ?> </div>
					
					</div>
					<div id="nav-center">
						<div id="timeline-username"><?php echo '@'.$db->clean(ucfirst($profile[0]["username"]),'encode');?></div> 
						<span id="bio-link"><?php echo  $social->prepareLINK($profile[0]["link"],'encode');?></span>
						<span id="times-count"> <img src="../resources/images/logo-18x18.png" alt="<?php echo $numberoftimelines;?> twigs" title="<?php echo $numberoftimelines;?> twigs" id="twig-count" /></span>
						
						<div id="timeline-header" style="<?php if(isset($profile[0]["header"])) { echo "background:url('".$db->clean($host.$profile[0]["header"],'encode')."');"; } ?>background-size: cover;background-repeat:no-repeat;background-color: #fff!important; <?php echo $profile[0]["headerfilter"];?>">
						<?php 
						if($profile[0]["id"] == $_SESSION['uid']) { 
						?>
						<div id="photo-edit-icon-header" onclick="Social.show('photo-editor');"></div>
						<?php } ?>
						</div>
							<div id="timeline-profile-picture-container"><div id="timeline-profile-picture" style="background:url('<?php echo $host . $db->clean($profile[0]["photo"],'encode');?>') !important; background-size: cover!important;background-color: #fff!important; <?php echo $profile[0]["photofilter"];?>"></div>
							</div>
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
							} else { ?>
							<span id="post-timeline-link"><a href="#"  onclick="Social.dom('float','display','block')">POST</a></span>
							<?php 
							}
							?>
							</div>
							

							</div>
						<div id="timeline">
						<?php
						
						$times = count($timeline);
						
						if($times > 250) {
							$times = 250;
						} 
						
						for($i=0; $i < $times; $i++) {
							
						
							$stats = $db->query("SELECT COUNT(likes),COUNT(starred),COUNT(views),COUNT(shares) FROM stats WHERE uid = ".$db->intcast($timeline[$i]['uid'])." and pid = ".$db->intcast($timeline[$i]['tid']));

							$num_views  = $db->intcast($stats[0]["COUNT(views)"]);
							$num_shares = $db->intcast($stats[0]["COUNT(shares)"]);
							$num_stars  = $db->intcast($stats[0]["COUNT(starred"]);
							$num_hearts = $db->intcast($stats[0]["COUNT(likes)"]);
									
							if($num_views  == '0') { $num_views  = ''; }	
							if($num_shares == '0') { $num_shares = ''; }
							if($num_stars  == '0') { $num_stars  = ''; }	
							if($num_hearts == '0') { $num_hearts = ''; }
							
							if(isset($timeline[$i]['sharedname'])) {
								$sharedvia = '&nbsp;shared via: <a class="profile-link-shared" href="'.$host . '@' .ucfirst($db->clean($timeline[$i]['sharedname'],'encode')).'">@'. $db->clean($timeline[$i]['sharedname'],'encode').'</a>';
								} else {
								$sharedvia = '';
							}

						?>
						<div class="timeline-post">
						<div id="image-container"><div id="image" style="background:url('<?php echo $host . $db->clean($profile[0]["photo"],'encode');?>') !important; background-size: cover!important; background-color: #fff!important;"></div></div>
						<a href="status/<?php echo $db->intcast($timeline[$i]['id']);?>" id="status-id"><div class="timeline-post-text" onclick="document.location='<?php echo $host . '@' .ucfirst($db->clean($profile[0]["username"],'encode'));?>/<?php echo $db->intcast($timeline[$i]['uid']);?>/status/<?php echo $db->intcast($timeline[$i]['tid']);?>/';">
						<a class="profile-link" href="<?php echo $host . '@' .ucfirst($db->clean($profile[0]["username"],'encode'));?>" class="profile-link">@<?php echo ucfirst($db->clean($profile[0]["username"],'encode'));?></a> <?php echo $sharedvia;?>
						<span class="post-date"><?php echo date("M jS, Y", $db->intcast($timeline[$i]['created'])); ?></span>
							<p class="twig">
							<?php 
							echo $social->prepareHTML($timeline[$i]['post']);
							echo $social->prepareMixedMedia($timeline[$i]['mixedmedia']); 
							echo PHP_EOL;
							?>
							</p>
						</div></a>
						<div class="timeline-options">
						<span><img class="timeline-icon" src="/resources/images/icons/comment.png" onclick="Social.createCommentForm('timeline-post-bar-<?php echo $i;?>','../postback/','<?php echo $db->clean($csrf,'encode');?>','<?php echo $db->intcast($timeline[$i]['tid']);?>','<?php echo $db->intcast($timeline[$i]['uid']);?>','<?php echo $db->intcast($profile[0]['id']);?>','<?php echo $db->clean($profile[0]["username"],'encode');?>');" /></span>
						<span class="tl-opt-num" id="heart<?php echo $i;?>-num"><?php echo $num_hearts;?></span><span><img class="timeline-icon" id="heart<?php echo $i;?>" src="/resources/images/icons/heart.png"  onclick="Social.timelineoptions('<?php echo $host;?>','heart','<?php echo $db->intcast($timeline[$i]['uid']);?>','<?php echo $db->intcast($timeline[$i]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>','heart<?php echo $i;?>')" /></span>
						<span class="tl-opt-num" id="star<?php echo $i;?>-num"><?php echo $num_stars;?></span><span><img class="timeline-icon" id="star<?php echo $i;?>" src="/resources/images/icons/star.png" onclick="Social.timelineoptions('<?php echo $host;?>','star','<?php echo $db->intcast($timeline[$i]['uid']);?>','<?php echo $db->intcast($timeline[$i]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>','star<?php echo $i;?>')"/></span>
						<span class="tl-opt-num"><?php echo $num_shares;?></span><span><img class="timeline-icon" src="/resources/images/icons/share.png" onclick="Social.timelineoptions('<?php echo $host;?>','share','<?php echo $db->intcast($timeline[$i]['uid']);?>','<?php echo $db->intcast($timeline[$i]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>')"/></span>
						<?php 
						if($profile[0]["id"] == $_SESSION['uid']) { 
						?>
						<span class="options-opt-icon"><img class="options-opt" onclick="Social.showTimelineOptions('timeline-options-box-<?php echo $i;?>');" src="/resources/images/icons/opt.png"/></span>
							<div id="timeline-options-box-<?php echo $i;?>" class="timeline-options-box">
								<img src="/resources/images/icons/close.png" class="timeline-options-box-close" onclick="Social.closeTimelineOptions('timeline-options-box-<?php echo $i;?>');"/>
								<ul>
								<li onclick="Social.timelineoptions('<?php echo $host;?>','delete','<?php echo $db->intcast($timeline[$i]['uid']);?>','<?php echo $db->intcast($timeline[$i]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>')">delete twig</li>
								</ul>
							</div>
						<?php } ?>
						</div>
						</div>
						<div id="timeline-post-bar-<?php echo $i;?>" class="profile-reply">
							
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
		<script src="../resources/js/filter.js" type="text/javascript"></script>

		<script type="text/javascript">
		
		var filter = new filters; 
			
			let filterlist = [
				'Brightness:0:200:100',
				'Contrast:0:200:100',
				'Grayscale:0:100:0',
				'Hue:0:360:0',
				'Saturate:0:10:0',
				'Sepia:0:100:0'
			];

			let presets = [
			
				['Professional',['Grayscale:86','Contrast:200','Brightness:97','Saturate:2','Sepia:31']],
				['Blackwhite',['Grayscale:100','Contrast:181']],
				['Worn',['Grayscale:10','Sepia:90','Contrast:189','Brightness:102']],
				['Fuchsia',['Hue:299','Saturate:1','Contrast:194','Brightness:130']],
				['Ink',['Brightness:74','Contrast:200','Grayscale:100']],
				['Moonlight',['Saturate:1','Sepia:14','Hue:175','Grayscale:77','Brightness:61','Contrast:176']],
				['Xray',['Saturate:1','Hue:18','Brightness:71','Sepia:51','Invert:100','Contrast:200']],
				['Flame',['Sepia:100','Saturate:7','Contrast:174']],
				
			];
			
			filter.createFilters('photo-editor-controls', 'image-edit', filterlist, presets, 'result');
		
		</script>

		<div id="progress-bar-upload" style="display:none;">Uploading, please wait a moment...
			<div id="progress-bars"></div>
		</div>
	</body>
</html>
<?php
$db->close();
?>