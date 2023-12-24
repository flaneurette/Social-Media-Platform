<?php

error_reporting(0);

ini_set('display_errors', 0); 
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.gc_maxlifetime',35650000);
ini_set('session.cookie_lifetime',35650000);
header("X-Frame-Options: DENY"); 
header("X-XSS-Protection: 1; mode=block"); 
header("Strict-Transport-Security: max-age=30");
header("Referrer-Policy: same-origin");
	
// header
$host = 'https://www.twigpage.com/';

$count_mentions = 0;

if(isset($_SESSION['uid']) && $_SESSION['uid'] !='') {
	// get messages statistics.
	$mentions = $db->query("SELECT * FROM timeline WHERE toid = '".$db->intcast($_SESSION['uid'])."' AND readit = '0'");
	$count_mentions = $db->intcast(count($mentions));
	
	if($count_mentions >=1) {
		$title = '<title>('.$count_mentions.')'.' Twigpage - Social Timelines.</title>';
		$favicon = '<link rel="icon" type="image/ico" href="/favicon-message.ico?message=true">';
		} else {
		$title = '<title>Twigpage - Social Timelines.</title>';
		$favicon = '<link rel="icon" type="image/ico" href="/favicon.ico">';
	}
	echo $title;
}

?>
	<meta charset="utf-8">
	<meta name="description" content="Twigpage is a new social media platform.">
	<meta name="keywords" content="twigpage, social media, facebook, twitter, twitter alternative, mastodon alternative">
	<meta name="author" content="Twigpage">
	<meta name="Pragma" content="no-cache">
	<meta name="Cache-Control" content="no-cache">
	<meta name="Expires" content="-1">
	<meta name="revisit-after" content="3 days">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- favicon-generator.org -->
	<?php echo $favicon;?>
	<link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
	<link rel="manifest" href="/manifest.json">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">

	<link href="<?php echo $host;?>resources/style/themes/default/reset.css?rev=1.6.41<?php echo time();?>" rel="stylesheet">
	<link href="<?php echo $host;?>resources/style/themes/default/style.css?rev=1.6.41<?php echo time();?>" rel="stylesheet">
	<link href="<?php echo $host;?>resources/style/themes/default/mobile.css?rev=1.6.41<?php echo time();?>" rel="stylesheet">
	</head>
	<body>
				<nav>
					<div>
						<ul id="nav">
						<?php 
						if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != '') {
							echo '<li id="nav"><a href="'.$host.'timeline/"><img src="'.$host.'resources/images/logo.png" id="header-logo"/></a> </li>';
							} else {
							echo '<li id="nav"><a href="'.$host.'"><img src="'.$host.'resources/images/logo.png" id="header-logo"/></a> </li>';
						}
						?>
						<?php 
						if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != '') {
						?>
						<li id="nav-right-menu" onclick="Social.showTimelineOptions('timeline-options-box-menu');"><a href="#">Menu</a></li>
						<div id="timeline-options-box-menu" class="timeline-options-box-menu">
							<ul class="mobile-nav">
								<li>&nbsp;</li>
								<li><a href="<?php echo $host;?>profile/" class="menu-link">Profile</a></li>
								<li><a href="<?php echo $host;?>timeline/?mobile=true" class="menu-link">Timeline</a></li>
								<li><a href="<?php echo $host;?>mentions/?mobile=true" class="menu-link">Mentions</a></li> 
								<li><a href="<?php echo $host;?>settings/?mobile=true" class="menu-link">Settings</a></li>
								<li><a href="#" class="menu-link"></a></li>
								<li><a href="<?php echo $host;?>logout/?mobile=true" class="menu-link">Sign out</a></li>
							</ul>
						</div>
							
						<?php
						} else {
							echo '<li id="nav-right"><a href="https://www.twigpage.com/signup/">Signup</a></li>';
						}
						?>
						</div>
				</nav>
				