<?php

session_start();

if(isset($_SESSION) && !isset($_POST["csrf"])) {
	
	$_SESSION['uid'] = '';
	$_SESSION['profile'] = '';
	$_SESSION['loggedin'] = '';
	$_SESSION['token'] = '';
	session_destroy();
	header("Location: ../login/?mobile=true");
	exit;
}
			
require("../resources/PHP/db.class.php");

$db = new sql();

if(!isset($_SESSION['token']) || empty($_SESSION['token']) ) {
	$csrf = $db->getToken();
	$_SESSION['token'] = $csrf;
	} else {
	$csrf = $db->clean($_SESSION['token'],'encode');
}

$submit = true;

if(isset($_POST["csrf"])) { 

	if(isset($_POST["csrf"]) != '') {
		$csrf_post = $db->clean($_POST["csrf"],'encode');;
		} else {
		$submit = false;
		$reason = "Error: Token is incorrect, are you a bot?";
	}
	if($_POST["csrf"] == $csrf) {
		$csrf_post = $db->clean($_POST["csrf"],'encode');;
		} else {
		$submit = false;
		$reason = "Error: Token is incorrect, are you a bot?";
	}	
	if(isset($_POST["username"]) != '') {
		$username = $db->clean($_POST["username"],'encode');
		} else {
		$submit = false;
		$reason = "Error: Username cannot be empty";
	}
	if(isset($_POST["password"]) != '') {
		$password = $db->clean($_POST["password"],'encode');
		} else {
		$submit = false;
		$reason = "Error: Password cannot be empty";
	}	
	
	if($submit == true) { 
	
		$userprofile = [];
		$result = [];
		
		$stmt = $mysqli->prepare("SELECT id,username,email,password FROM profile where username = ? LIMIT 1");
		
		$params = array("s",$username);
		
		foreach($params as $key => $value) $userprofile[$key] = &$params[$key];
		call_user_func_array(array($stmt, 'bind_param'), $userprofile);
		$stmt->execute();
		
		if($stmt->error) {
			
			echo $stmt->error;
		}
		
		$query = $stmt->get_result();
		
		while($row = $query->fetch_array(MYSQLI_ASSOC)) {
			$result[] = $row;
		}

        $stmt->close();
		
		if(count($result) >= 1 && password_verify($password, $result[0]['password'])) {
			$resultmessage = "Success! Have fun on Twigpage!";
			$_SESSION['uid'] = $db->intcast($result[0]['id']);
			$_SESSION['profile'] = $result[0];
			$_SESSION['loggedin'] = '1';
			 header("Location: ../login/");
			 exit;
			} else {
			$submit = false;
			$reason = "Error: Login details are incorrect, please try again!";
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
						<div id="timeline-username">Logout</div> 
						
						<div id="timeline-header" class="logout-page"></div>
							
							<div id="timeline-profile">
							
							<div>
							
							</div>
							
						<div id="timeline">
								
						See you soon! <br /><br />
						<?php
						if($submit == false) {
							echo "<div id='signup-error'>" .  $reason . "</div>";
						} elseif(isset($resultmessage)) {
							echo "<div id='signup-success'>" .  $resultmessage . "</div>";
						} else {}
						
						if(!isset($resultmessage)) {
						?>
						<hr />
						<form name="post" action="" method="POST">
						<input type="hidden" name="csrf" value="<?php echo $csrf;?>" />
						<span>Username: </span> <input type="text" name="username" />
						<span>Password: </span> <input name="password" type="password" />
						<input type="submit" name="post" />
						</form>
						<?php
						}
						?>
						<br /> 
					</div>
		</div>

		<?php
		$mobile = true;
		include("../resources/PHP/footer.php");
		?>
	</body>
</html>