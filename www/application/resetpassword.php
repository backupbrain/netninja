<?php
	
ini_set('display_errors',1);
error_reporting(E_ALL & !E_NOTICE);
require_once('common.php');


$settings = array(
	'username' => 'admin',
	'password' => hash_password($factory_password, 'admin')
);

save_settings($settings, $password_file);
?>