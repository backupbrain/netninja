<?php
	
require_once('common.php');

$scrubbed_factory_password = escapeshellarg($factory_password);

`sudo ../../scripts/change_password.sh --password=$scrubbed_factory_password`;

$settings = array(
	'username' => 'admin',
	'password' => hash_password($factory_password, 'admin')
);



save_settings($settings, 'application/'.$password_file);
?>