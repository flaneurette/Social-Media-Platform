<?php

		session_start();

		require("../resources/PHP/db.class.php");

		$db = new sql();

		if(!isset($_SESSION['token']) || empty($_SESSION['token']) ) {
			$csrf = $db->getToken();
			$_SESSION['token'] = $csrf;
			} else {
			$csrf = $db->clean($_SESSION['token'],'encode');
		}

		if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != '' && !empty($_SESSION['uid'])) {
			$uid = $db->intcast($_SESSION['uid']);
			} else {
			header("Location: ../");
			exit;
			$uid = $db->intcast($_SESSION['uid']);
		}
		
		// fetch profile section
		$userprofile = [];
		
		$stmt = $mysqli->prepare("SELECT id, name, username, accounttype, link, bio, location, email, photo, header, bodycolor, textcolor  FROM profile where id = ? LIMIT 1");
		$userid = $uid;
		$stmt->bind_param('i', $userid);
		$stmt->execute();
		$stmt->bind_result($id, $name, $username, $accounttype, $link, $bio, $location, $email, $photo, $header, $bodycolor, $textcolor);
		
		while($stmt->fetch()) {
			array_push($userprofile,$id);
			array_push($userprofile,$name);
			array_push($userprofile,$username);
			array_push($userprofile,$accounttype);
			array_push($userprofile,$link);
			array_push($userprofile,$bio);
			array_push($userprofile,$location);
			array_push($userprofile,$email);
			array_push($userprofile,$photo);
			array_push($userprofile,$header);
			array_push($userprofile,$bodycolor);
			array_push($userprofile,$textcolor);
		}		
		
		
		if(isset($_POST["bodycolor"])) {
			$bodycolor = $db->clean($_POST["bodycolor"],'encode');
			} else {
			$bodycolor = '#ffffff';
		}
		
		if(isset($_POST["textcolor"])) {
			$textcolor = $db->clean($_POST["textcolor"],'encode');
			} else {
			$textcolor = '#444444';
		}	
		
		$submit = true;
		if($_POST["csrf"]) { 
			if(isset($_POST["csrf"]) != '') {
				$csrf_post = $db->clean($_POST["csrf"],'encode');
				} else {
				$submit = false;
				$reason = "Error: Token is incorrect, are you a bot?";
			}
			if($_POST["csrf"] == $csrf) {
				$csrf_post = $db->clean($_POST["csrf"],'encode');
				$submit = true;
				} else {
				$submit = false;
				$reason = "Error: Token is incorrect, are you a bot?";
			}	
			
			if(isset($_POST["name"]) != '') {
				$name = $db->clean($_POST["name"],'encode');
				} else {
				$submit = false;
				$reason = "Error: Name cannot be empty";
			}
			
			if(isset($_POST["accounttype"]) != '') {
				$accounttype = $db->clean($_POST["accounttype"],'encode');
				} else {
				$submit = false;
				$reason = "Error: Accounttype cannot be empty";
			}	
			
			if(isset($_POST["link"]) != '') {
				$link = $db->clean($_POST["link"],'encode');
				} else {
				$submit = false;
				$reason = "Error: Link cannot be empty";
			}	
			
			if(isset($_POST["bio"]) != '') {
				$bio = $db->clean($_POST["bio"],'encode');
				} else {
				$submit = false;
				$reason = "Error: Bio cannot be empty";
			}
			
			if(isset($_POST["location"]) != '') {
				$location = $db->clean($_POST["location"],'encode');
				} else {
				$submit = false;
				$reason = "Error: Location cannot be empty";
			}
			
			if(isset($_POST["email"]) != '') {
				$email = $db->clean($_POST["email"],'encode');
				} else {
				$submit = false;
				$reason = "Error: E-mail cannot be empty";
			}	
			//echo "<pre>";
			//var_dump($_FILES);
			//echo "</pre>";
			//echo $_FILES['photo']['name'][0];
			
			if($_FILES['photo']['tmp_name'][0] !='') {
					
					if($_FILES['photo']['error'][0] != 1) {
						
						$destination = '../images/profile/';
						
						$seed  = time().mt_rand(0,0xffffff).mt_rand(0,0xffffff).mt_rand(0,0xffffff).mt_rand(0,0xffffff);
						$seed .= time().mt_rand(0,0xffffff).mt_rand(0,0xffffff).mt_rand(0,0xffffff).mt_rand(0,0xffffff);
						$seed .= microtime();
						$seed .= '-profile';
						
						$photo = substr(strtolower($_FILES['photo']['name'][0]),strlen($_FILES['photo']['name'][0])-3,3);
						
						switch($photo) {
							case 'jpg':
							$photo = '.jpg';
							break;
							case 'jpeg':
							$photo = '.jpeg';
							break;
							case 'png':
							$photo = '.png';
							break;
							case 'gif':
							$photo = '.gif';
							break;
						}
						
						@chmod($destination,0777);
						unlink('../'.$userprofile[8]);
						$upload = move_uploaded_file($_FILES['photo']['tmp_name'][0], strtolower($destination).$seed.$photo);
						if($upload) { 
							$photo = str_replace('../','',strtolower($destination).$seed.$photo);
							@chmod($destination,0755);
							} else {
							$submit = false;
							$reason = "Image failed to upload!";
						}
					} else {
						$submit = false;
						$reason = "Image cannot be empty";
					}
					
				} else {
				// $reason = "Error: This is not a link to a photo.";
			}

			if($_FILES['header']['tmp_name'][0] !='') {
					
					if($_FILES['header']['error'][0] != 1) {
						
						$destination = '../images/profile/';
						
						$seed  = time().mt_rand(0,0xffffff).mt_rand(0,0xffffff).mt_rand(0,0xffffff).mt_rand(0,0xffffff);
						$seed .= time().mt_rand(0,0xffffff).mt_rand(0,0xffffff).mt_rand(0,0xffffff).mt_rand(0,0xffffff);
						$seed .= microtime();
						$seed .= '-header';
						
						$photo = substr(strtolower($_FILES['header']['name'][0]),strlen($_FILES['header']['name'][0])-3,3);
						
						switch($photo) {
							case 'jpg':
							$header = '.jpg';
							break;
							case 'jpeg':
							$header = '.jpeg';
							break;
							case 'png':
							$header = '.png';
							break;
							case 'gif':
							$header = '.gif';
							break;
						}
						
						@chmod($destination,0777);
						unlink('../'.$userprofile[9]);
						$upload_header = move_uploaded_file($_FILES['header']['tmp_name'][0], strtolower($destination).$seed.$header);
						if($upload_header) { 
							$header = str_replace('../','',strtolower($destination).$seed.$header);
							@chmod($destination,0755);
							} else {
							$submit = false;
							$reason = "Header image failed to upload!";
						}
					} else {
						$submit = false;
						$reason = "Header image cannot be empty";
					}
					
				} else {
				// $reason = "Error: This is not a link to a photo.";
			}

			if(strlen($_FILES['photo']['name'][0]) > 12 && strlen($_FILES['header']['name'][0]) > 12) {
				if($submit == true) { 
					// insert new records into profile
					$stmt = $mysqli->prepare("UPDATE profile SET name = ?, accounttype = ?, link = ?, bio = ?, location = ?, email = ?, photo = ?, header = ?, bodycolor = ?, textcolor = ? WHERE id = ?");
					$userid = $uid;
					$stmt->bind_param("sssssssssss", $name, $accounttype, $link, $bio, $location, $email, $photo, $header, $bodycolor, $textcolor, $userid);
					$stmt->execute();
					$resultmessage = "Successfully updated both your profile, profile picture and header image!";
				}	
			} elseif(strlen($_FILES['photo']['name'][0]) > 12) {
				if($submit == true) { 
					// insert new records into profile
					$stmt = $mysqli->prepare("UPDATE profile SET name = ?, accounttype = ?, link = ?, bio = ?, location = ?, email = ?, photo = ?, bodycolor = ?, textcolor = ? WHERE id = ?");
					$userid = $uid;
					$stmt->bind_param("ssssssssss", $name, $accounttype, $link, $bio, $location, $email, $photo, $bodycolor, $textcolor, $userid);
					$stmt->execute();
					$resultmessage = "Successfully updated your profile and picture!";
				}	
			} elseif(strlen($_FILES['header']['name'][0]) > 12) {
				if($submit == true) { 
					// insert new records into profile
					$stmt = $mysqli->prepare("UPDATE profile SET name = ?, accounttype = ?, link = ?, bio = ?, location = ?, email = ?, header = ?, bodycolor = ?, textcolor = ? WHERE id = ?");
					$userid = $uid;
					$stmt->bind_param("ssssssssss", $name, $accounttype, $link, $bio, $location, $email, $header, $bodycolor, $textcolor, $userid);
					$stmt->execute();
					$resultmessage = "Successfully updated your profile and header image!";
				}	
			} else {
				if($submit == true) { 
					// update records into profile
					$stmt = $mysqli->prepare("UPDATE profile SET name = ?, accounttype = ?, link = ?, bio = ?, location = ?, email = ?, bodycolor = ?, textcolor = ? WHERE id = ?");
					$userid = $uid;
					$stmt->bind_param("sssssssss", $name, $accounttype, $link, $bio, $location, $email, $bodycolor, $textcolor, $userid);
					$stmt->execute();
					$resultmessage = "Success!";
				}		
			}
			
			$stmt->close();
		}
		
		// fetch profile section
		$userprofile = [];
		
		$stmt = $mysqli->prepare("SELECT id, name, username, accounttype, link, bio, location, email, photo, header, bodycolor, textcolor  FROM profile where id = ? LIMIT 1");
		$userid = $uid;
		$stmt->bind_param('i', $userid);
		$stmt->execute();
		$stmt->bind_result($id, $name, $username, $accounttype, $link, $bio, $location, $email, $photo, $header, $bodycolor, $textcolor);
		
		while($stmt->fetch()) {
			array_push($userprofile,$id);
			array_push($userprofile,$name);
			array_push($userprofile,$username);
			array_push($userprofile,$accounttype);
			array_push($userprofile,$link);
			array_push($userprofile,$bio);
			array_push($userprofile,$location);
			array_push($userprofile,$email);
			array_push($userprofile,$photo);
			array_push($userprofile,$header);
			array_push($userprofile,$bodycolor);
			array_push($userprofile,$textcolor);
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
						</ul>	
					</div>
					
					<div id="nav-center">
						<div id="timeline-username">Settings</div> 
						
						<div id="timeline-header"><img src="../images/settings.png"></div>
							
							<div><div>
							
						</div>
							
						<div class="settings">
								
						Change your settings and profile. <br /><br />
						<?php
						if($submit == false) {
							echo "<div id='settings-error'>" .  $reason . "</div>";
						} elseif(isset($resultmessage)) {
							echo "<div id='settings-success'>" .  $resultmessage . "</div>";
						} else {}
						?>
						<hr />
						<div class="settings-form">
							<form name="post" action="" method="POST" autocomplete="off" data-lpignore="true" enctype="multipart/form-data">
							<input type="hidden" name="csrf" value="<?php echo $db->clean($csrf,'encode');?>" />
							
							<strong>Account information</strong>
							
							<div class="settings-form-item-account"> 
							
								<div class="settings-form-item-left">
									<span class="form-sub">E-mail: </span> <input type="text" name="email"  value="<?php echo $db->clean($userprofile[7],'encode');?>"/>
									<span class="form-sub">Name: </span> <input type="text" name="name" value="<?php echo $db->clean($userprofile[1],'encode');?>" />
									<span class="form-sub">Account type: </span> <input type="text" name="accounttype" value="<?php echo $db->clean($userprofile[3],'encode');?>" />
								</div>
								<div class="settings-form-item-left">
									<span class="form-sub">Link: </span> <input type="text" name="link" value="<?php echo $db->clean($userprofile[4],'encode');?>" />
									<span class="form-sub">Bio: </span> <input type="text" name="bio" value="<?php echo $db->clean($userprofile[5],'encode');?>" />
									<span class="form-sub">Location: </span> <input type="text" name="location" value="<?php echo $db->clean($userprofile[6],'encode');?>" />
								</div>	
							</div>
							
							<strong>Pictures &amp; Photos</strong>
							
							<div class="settings-form-item-pictures"> 
								<div class="settings-form-item-left">
								<span class="form-sub">Profile Picture: </span> 
								<div id="timeline-photo"><img class="timeline-photo" src="<?php echo $host . $db->clean($userprofile[8],'encode');?>" width="50"/></div> <input type="file" name="photo[]"  title="Square images works best!" alt="Square images works best!">
								<span class="form-sub">Header (615 x 168): </span> 
								<input type="file" name="header[]"  title="615px width and 168px height works best!" alt="615px width and 168px height works best!">
								</div>
							</div>
							
							<?php
							
							if(isset($userprofile[10])) { 
								$bodycolor = $db->clean($userprofile[10],'encode');
								} else {
								$bodycolor = '#ffffff';
							}
							
							if(isset($userprofile[11])) { 
								$textcolor = $db->clean($userprofile[11],'encode');
								} else {
								$textcolor = '#444444';
							}
							?>
							<strong>Color scheme</strong>
							
							<div class="settings-form-item-colors"> 
								<div class="settings-form-item-left">
								<label for="bodycolor">Choose a background color scheme:</label> <input type="color" id="bodycolor" name="bodycolor" value="<?php echo $bodycolor;?>" style="padding:0px!important;">
								<label for="textcolor">Choose a text color:</label> <input type="color" id="textcolor" name="textcolor" value="<?php echo $textcolor;?>" style="padding:0px!important;">
								</div>
							</div>

							<strong>Saving</strong>
							
							
								<div class="settings-form-submit">
									<input type="submit" name="post" value="Update" />
								</div>
							</form>
						</div>
						<br /> 
					</div>
		</div>

		<?php
		include("../resources/PHP/footer.php");
		?>
	</body>
</html>