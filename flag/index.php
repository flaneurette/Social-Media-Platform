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
		
		
		if(isset($_POST['user-report']) && isset($_POST['csrf'])) {
			
				if($_POST['csrf'] == $_SESSION['token']) {
			
					$report = $db->clean($_POST['user-report'],'encode');
					$accounttype = $db->clean($_POST['accounttype'],'encode');
					$name = $db->clean($_POST['name'],'encode');
					$email = $db->clean($_POST['email'],'encode');
					// send mail.
					
					
					$postid  		= $db->intcast($_REQUEST['postid']);
					$profileid		= $db->clean($_REQUEST["profileid"],'encode');
					$uid 			= $db->intcast($_SESSION['uid']);
					$insertvalue 	= 1;
					
					$stmt = $mysqli->prepare("INSERT INTO flagged (flaggedby, reason, postid, hide) VALUES (?, ?, ?, ?)");
					$stmt->bind_param("isii", $uid, $report, $postid, $insertvalue);
					$stmt->execute();
					
	
					$message = $report;
					$message .= PHP_EOL;
					$message .= $accounttype;
					$message .= PHP_EOL;
					$message .= $name;
					$message .= PHP_EOL;
					$message .= $email;
					
					mail("info@flaneurette.nl","Incidence",$message);
					
					$showmessage = "Success!";
				}
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
						<div id="timeline-username">Flag or report</div> 
						
						<div id="timeline-header" class="settings-page"></div>
							
							<div><div>
							
						</div>
						
						<div class="settings">
						<hr />
						<div class="settings-form">
						
							<?php
							if($showmessage) {
								echo "<div id='settings-success'>" .  $showmessage . "</div>";
							} 
							?>
						
							<form name="post" action="" method="POST" autocomplete="off" data-lpignore="true">
							<input type="hidden" name="csrf" value="<?php echo $db->clean($csrf,'encode');?>" />
					
							<div class="settings-form-item-account"> 
							
								<div class="settings-form-item-left">
									<span class="form-sub">E-mail: </span> <input type="text" name="email"  value=""/>
									<span class="form-sub">Name: </span> <input type="text" name="name" value="" />
									<span class="form-sub">Report type: </span> <input type="text" name="accounttype" disabled value="INCIDENCE:<?php echo $db->clean($_REQUEST["profileid"],'encode') . ':' . $db->clean($_REQUEST["postid"],'encode'); ?>" />
								</div>
								<div class="settings-form-item-left">
									<span class="form-sub">Additional information:</span> <textarea type="text" name="user-report" cols="40" rows="10" placeholder="Please provide additional details..." /></textarea>
									<input type="submit" value="Submit report" />
								</div>	
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