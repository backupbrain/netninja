<?php

require_once("common.php");

$source = "accesspoint";

$internal_interface="wlan1";
$external_interface="eth0";

$allowed_protocols = array("udp", "tcp","none");

$authorized = true;

$postdata = null;

if ($_POST) {
	$postdata = $_POST;
}


if ($postdata) {

	$original_ca_cert = rtrim(`../../scripts/vpn/get_ca_cert.sh`,"\n");
	$old_password = `../../scripts/vpn/get_auth_setting.sh --setting=password`;
	$original_client_cert = rtrim(`../../scripts/vpn/get_client_cert.sh`,"\n");

	$old_is_adblocking_enabled = filter_var(`../../scripts/adblock/is_enabled.sh`, FILTER_VALIDATE_BOOLEAN);
	
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
	
	$service = $postdata['service'];
	
	$is_adblocking_enabled = filter_var($postdata['adblock'], FILTER_VALIDATE_BOOLEAN);
	$server = $postdata['server'];
	$port = intval($postdata['port']);
	$protocol = $postdata['protocol'];
	$username = $postdata['username'];
	$password = $postdata['password'];
	$ca_cert = $postdata['ca_cert'];
	$client_cert = $postdata['client_cert'];
	$client_key = $postdata['client_key'];
	
	
	$continue = true;
	$formErrors = array();
	
	

	if ($service == "tor") {
		`sudo ../../scripts/disable_vpn_tor.sh`;
		
		// determine our working network interface
		$wlan0_exists = intval(`sudo ../../scripts/interface_exists.sh --interface=wlan0`);
		$external_interface="eth0";
		if ($wlan0_exists) {
			$external_interface="wlan0";
		}
		
		`sudo ../../scripts/tor/enable.sh --external_interface=$external_interface --local_interface=$internal_interface`;
		$response = "";
		success_response($response);
	} else if ($service == "private") {
		$scrubbed_server = escapeshellarg($server);
		$scrubbed_port = escapeshellarg($port);
		$scrubbed_protocol = escapeshellarg($protocol);
		$scrubbed_username = escapeshellarg($username);
		$scrubbed_password = escapeshellarg($password);
		$scrubbed_ca_cert = escapeshellarg($ca_cert);
		
		
		if (!$server) {
			$formErrors["vpn_server"];
			$continue = false;
		}
		
		if (!is_int($port) or ($port > 65535) or ($port < 1)) {
			$formErrors["vpn_port"] = true;
			$continue = false;
		}
		
		if (!in_array($protocol, $allowed_protocols)) {
			$formErrors["vpn_protocol"] = true;
			$continue = false;
		}
		
		if ($username and !$password) {
			// presume we are keeping the old password
			$password = $old_password;
			$scrubbed_password = escapeshellarg($password);
		}
		if (!$username and $password) {
			$formErrors['vpn_username'] = true;
			$continue = false;
		}
		
		if ($original_ca_cert and !$ca_cert) {
			$ca_cert = $original_ca_cert;
			$scrubbed_ca_cert = escapeshellarg($ca_cert);
		}

		if (!$original_ca_cert and !$ca_cert) {
			$formErrors['vpn_ca_cert'] = true;
			$continue = false;
		}
		if (!$username and !$password and !$ca_cert) {
			$formErrors['vpn_ca_cert'] = true;
			$continue = false;
		}
		
		$scrubbed_client_cert = escapeshellarg($client_cert);
		$scrubbed_client_key = escapeshellarg($client_key);
		
		if ($continue) {
			`sudo ../../scripts/disable_vpn_tor.sh`;
			
			`sudo ../../scripts/vpn/set_auth_setting.sh --username=$scrubbed_username --password=$scrubbed_password`;
			

			`sudo ../../scripts/vpn/set_settings.sh --server=$scrubbed_server --port=$scrubbed_port --proto=$scrubbed_protocol`;
			
			if ($username and $password) {
				`sudo ../../scripts/vpn/set_auth_settings.sh --username=$scrubbed_username --password=$scrubbed_password`;
			}
			if ($ca_cert) {
				`sudo ../../scripts/vpn/set_ca_cert.sh --ca_cert=$scrubbed_ca_cert`;
			}
			
			//`sudo ../../scripts/vpn/set_client_cert.sh --client_cert=$scrubbed_client_cert -client_key=$scrubbed_client_key`;
			
			`sudo ../../scripts/vpn/enable.sh --interface=$internal_interface`;

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
		// determine our working network interface
		$wlan0_exists = intval(`sudo ../../scripts/interface_exists.sh --interface=wlan0`);
		$external_interface="eth0";
		if ($wlan0_exists) {
			$external_interface="wlan0";
		}
		`sudo ../../scripts/disable_vpn_tor.sh --internal_interface=$internal_interface --external_interface=$external_interface`;
		
		$response = "";
		success_response($response);	
	}

	
	
	if ($is_adblocking_enabled) {
		`sudo ../../scripts/adblock/enable.sh`;
	} else {
		`sudo ../../scripts/adblock/disable.sh`;	
	}
	// restart dhcp server to force client to re-connect when
	// adblock status changes
	if ($old_is_adblocking_enabled != $is_adblocking_enabled) {
		`sudo ../../scripts/accesspoint/disable.sh`;		
		`sudo ../../scripts/accesspoint/enable.sh`;
	}
		
}

?>
