<?php

require_once("common.php");

$source = "accesspoint";

$authorized = true;

$postdata = null;

if ($_POST) {
	$postdata = $_POST;
}


if ($postdata) {
	
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
	
	$ssid = $postdata['ssid'];
	$password = $postdata['password'];
	$is_hidden = $postdata['is_hidden'];
	
	
	$continue = true;

	// is the new SSID empty?
	if (!$ssid) {
		$formErrors["accesspoint_wifi_ssid"] = true;
		$continue = false;
	}


	// is the new password empty?
	if (!$password or (strlen($password) < 8)) {
		$formErrors["accesspoint_wifi_password"] = true;
		$continue = false;
	}
	
	$scrubbed_ssid = escapeshellarg($ssid);
	$scrubbed_password = escapeshellarg($password);
	
	if ($continue) {
		`sudo ../../scripts/accesspoint/disable.sh`;
		`sudo ../../scripts/accesspoint/configure.sh --ssid=$scrubbed_ssid --passphrase=$scrubbed_password`;

		$scrubbed_hidden_value = escapeshellarg("false");
		if ($is_hidden == "true") {
			$scrubbed_hidden_value = escapeshellarg("true");
		}
		`sudo ../../scripts/accesspoint/hidden.sh --hidden=$scrubbed_hidden_value`;
		
		`sudo ../../scripts/accesspoint/enable.sh`;
		// give a response
		$response = "";
		success_response($response);
	} else {
		// give a response
		$response = array(
			"errors" => $formErrors
		);
		error_response($response);
	}
		
}

?>
