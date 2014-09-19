<?php

require_once("common.php");

$source = "accesspoint";

$channelmin = 1;
$channelmax = 11;

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
	$channel = intval($postdata['channel']);
	if ($channel < $channelmin) $channel = $channelmin;
	if ($channel > $channelmax) $channel = $channelmax;
	
	
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
	$scrubbed_channel = escapeshellarg($channel);
	
	if ($continue) {
		`sudo ../../scripts/accesspoint/disable.sh`;
		`sudo ../../scripts/accesspoint/configure.sh --ssid=$scrubbed_ssid --passphrase=$scrubbed_password --channel=$scrubbed_channel`;

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
