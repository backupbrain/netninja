<?php
	
require_once('common.php');


$settings = array(
	'username' => 'admin',
	'password' => hash_password('password', 'admin')
);

save_settings($settings, $password_file);
?>