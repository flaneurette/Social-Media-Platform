<!DOCTYPE html>
<html>
	<head>
	<?php
	include("../../resources/PHP/header.php");
	?>
		<div id="wrapper">
					<div id="nav-left">
						<ul>
						<li><a href="<?php echo $host;?>profile/">Profile</a></li>
						<li><a href="<?php echo $host;?>timeline/">Timeline</a></li>
						<li><a href="<?php echo $host;?>mentions/">Mentions</a></li>
						<li><a href="<?php echo $host;?>settings/">Settings</a></li>
						<li class="mobile-block"><a href="<?php echo $statusloggedurl;?>"><?php echo $statuslogged;?></a></li>
						</ul>	
					</div>
					
					<div id="nav-center">
						
						
						<div id="timeline-username">Contact</div> 
						
						<div id="timeline-header" class="contact-page"></div>

						
						<div class="content">
			
							<div class="content-text"> 
							To contact us, there are several profiles to which you can submit your question or comment:
							</div>
							
							<strong>Accounts</strong>
							
							<div class="content-text"> 
							@hq
							@administrator
							</div>
													
						</div>
					</div>
		</div>

		<?php
		include("../../resources/PHP/footer.php");
		?>
	</body>
</html>