<?php
$day = getdate();
?>
<style>

.white {
	background-color:#fff;
	width:15px;
	height: 15px;
	border:1px solid #ddd; 
	padding:5px;
	margin:0px;
	display:block;
	float:left;
	text-align:center;
	font-size:12px;
	color: #999;
}

.black {
	background-color:#ddd;
	width:15px;
	height: 15px;
	border:1px solid #ddd; 
	padding:5px;
	margin:0px;
	color:#000;
	display:block;
	float:left;
	text-align:center;
	font-size:12px;
	color: #999;
}

.container {
	margin:5px;
	width: 530px;
}

#board {
	width: 530px;
}
#now {
	background-color: darkred;
	border: 1px solid darkred;
	color: #fff;
}
</style>

<div id="board"> 

</div>

<script>

	let day = <?php echo ($day['yday']+1); ?>;
	
	var board = document.getElementById('board');
	
	var k = 1;
	for(var i=0; i <= 17; i++) {
		var div = document.createElement('div');
			div.id = 'div' + j;
			div.className = 'container';
			board.appendChild(div);
		for(var j=1; j <= 20; j++) {
			if(k <= 365) {
				var span = document.createElement('span');
				if(j % 2) {
				span.className = 'black';
				} else {
				span.className = 'white';
				}
				if(day == k) {
					span.id = 'now';
				}
				span.innerHTML = k;
				div.appendChild(span);
				k++;
			}
		}
		k++;
	}
	
</script>