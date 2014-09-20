<?php
	
require_once('common.php');


$settings = array(
	'username' => 'admin',
	'password' => hash_password($factory_password, 'admin')
);

save_settings($settings, 'application/'.$password_file);
?>