<?php

	header('Content-type: image/png');
	session_start();
	
	$font 		= './Thankfully.ttf';
	$text 		= "";
	$captcha 	= imagecreatefrompng('image.png');
	$shadow 	= imagecolorallocate($captcha, 0, 0, 0);
	$color 		= imagecolorallocate($captcha, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255));
	$consonants = array('b','c','d','f','g','h','j','k','l','m','n','p','r','s','t','v','w','x','y','z');
	$vowels 	= array('a','e','i','o','u');

	for($i=0; $i<=2; $i++){
		   $text  .= $consonants[mt_rand(0,count($consonants)-1)];
		   $text  .= $vowels[mt_rand(0,count($vowels)-1)];
	}
		
	$_SESSION['captcha_question'] = $text;
	imagettftext($captcha, 66, 0, 30, 112, $color, $font, $text);
	imagepng($captcha);
	imagedestroy($captcha);
	$text = "";

?>
