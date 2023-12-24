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

?>

<!DOCTYPE html>
<html>
	<head>
	<?php
	include("../resources/PHP/header.php");

	$timelines = [];
	
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
						<li class="selected"><a href="<?php echo $host;?>timeline/">Timeline</a></li>
						<li><a href="<?php echo $host;?>mentions/">Mentions <?php if($count_mentions >=1) { echo "<span class=\"messages-alert\">" . $count_mentions ."</span>"; } ?></span></a></li>
						<li><a href="<?php echo $host;?>settings/">Settings</a></li>
						<li class="mobile-block"><a href="<?php echo $statusloggedurl;?>"><?php echo $statuslogged;?></a></li>
						<li><form name="search" action="<?php echo $host;?>search/" method="POST"><input type="hidden" name="csrf" value="<?php echo $db->clean($csrf,'encode');?>" /><input type="text" id="nav-search" name="search" size="30" value="search..." /></form></li>
						</ul>	
						<div id="friends"><?php include("../resources/PHP/friends.php"); ?></div><div id="who-to-follow"><?php include("../resources/PHP/whotofollow.php"); ?></div>
					</div>
					
					<div id="nav-center">
						<div id="timeline-username">Timeline</div>
						<div id="timeline-date"><?php echo date("F jS Y");?><span id="icon-chess" onclick="Social.calendar();"></span></div>
						<div id="timeline">
						<div id="timeline-post-self">
							<form name="post" action="../post/" method="POST" onsubmit="return Social.checkTwig('timelinepost-textarea');" autocomplete="off" data-lpignore="true" enctype="multipart/form-data">
							<input type="hidden" name="<?php echo ini_get("session.upload_progress.name"); ?>" value="123" />
							<input type="hidden" name="csrf" value="<?php echo $db->clean($csrf,'encode');?>" />
							<textarea name="post-message" id="post-message"></textarea>
							<div id="style-bar">
								<input type="button" onclick="Social.styling('bold','timelinepost-textarea');return false;" value="Bold">
								<input type="button" onclick="Social.styling('emphasize','timelinepost-textarea');return false;" value="Emphasize">
								<input type="button" onclick="Social.styling('blockquote','timelinepost-textarea');return false;" value="Quote">
								<input type="button" onclick="Social.styling('code','timelinepost-textarea');return false;" value="Code">
							</div>
							<div name="text" contentEditable="true" name="post-message" placeholder="Write..." id="timelinepost-textarea" oninput="Social.timelinePost('post-message','charcounter-init');"></div>
							<div id="progress-charcount"></div>
							<div id="charcount"><span id="charcounter-init">255</span> chars</div>
							<div id="emoji"></div>
							<!-- <span id="spellcheck">Enable spellcheck: <input type="checkbox" value="" /></span> -->
							 <label for="mixedmedia"><img id="mixedmedia-image" src="../resources/images/icons/file.png" alt=".gif, .jpg, .png, .ogg or .mp3. Maximum of 20MB." title=".gif, .jpg, .png, .ogg or .mp3. Maximum of 20MB.">
							<input type="file" id="mixedmedia" name="mixedmedia[]" onchange="Social.fileUploads('mixedmedia-image', this.value,'timeline-post-button');" accept="image/png, image/jpeg, image/gif, image/jpg, audio/mp3, audio/ogg" style="display:none;"/>
							</label>  
							<img src="../resources/images/icons/emoji.png" width="30" id="emoji-button" title="Choose emoji" alt="Choose emoji" onclick="Emojis.emoticons('timelinepost-textarea')"/> <img src="../resources/images/icons/icon-resize.png" width="30" id="icon-resize" title="Resize" alt="Resize" onclick="Social.resizeArea('timelinepost-textarea',450);Social.show('style-bar');"/> 
							<div id="styling" onclick="Social.show('style-guide');"><em>Styling</em></div>
							<div id="style-guide">
								<input type="button" onclick="Social.styling('bold','timelinepost-textarea');return false;" value="Bold">
								<input type="button" onclick="Social.styling('emphasize','timelinepost-textarea');return false;" value="Emphasize">
								<input type="button" onclick="Social.styling('blockquote','timelinepost-textarea');return false;" value="Quote">
								<input type="button" onclick="Social.styling('code','timelinepost-textarea');return false;" value="Code">
							</div>
							<input type="submit" name="post" id="timeline-post-button" value="Post"/>
							</form>
						</div>
						<br />
						<?php
						
						if($countown >= 1) {
							for($j=0;$j<$countown;$j++) {
								$timeline = $db->query("SELECT * FROM timeline,profile WHERE profile.active = '1' AND timeline.uid = '".$db->intcast($uid)."' AND profile.id = '".$db->intcast($selectown[$j]['id'])."' ORDER BY timeline.tid  DESC LIMIT 255");
									for($m=0;$m<count($timeline);$m++) {
												array_push($timelines,$timeline[$m]);
									}
							}
						} 
						
						// select friends timelines
						
						$selectfriends = $db->query("SELECT * FROM friends WHERE uid = '".$db->intcast($uid)."'");
						$countfriends = count($selectfriends);
						
						if($countfriends  >=1) {

							for($j=0;$j<$countfriends;$j++) {
								
							   $timeline_friends     = $db->query("SELECT id,tid,uid,post,photo,mixedmedia,username,created,sharedname FROM timeline,profile WHERE profile.active = '1' AND timeline.uid = '".$db->intcast($selectfriends[$j]['fid'])."' AND profile.id = '".$db->intcast($selectfriends[$j]['fid'])."' ORDER BY timeline.tid  DESC LIMIT 255");
								if(count($timeline_friends) >=1) { 
									if(!empty($timeline_friends)){ 
											for($l=0;$l<count($timeline_friends);$l++) {
												array_push($timelines,$timeline_friends[$l]);
											}
									}
								}
							}
						} 	
				
						array_multisort(
							array_column($timelines, 'tid'),
							$timelines
						);
						
						$timelines = array_reverse($timelines); 
						
						for($k=0; $k < count($timelines); $k++) { 
				
									$stats = $db->query("SELECT COUNT(likes),COUNT(starred),COUNT(views),COUNT(shares) FROM stats WHERE uid = ".$db->intcast($timelines[$k]['uid'])." and pid = ".$db->intcast($timelines[$k]['tid']));
									$num_views  = $db->intcast($stats[0]["COUNT(views)"]);
									$num_shares = $db->intcast($stats[0]["COUNT(shares)"]);
									$num_stars  = $db->intcast($stats[0]["COUNT(starred"]);
									$num_hearts = $db->intcast($stats[0]["COUNT(likes)"]);
									
									if($num_views  == '0') { $num_views  = ''; }	
									if($num_shares == '0') { $num_shares = ''; }
									if($num_stars  == '0') { $num_stars  = ''; }	
									if($num_hearts == '0') { $num_hearts = ''; }

									if(isset($timelines[$k]['sharedname'])) {
										$sharedvia = '&nbsp;shared via: <a class="profile-link-shared" href="'.$host . '@' .ucfirst($db->clean($timelines[$k]['sharedname'],'encode')).'">@'. $db->clean($timelines[$k]['sharedname'],'encode').'</a>';
										} else {
										$sharedvia = '';
									}
							?>
							<div class="timeline-post">
							<div id="image-container"><div id="image" style="background:url('<?php echo $host . $db->clean($timelines[$k]["photo"],'encode');?>') !important; background-size: cover!important;"></div></div>
							<div class="timeline-post-text">
							<a class="profile-link" href="<?php echo $host . '@' .ucfirst($db->clean($timelines[$k]['username'],'encode'));?>" class="profile-link">@<?php echo $db->clean($timelines[$k]['username'],'encode');?></a> <?php echo $sharedvia;?>
							<span class="post-date"><?php echo date("M jS, Y", $db->clean($timelines[$k]['created'],'encode')); ?></span>
								<p class="twig">
									<?php 
									echo $social->prepareHTML($timelines[$k]['post']);
									echo $social->prepareMixedMedia($timelines[$k]['mixedmedia']);
									echo PHP_EOL;									
									?>
								</p>
							</div>
								<div class="timeline-options">
									<span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/comment.png" onclick="Social.createCommentForm('timeline-post-bar-<?php echo $k;?>','../postback/','<?php echo $db->clean($csrf,'encode');?>','<?php echo $db->intcast($timelines[$k]['tid']);?>','<?php echo $db->intcast($timelines[$k]['uid']);?>', '<?php echo $db->intcast($timelines[$k]['id']);?>', '<?php echo $db->clean($timelines[$k]['username'],'encode');?>');" /></span>
									<span class="tl-opt-num" id="heart<?php echo $k;?>-num"><?php echo $num_hearts;?></span><span><img class="timeline-icon" id="heart<?php echo $k;?>" src="<?php echo $host ;?>resources/images/icons/heart.png"  onclick="Social.timelineoptions('<?php echo $host;?>','heart','<?php echo $db->intcast($timelines[$k]['uid']);?>','<?php echo $db->intcast($timelines[$k]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>','heart<?php echo $k;?>')" /></span>
									<span class="tl-opt-num" id="star<?php echo $k;?>-num"><?php echo $num_stars;?></span><span><img class="timeline-icon" id="star<?php echo $k;?>" src="<?php echo $host ;?>resources/images/icons/star.png" onclick="Social.timelineoptions('<?php echo $host;?>','star','<?php echo $db->intcast($timelines[$k]['uid']);?>','<?php echo $db->intcast($timelines[$k]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>','star<?php echo $k;?>')"/></span>
									<span class="tl-opt-num"><?php echo $num_shares;?></span><span><img class="timeline-icon" src="<?php echo $host ;?>resources/images/icons/share.png" onclick="Social.timelineoptions('<?php echo $host;?>','share','<?php echo $db->intcast($timelines[$k]['uid']);?>','<?php echo $db->intcast($timelines[$k]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>')"/></span>
								<?php 
								if($timelines[$k]["id"] == $_SESSION['uid']) { 
								?>
								
								<span class="options-opt-icon"><img class="options-opt-timeline" onclick="Social.showTimelineOptions('timeline-options-box-<?php echo $k;?>');" src="<?php echo $host ;?>resources/images/icons/opt.png"/></span>
									<div id="timeline-options-box-<?php echo $k;?>" class="timeline-options-box">
										<img src="<?php echo $host ;?>resources/images/icons/close.png" class="timeline-options-box-close" onclick="Social.closeTimelineOptions('timeline-options-box-<?php echo $k;?>');"/>
										<ul>
										<li onclick="Social.timelineoptions('<?php echo $host;?>','delete','<?php echo $db->intcast($timelines[$k]['uid']);?>','<?php echo $db->intcast($timelines[$k]['tid']);?>','<?php echo $db->clean($csrf,'encode');?>')">delete twig</li>
										</ul>
									</div>
								<?php } ?>
								
								</div>
							</div>
							<div id="timeline-post-bar-<?php echo $k;?>" class="profile-reply">
								
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
		<div id="progress-bar-upload" style="display:none;">Uploading, please wait a moment...
			<div id="progress-bars"></div>
		</div>
	</body>
</html>
<?php
$db->close();
?>