<?php

echo phpinfo();
echo "1";
session_start();


	require("../resources/PHP/db.class.php");
	require("../resources/PHP/social.class.php");

	$db 	= new sql();
	$uid = 1;
	$timeline 	= $db->query("SELECT * FROM timeline WHERE uid = ".$db->intcast($uid)." ORDER BY tid DESC");

	// get database information on profile.
	$timeline 	= $db->query("SELECT * FROM timeline WHERE uid = ".$db->intcast($uid)." ORDER BY tid DESC");

	$userprofile = [];
	$profile = [];		
			
	$stmt = $mysqli->prepare("SELECT * FROM profile where id = ? LIMIT 1");

	$params = array("s",$uid);

	foreach($params as $key => $value) $userprofile[$key] = &$params[$key];
	call_user_func_array(array($stmt, 'bind_param'), $userprofile);
	$stmt->execute();
	$query = $stmt->get_result();

	while($row = $query->fetch_array(MYSQLI_ASSOC)) {
		$profile[] = $row;
	}

	$stmt->close();
	
	var_dump($profile);
?>