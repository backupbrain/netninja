<?php

require_once("common.php");

$source = "version";

$authorized = true;


if (!check_login()) {
	$authorized = false;
	$formErrors["unauthorized"] = true;
	// give a response
	$response = array(
		"errors" => $formErrors
	);
	error_response($response);
	return;
}
	
$response = array(
	'version' => get_setting($version_file, 'version')
);

?>
