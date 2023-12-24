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
						<div id="timeline-username">Terms</div> 
						
						<div id="timeline-header" class="terms-page"></div>

						
						<div class="content">

							<div class="content-text"> 
							This terms are valid from the 9th of december 2022 until further notice.
							</div>
							
							<strong>Using twigpage</strong>
							
							<div class="content-text"> 
							By using Twigpage you agree that you will use the service in a responsible manner, and we ask not to automatically scrape our services with scripts or programs, in such a way that it will disrupt the Twigpage service. Twigpage does currently not have an API that allows this. 
							</div>
							
							<strong>Signing up</strong>
							
							<div class="content-text"> 
							By signing up to Twigpage, the user is reponsible to choose a secure password. It is advised to create such a secure password in order that your account remain secure. It is not allowed to upload anything other than images, pictures, or music. Our service will detect any malicious upload attempt, and will warn the admin of unlawful attempts and we may take futher actions.
							</div>

							<strong>Prohibited content</strong>
							
							<div class="content-text"> 
							We ask that our users follow the laws from the country they reside in. If something is not allowed, we ask you to not post it. Further, U.S. laws apply as this service runs on U.S. servers. If we get a takedown request or a post is flagged, based upon U.S. laws, we might comply. Be considerate, is all we ask.
							</div>
							
							<strong>Bugs</strong>
							
							<div class="content-text"> 
							Software has bugs, we admit, if you find any 🐛 please contact the administrator of twigpage at: @administrator. Depending on the level of the bug, we might award you with a (special) verification and much praise!
							</div>
							
							<strong>Cookies</strong>
							<div class="content-text"> 
							Twigpage does not make use of cookies, and thus we do not show a cookie acceptance bar. We also do not use third-party trackingcookies. At Twigpage, we value privacy.
							</div>							
						</div>
					</div>
		</div>

		<?php
		include("../../resources/PHP/footer.php");
		?>
	</body>
</html>