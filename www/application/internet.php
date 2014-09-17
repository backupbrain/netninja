<?php

require_once("common.php");

$source = "internet";

$internal_interface="wlan1";
$external_interface="eth0";

$allowed_encryptions = array("wpa", "wep", "none");

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
	
	$wifi_enabled = $postdata['wifi_enabled'];
	$ssid = $postdata['ssid'];
	$password = $postdata['password'];
	$encryption = strtolower(trim($postdata['encryption']));
	
	
	
	// we have to remove the last newline character
	$old_ssid = rtrim(`../../scripts/wifi_client/get_ssid.sh`, "\n");
	$old_password = rtrim(`../../scripts/wifi_client/get_password.sh`,"\n");
	
	if ($wifi_enabled) {

		$continue = true;

		// is the new SSID empty?
		if (!$ssid) {
			$formErrors["client_wifi_ssid"] = true;
			$continue = false;
		}

		
		if (!in_array($encryption, $allowed_encryptions)) {
			$formErrors["wifi_encryption"] = true;
			$continue = false;
		}
		

		// Do we need a password?
		if (($encryption != "none") && !$password) {
			$formErrors["client_wifi_password"] = true;
			$continue = false;
		}
		
		
		$scrubbed_ssid = escapeshellarg($ssid);
		$scrubbed_password = escapeshellarg($password);
		$scrubbed_encryption = escapeshellarg($encryption);
		
		if ($continue) {
			`sudo ../../scripts/wifi_client/disable.sh`;
			`sudo ../../scripts/wifi_client/configure.sh --ssid=$scrubbed_ssid --passphrase=$scrubbed_password --encryption=$scrubbed_encryption`;
			`sudo ../../scripts/wifi_client/enable.sh`;
			
			

			$is_tor_running = intval(`../scripts/service_exists.sh --service=tor`);
			$is_vpn_running = intval(`../scripts/service_exists.sh --service=openvpn`);
			$service = "none";
			if ($is_tor_running) $service = "tor";
			if ($is_vpn_running) $service = "private";
			
			
			// restart routing
			if ($service == "tor") {
				`sudo ../../scripts/disable_vpn_tor.sh`;
				`sudo ../../scripts/tor/enable.sh --interface=$internal_interface`;

			} else if ($service == "private") {
				`sudo ../../scripts/disable_vpn_tor.sh`;
				`sudo ../../scripts/vpn/enable.sh --interface=$internal_interface`;

			} else {
				// determine our working network interface
				$wlan0_exists = intval(`sudo ../scripts/interface_exists.sh --interface=wlan0`);
				$external_interface="eth0";
				if ($wlan0_exists) {
					$external_interface="wlan0";
				}
				`sudo ../../scripts/disable_vpn_tor.sh --internal_interface=$internal_interface --external_interface=$external_interface`;
			}
			
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
		
	} else {
		`sudo ../../scripts/wifi_client/disable.sh`;
		// give a response
		$response = "";
		success_response($response);
	}
}

?>
