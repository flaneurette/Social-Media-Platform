<?php

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
						<div id="timeline-username">@Twigpage!</div> 
						
						<div id="timeline-header"><!-- photo --></div>
							
							<div id="timeline-profile">
							
							<div>
							
							</div>
							
						<div id="timeline">
						<div id="nav-left-menu">
						<ul>
						<li><a href="<?php echo $host;?>profile/">Profile</a></li>
						<li><a href="<?php echo $host;?>timeline/">Timeline</a></li>
						<li><a href="<?php echo $host;?>mentions/">Mentions <?php if($count_mentions >=1) { echo "<span class=\"messages-alert\">" . $count_mentions ."</span>"; } ?></span></a></li>
						<li><a href="<?php echo $host;?>settings/">Settings</a></li>
						<li class="mobile-block"><a href="<?php echo $statusloggedurl;?>"><?php echo $statuslogged;?></a></li>
						</ul>	

						</div>
						
						<br /> 
					</div>
		</div>

		<?php
		include("../resources/PHP/footer.php");
		?>
	</body>
</html>