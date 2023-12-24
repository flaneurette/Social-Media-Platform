<?php
session_start();
if(isset($_SESSION["upload_progress_123"])) { 
if($_SESSION["upload_progress_123"]["done"] == false) {

	$length = $_SESSION["upload_progress_123"]["content_length"];
	$processed = $_SESSION["upload_progress_123"]["bytes_processed"];

	if($length >=1) { 
		echo (int)(($processed / $length) * 100 );
	}

}
}
?>