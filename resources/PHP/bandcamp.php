<?php

	require("db.class.php");

	$db = new sql();

	if(strlen($_REQUEST['embed']) > 255) {
		echo 'false';
		exit;
	}
	
	if(stristr($_REQUEST['embed'],'../')) {
		echo 'false';
		exit;
	}
	
	$document = $db->clean($_REQUEST['embed'],'encode');

	if(preg_match_all('/https:\/\/[a-z0-9]+.bandcamp.com\/track\/[a-z0-9-_]+/i', $document, $matches)) {
		$embed = true;
	}

	if(preg_match_all('/https:\/\/[a-z0-9]+.bandcamp.com\/album\/[a-z0-9-_]+/i', $document, $matches)) {
		$embed = true;
	}

	if($embed == true) {
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $document);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);

		if(preg_match_all('/property\=\"twitter\:player\"\s+content\=\"(.*)\"/i', $output, $output)) {
			if($output[1][0]) { 
				echo '<iframe id="bandcamp-player" src="'.$db->clean($output[1][0],'encode').'"></iframe>';
				} else {
				echo 'false';
			}
		} else {
			echo 'false';
		}
	}
?>