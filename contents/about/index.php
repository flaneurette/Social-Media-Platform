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
						<div id="timeline-username">About</div> 
						
						<div id="timeline-header" class="about-page"></div>

						
						<div class="content">
							
							<div class="content-text"> 
							Welcome to Twigpage! Twigpage is a new microblogging platform. Write posts the way you like, like, share and star posts at your will. Upload images, and even audio files to your timeline. Started in 2022, Twigpage is now one of the newest social media plaforms, so come and join us and write something on your own personal timeline! invite your friends, and do so much more on Twigpage. Twigpage comes from the word twig and page. Like any network, a tree has many twigs, which together form the branches, twigs and leaves. Twigpage is considerate to nature, and therefore our servers are climate neutral and carbon is offset by planting trees. 
							</div>
							
							<strong>Joining</strong>
							
							<div class="content-text"> 
							You can sign up at this page: <a href="../../signup/">signup</a>
							</div>
													
						</div>
					</div>
		</div>

		<?php
		include("../../resources/PHP/footer.php");
		?>
	</body>
</html>