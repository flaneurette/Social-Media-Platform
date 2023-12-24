<?php

$formadd = '';

if($_REQUEST['mobile']) {
	$formadd = '?mobile=true';
}

header("Location: ../".$formadd);
exit;

?>

