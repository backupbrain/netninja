<?
require_once("application/logout.php");
?>
<!DOCTYPE html>
<head>
	<style id="antiClickjack">body{display:none !important;}</style>
	<script type="text/javascript">
   if (self === top) {
       var antiClickjack = document.getElementById("antiClickjack");
       antiClickjack.parentNode.removeChild(antiClickjack);
   } else {
       top.location = self.location;
   }
	</script>
	<?php
	// prevent BREACH attack
	$randomData = mcrypt_create_iv(25, MCRYPT_DEV_URANDOM);
	echo "<!--"
	    . substr(
	        base64_encode($randomData), 
	        0, 
	        ord($randomData[24]) % 32
	    ) 
	    . "-->";
	?>
</head>
<body>
</body>
</html>