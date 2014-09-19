<?php
	
$enable_ssh = false;
if ($_GET['enable']) {
	$enable_ssh = true;
}	

if ($enable_ssh) {
	`sudo ../../scripts/enable_ssh.sh`;
} else {
	`sudo ../../scripts/disable_ssh.sh`;
}
?>