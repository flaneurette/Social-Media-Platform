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
						<div id="timeline-username">Privacy Policy</div> 
						
						<div id="timeline-header" class="privacy-page"></div>

						
						<div class="content">
							
							<div class="content-text"> 
							This privacy policy is valid from the 9th of december 2022 until further notice.
							</div>
							
							<strong>Formalities</strong>
							
							<div class="content-text"> 
							Twigpage office is located in The Netherlands, Europe, however we use U.S. (web) servers for our websites, and thus U.S. laws apply to this privacy policy, as this service is run from U.S. mainland. Our servers are located at RackNerd.	
							</div>
							
							<strong>Declaration</strong>
							
							<div class="content-text"> 
							Twigpage does not store any user details, except for the e-mailaddres, name, photo, header, encrypted password (zero knowledge) and username in order to make the site function as it is supposed to do. As for now, Twigpage does not sent e-mails to it's users, but this might change in the future. When it does, twigpage will implement an opt-out functionality under the users settings. Twigpage does not make use of Google Analytics. Instead we have our own statistics, which is linked to a user id. The only statistics we store on our servers are the number of likes, shares, stars and views from other users indicated by a userid. Twigpage does everything possible to secure user details, and most is (industry standard) encrypted via the users password of which we have no knowledge other than the user itself.
							</div>

							<strong>Security</strong>
							
							<div class="content-text"> 
							Twigpage guarantees a level of security that is in line with the OWASP security industry standards. This means that our webapplications are secure to the standards of the OWASP guidelines.
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