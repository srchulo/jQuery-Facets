<?php
	foreach($_GET as $key=>$value) {
		echo "$key=";
		if(is_array($value)) {
			foreach($value as $el) { 
				echo "$el,";
			}
		}
		else { 
			echo "$value"; 
		}
		echo "<br />";
	}
?>
