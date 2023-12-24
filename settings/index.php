<?php

		session_start();

		require("../resources/PHP/db.class.php");
		require("../resources/PHP/social.class.php");

		$db 	= new sql();
		$social = new social();

		if(!isset($_SESSION['token']) || empty($_SESSION['token']) ) {
			$csrf = $db->getToken();
			$_SESSION['token'] = $csrf;
			} else {
			$csrf = $db->clean($_SESSION['token'],'encode');
		}

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
		
		// fetch profile section
		$userprofile = [];
		
		$stmt = $mysqli->prepare("SELECT id, name, username, accounttype, link, bio, location, email, photo, header, background, bodycolor, textcolor  FROM profile where id = ? LIMIT 1");
		$userid = $uid;
		$stmt->bind_param('i', $userid);
		$stmt->execute();
		$stmt->bind_result($id, $name, $username, $accounttype, $link, $bio, $location, $email, $photo, $header, $background, $bodycolor, $textcolor);
		
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
			array_push($userprofile,$background);
		}		
		
		
		if(isset($_POST["bodycolor"])) {
			
			$bc = $_POST["bodycolor"];
			
			if(strlen($bc) > 7 ) {
				$bc = substr($bc,0,7);
			}
			
			$bodycolor = $db->clean($bc,'encode');
			} else {
			$bodycolor = '#ffffff';
		}

		if(isset($_POST["textcolor"])) {
			
			$tc = $_POST["textcolor"];
			
			if(strlen($tc) > 7 ) {
				$tc = substr($tc,0,7);
			}
			
			$textcolor = $db->clean($tc,'encode');
			} else {
			$textcolor = '#ffffff';
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
			
			if($_FILES['photo']['tmp_name'][0] !='') {
					
					if($_FILES['photo']['error'][0] != 1) {
						
						$destination = '../images/profile/';
						
						$seed  = time().mt_rand(0,0xffffff).mt_rand(0,0xffffff).mt_rand(0,0xffffff).mt_rand(0,0xffffff);
						$seed .= time().mt_rand(0,0xffffff).mt_rand(0,0xffffff).mt_rand(0,0xffffff).mt_rand(0,0xffffff);
						$seed .= microtime();
						$seed .= '-profile';
						
						$photo = substr(strtolower($_FILES['photo']['name'][0]),strlen($_FILES['photo']['name'][0])-4,4);
						
						switch($photo) {
							case '.jpg':
							$photo = '.jpg';
							break;
							case 'jpeg':
							$photo = '.jpeg';
							break;
							case '.png':
							$photo = '.png';
							break;
							case '.gif':
							$photo = '.gif';
							break;
							default:
							$photo = false;
							break;
						}
						
						if($photo != false) {
							
							@chmod($destination,0777);
							
							if(!stristr($userprofile[8],'smile.png')) { 
								unlink('../'.$db->clean($userprofile[8],'dir'));
							}
							$upload = move_uploaded_file($_FILES['photo']['tmp_name'][0], strtolower($destination).$seed.$photo);
							if($upload) { 
								$photo = $db->clean(strtolower($destination).$seed.$photo,'dir');
								@chmod($destination,0755);
								} else {
								$submit = false;
								$reason = "Image failed to upload!";
							}
							
						} else { 
								$submit = false;
								$reason = "This type of file is not allowed, please choose a gif, jpg or png image!";	
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
						
						$photo = substr(strtolower($_FILES['header']['name'][0]),strlen($_FILES['header']['name'][0])-4,4);
						
						switch($photo) {
							case '.jpg':
							$header = '.jpg';
							break;
							case 'jpeg':
							$header = '.jpeg';
							break;
							case '.png':
							$header = '.png';
							break;
							case '.gif':
							$header = '.gif';
							break;
							default:
							$header = false;
							break;
						}
						
						if($header != false) { 
						
							@chmod($destination,0777);

							if(!stristr($userprofile[9],'background.png')) { 
								unlink('../'.$db->clean($userprofile[9],'dir'));
							}
	
							$upload_header = move_uploaded_file($_FILES['header']['tmp_name'][0], strtolower($destination).$seed.$header);
							if($upload_header) { 
								$header = $db->clean(strtolower($destination).$seed.$header,'dir');
								@chmod($destination,0755);
								} else {
								$submit = false;
								$reason = "Header image failed to upload!";
							}
							
						} else { 
								$submit = false;
								$reason = "This type of file is not allowed, please choose a gif, jpg or png image!";	
						}
						
					} else {
						$submit = false;
						$reason = "Header image cannot be empty";
					}
					
				} else {
				// $reason = "Error: This is not a link to a photo.";
			}

			if($_FILES['background']['tmp_name'][0] !='') {
					
					if($_FILES['background']['error'][0] != 1) {
						
						$destination = '../images/profile/';
						
						$seed  = time().mt_rand(0,0xffffff).mt_rand(0,0xffffff).mt_rand(0,0xffffff).mt_rand(0,0xffffff);
						$seed .= time().mt_rand(0,0xffffff).mt_rand(0,0xffffff).mt_rand(0,0xffffff).mt_rand(0,0xffffff);
						$seed .= microtime();
						$seed .= '-background';
						
						$photo = substr(strtolower($_FILES['background']['name'][0]),strlen($_FILES['background']['name'][0])-4,4);
						
						switch($photo) {
							case '.jpg':
							$background = '.jpg';
							break;
							case 'jpeg':
							$background = '.jpeg';
							break;
							case '.png':
							$background = '.png';
							break;
							case '.gif':
							$background = '.gif';
							break;
							default:
							$background = false;
							break;
						}
						
						if($background != false) { 
						
							@chmod($destination,0777);

							if(!stristr($userprofile[12],'background.png')) { 
								unlink('../'.$db->clean($userprofile[12],'dir'));
							}
	
							$upload_background = move_uploaded_file($_FILES['background']['tmp_name'][0], strtolower($destination).$seed.$background);
							if($upload_background) { 
								$background = $db->clean(strtolower($destination).$seed.$background,'dir');
								@chmod($destination,0755);
								} else {
								$submit = false;
								$reason = "Background image failed to upload!";
							}
							
						} else { 
								$submit = false;
								$reason = "This type of file is not allowed, please choose a gif, jpg or png image!";	
						}
						
					} else {
						$submit = false;
						$reason = "Background image cannot be empty";
					}
					
				} else {
				// $reason = "Error: This is not a link to a photo.";
			}
			
			if(strlen($_FILES['photo']['name'][0]) > 5 && strlen($_FILES['header']['name'][0]) > 5 && strlen($_FILES['background']['name'][0]) > 5) {
				if($submit == true) { 
					// insert new records into profile
					$stmt = $mysqli->prepare("UPDATE profile SET name = ?, accounttype = ?, link = ?, bio = ?, location = ?, email = ?, photo = ?, header = ?, background = ?, bodycolor = ?, textcolor = ? WHERE id = ?");
					$userid = $uid;
					$stmt->bind_param("ssssssssssss", $name, $accounttype, $link, $bio, $location, $email, $photo, $header, $background, $bodycolor, $textcolor, $userid);
					$stmt->execute();
					$resultmessage = "Successfully updated both your profile, profile picture, header and background image!";
				}	
			} elseif(strlen($_FILES['photo']['name'][0]) > 5 && strlen($_FILES['header']['name'][0]) > 5) {
				if($submit == true) { 
					// insert new records into profile
					$stmt = $mysqli->prepare("UPDATE profile SET name = ?, accounttype = ?, link = ?, bio = ?, location = ?, email = ?, photo = ?, header = ?, bodycolor = ?, textcolor = ? WHERE id = ?");
					$userid = $uid;
					$stmt->bind_param("sssssssssss", $name, $accounttype, $link, $bio, $location, $email, $photo, $header, $bodycolor, $textcolor, $userid);
					$stmt->execute();
					$resultmessage = "Successfully updated both your profile, profile picture and header image!";
				}	
			} elseif(strlen($_FILES['photo']['name'][0]) > 5) {
				if($submit == true) { 
					// insert new records into profile
					$stmt = $mysqli->prepare("UPDATE profile SET name = ?, accounttype = ?, link = ?, bio = ?, location = ?, email = ?, photo = ?, bodycolor = ?, textcolor = ? WHERE id = ?");
					$userid = $uid;
					$stmt->bind_param("ssssssssss", $name, $accounttype, $link, $bio, $location, $email, $photo, $bodycolor, $textcolor, $userid);
					$stmt->execute();
					$resultmessage = "Successfully updated your profile and picture!";
				}	
			} elseif(strlen($_FILES['header']['name'][0]) > 5) {
				if($submit == true) { 
					// insert new records into profile
					$stmt = $mysqli->prepare("UPDATE profile SET name = ?, accounttype = ?, link = ?, bio = ?, location = ?, email = ?, header = ?, bodycolor = ?, textcolor = ? WHERE id = ?");
					$userid = $uid;
					$stmt->bind_param("ssssssssss", $name, $accounttype, $link, $bio, $location, $email, $header, $bodycolor, $textcolor, $userid);
					$stmt->execute();
					$resultmessage = "Successfully updated your profile and header image!";
				}	
			} elseif(strlen($_FILES['background']['name'][0]) > 5) {
				if($submit == true) { 
					// insert new records into profile
					$stmt = $mysqli->prepare("UPDATE profile SET name = ?, accounttype = ?, link = ?, bio = ?, location = ?, email = ?, background = ?, bodycolor = ?, textcolor = ? WHERE id = ?");
					$userid = $uid;
					$stmt->bind_param("ssssssssss", $name, $accounttype, $link, $bio, $location, $email, $background, $bodycolor, $textcolor, $userid);
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
		
		$stmt = $mysqli->prepare("SELECT id, name, username, accounttype, link, bio, location, email, photo, header, background, bodycolor, textcolor  FROM profile where id = ? LIMIT 1");
		$userid = $uid;
		$stmt->bind_param('i', $userid);
		$stmt->execute();
		$stmt->bind_result($id, $name, $username, $accounttype, $link, $bio, $location, $email, $photo, $header, $background, $bodycolor, $textcolor);
		
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
			array_push($userprofile,$background);
		}	
?>
<!DOCTYPE html>
<html>
	<head>
		<?php
		include("../resources/PHP/header.php");
							
		if(isset($userprofile[10]) != NULL && isset($userprofile[11]) != NULL) { 

			if(strstr($userprofile[10],'#') && strstr($userprofile[11],'#')) { 
				echo $social->css($userprofile[10],$userprofile[11]);		
			}
		}
		?>
		
		<div id="photo-editor">
			<div id="photo-editor-main">
				<div id="photo-editor-photo">1</div>
				<div id="photo-editor-controls">2</div>
			</div>
		</div>

		<div id="wrapper">
					<div id="nav-left">
						<ul>
						<li><a href="<?php echo $host;?>profile/">Profile</a></li>
						<li><a href="<?php echo $host;?>timeline/">Timeline</a></li>
						<li><a href="<?php echo $host;?>mentions/">Mentions <?php if($count_mentions >=1) { echo "<span class=\"messages-alert\">" . $count_mentions ."</span>"; } ?></span></a></li>
						<li class="selected"><a href="<?php echo $host;?>settings/">Settings</a></li>
						<li class="mobile-block"><a href="<?php echo $statusloggedurl;?>"><?php echo $statuslogged;?></a></li>
						</ul>	
					</div>
					
					<div id="nav-center">
						<div id="timeline-username">Settings</div> 
						
						<div id="timeline-header" class="settings-page"></div>
							
							<div><div>
							
						</div>
							
						<div class="settings">
						<?php
						if($submit == false) {
							echo "<div id='settings-error'>" .  $reason . "</div>";
							} elseif(isset($resultmessage)) {
							echo "<div id='settings-success'>" .  $resultmessage . "</div>";
						} else {}
						?>
						<hr />
						<div class="settings-form">
							<form name="post" action="" method="POST" onSubmit="return Social.progressAndroid('Uploading... please wait.');" autocomplete="off" data-lpignore="true" enctype="multipart/form-data">
							<input type="hidden" name="csrf" value="<?php echo $db->clean($csrf,'encode');?>" />
					
							<div class="settings-form-item-account"> 
							
								<div class="settings-form-item-left">
									<span class="form-sub">E-mail: </span> <input type="text" name="email"  value="<?php echo $db->clean($userprofile[7],'encode');?>"/>
									<span class="form-sub">Name: </span> <input type="text" name="name" value="<?php echo $db->clean($userprofile[1],'encode');?>" />
									<span class="form-sub">Account type: </span> <input type="text" name="accounttype" value="<?php echo $db->clean($userprofile[3],'encode');?>" />
								</div>
								<div class="settings-form-item-left">
									<span class="form-sub">Website: </span> <input type="text" name="link" value="<?php echo $db->clean($userprofile[4],'encode');?>" />
									<span class="form-sub">Bio: </span> <input type="text" name="bio" value="<?php echo $db->clean($userprofile[5],'encode');?>" />
									<span class="form-sub">Location: </span> <input type="text" name="location" value="<?php echo $db->clean($userprofile[6],'encode');?>" />
								</div>	
							</div>
							
							<strong>Pictures &amp; Photos</strong>
							
							<div class="settings-form-item-pictures"> 
								<div class="settings-form-item-left">
								 
								<div id="image-container"><div id="settings-image" style="background:url('<?php echo $host . $db->clean($userprofile[8],'encode');?>') !important; background-size: cover!important; background-color: #000!important;"></div></div>
								<div class="form-sub">Profile Picture.</div>
								
								<input type="file" name="photo[]"  accept="image/png, image/jpeg, image/gif, image/jpg" title="Square images works best!" alt="Square images works best!">
								
								<span class="form-sub">Header (615 x 168).</span> 
								<input type="file" name="header[]" accept="image/png, image/jpeg, image/gif, image/jpg" title="615px width and 168px height works best!" alt="615px width and 168px height works best!">
								
								<!-- <span class="form-sub">Background (1024 x 768): </span> 
								<input type="file" name="background[]" accept="image/png, image/jpeg, image/gif, image/jpg">
								-->
																
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
									<input type="submit" name="post" value="Update" onclick="Social.progressAndroid('Uploading... please wait.');" />
								</div>
							</form>
						</div>
						<br /> <br /> <br /> 
					</div>
		</div>
		
		<?php
		include("../resources/PHP/footer.php");
		?>
	</body>
</html>