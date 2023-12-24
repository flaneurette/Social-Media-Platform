<?php

session_start();

require("../resources/PHP/db.class.php");

$db = new sql();

$submit = true;

if(isset($_REQUEST["code"])) { 

	if(isset($_REQUEST["code"]) != '') {
		$verifycode = $db->clean($_REQUEST["code"],'encode');
		$verifycode = $db->clean(str_replace('verified','',$verifycode),'encode');
		} else {
		$submit = false;
	}
	
	if($submit == true) { 
		
		$userprofile = [];
		
		$stmt = $mysqli->prepare("SELECT hash,active,verified FROM profile where hash = ?");
		$stmt->bind_param('s', $verifycode);
		$stmt->execute();
		$stmt->bind_result($hash,$active,$verified);
		
		while($stmt->fetch()) {
			array_push($userprofile,$hash);
			array_push($userprofile,$active);
			array_push($userprofile,$verified);
		}		
		
		if($userprofile[2] == '1') {
			$submit = false;
			$reason = "Error: Account already verified!";	
			} else {
			
			$stmt = $mysqli->prepare("UPDATE profile SET active = 1, verified = 1 WHERE hash = ?");
			$stmt->bind_param("s", $verifycode);
			$stmt->execute();

			$resultmessage = "Success! please check your e-mail for instructions. Have fun on Twigpage!";

			}
	}
}
header("Location: index.php");
?>
Success.