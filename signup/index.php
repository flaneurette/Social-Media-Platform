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

$submit = true;
$email = '';
$username = '';
$password = '';

if(isset($_POST["csrf"])) { 

	if(isset($_POST["csrf"]) != '') {
		$csrf_post = $db->clean($_POST["csrf"],'encode');
		} else {
		$submit = false;
		$reason = "Token is incorrect, are you a bot?";
	}
	
	if($_POST["csrf"] == $csrf) {
		$csrf_post = $db->clean($_POST["csrf"],'encode');
		} else {
		$submit = false;
		$reason = "Token is incorrect, are you a bot?";
	}	
	
	if(isset($_POST["email"]) != '') {
		$email = $db->clean($_POST["email"],'encode');
		} else {
		$submit = false;
		$reason = "E-mail cannot be empty";
	}
	
	if(isset($_POST["username"]) != '') {
		$username = strtolower($db->clean($_POST["username"],'encode'));
		$username = strtolower($db->clean($_POST["username"],'user'));
		} else {
		$submit = false;
		$reason = "Username cannot be empty";
	}
	
	if(strlen($_POST["username"]) <= 25) {
		$username = strtolower($db->clean($_POST["username"],'encode'));
		$username = strtolower($db->clean($_POST["username"],'user'));
		} else {
		$submit = false;
		$reason = "Username is too long, try something shorter.";
	}	
	
	if(strlen($_POST["password"]) >= 64 ) {
		$submit = false;
		$reason = "Password is unreasonbly long";		
	}
	
	if(isset($_POST["password"]) != '') {
		$password = $db->clean($_POST["password"],'encode');
		} else {
		$submit = false;
		$reason = "Password cannot be empty";
	}	
	
	if(isset($_POST["captcha"]) != '') {
		$captcha = $_POST["captcha"];
			if($_SESSION['captcha_question'] != $captcha) {
				$submit = false;
				$reason = "Captcha incorrectly solved.";
			} 
		} else {
		$submit = false;
	}
	
	if($submit == true) { 
		
		$userprofile = [];
		
		$stmt = $mysqli->prepare("SELECT id, name, password FROM profile where username = ?");
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$stmt->bind_result($id,$name,$password);
		
		while($stmt->fetch()) {
			array_push($userprofile,$name);
			array_push($userprofile,$email);
		}		
		
		$stmt->close();
		
		if(count($userprofile) >= 1) {
			
			$submit = false;
			$reason = "Account username already exist.";
			
			} else {

			$hash 		=  sha1($db->getToken());
			$password 	=  password_hash($password,PASSWORD_DEFAULT);
			$joined		=  date("F j, Y");
			$active 	=  1;
			$photo      = 'images/profile/smile.png';
			
			$stmt = $mysqli->prepare("INSERT INTO profile (username, email, hash, password, joined, active, photo) VALUES (?, ?, ?, ?, ?, ?, ?)");
			$stmt->bind_param("sssssis", $username, $email, $hash, $password, $joined, $active, $photo);
			$stmt->execute();

			$resultmessage = "Success. Please check your e-mail for instructions. Have fun on Twigpage!";

			$email 		= str_replace(';','',$email);
			$username 	= $username;
			$body 		= 'Hello, please verify your signup: https://www.twigpage.com/verified'.$hash;
			$subject 	= 'Welcome to Twigpage';

			mail($email,$subject,$body,"From: Twigpage <info@twigpage.com>");

		}
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
						</ul>	
					</div>
					
					<div id="nav-center">
						<div id="timeline-username">Signup</div> 
						
						<div id="timeline-header" class="signup-page"></div>
							<div id="timeline-signup"><div>
						</div>
							
						<div id="signup-form">
						
						<?php
						if($submit == false) {
							echo "<div id='signup-error'>" .  $reason . "</div>";
						} elseif(isset($resultmessage)) {
							echo "<div id='signup-success'>" .  $resultmessage . "</div>";
						} else {}
						?>
						
						<form action="" method="POST" name="signup" onsubmit="return Social.checkSignupForm();">
						<input type="hidden" name="csrf" value="<?php echo $csrf;?>" />
						<span>E-mail: </span> <input type="text" name="email" id="signup-email" value="<?php echo $email;?>" />
						<span>Desired Username: </span> <input type="text" name="username" id="signup-username" pattern="\w{3,16}" data-lpignore="true" autocomplete="false" value="<?php echo $username;?>"/>
						<span>Desired Password: </span> <input name="password" id="signup-password" type="password" data-lpignore="true" autocomplete="false" value="<?php echo $password;?>"/>
						
						<div id="captcha"><img src="../resources/PHP/mail/captcha/index.php" height="67" /></div>
						<span>Solve captcha: </span> <input type="text" name="captcha" id="signup-captcha" />
						
						By signing up you will agree to our <a href="https://www.twigpage.com/contents/privacy/" target="_blank" >privacy policy</a> &amp; <a href="https://www.twigpage.com/contents/terms/" target="_blank" >terms</a>
						<br />
						<input type="submit" name="post" value="Signup" style="margin-bottom:33px!important;" />
						</form>
					</div>
		</div>

		<?php
		$mobile = true;
		include("../resources/PHP/footer.php");
		?>
	</body>
</html>