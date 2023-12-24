<?php

$ticker = $db->query("SELECT * FROM timeline ORDER BY RAND()");
	for($i=0; $i < count($ticker); $i++) {	
?>
				
				<a href="#">
				<span class="ticker" style="text-decoration:none!important;color:#fff;">
					<?php echo $db->prepareHTML($ticker[$i]['post']);?>
				<//span>
				</a>
	
<?php
}					

?>