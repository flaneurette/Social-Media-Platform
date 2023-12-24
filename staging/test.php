<?php

require("../resources/PHP/db.class.php");

$db = new sql();

$message = 'sfdsdf  sdfsdf <b>sdfsdf</b> asdasdasd <br> <i>sdsdfsd</i>';
$message = utf8_encode($db->clean($message,'encode'));

		$searchtags = ['&lt;br&gt;','&lt;br /&gt;','&lt;em&gt;','&lt;/em&gt;','&lt;i&gt;','&lt;/i&gt;','&lt;b&gt;','&lt;/b&gt;','&lt;strong&gt;','&lt;/strong&gt;'];
		$replacetags = ['<br>','<br>','<em>','</em>','<i>','</i>','<b>','</b>','<b>','</b>'];
		$message = str_ireplace($searchtags,$replacetags,$message);

echo $message;

?>