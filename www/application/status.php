<?php

require_once("common.php");

$source = "status";

$hostapd_file = '/etc/hostapd/hostapd.conf';

$external_interfaces = array(
	'eth0' => null,
	'wlan0' => null
);

$internet_connected = false;
$firmware_version = get_setting($version_file, 'version');

$is_tor_running = intval(`../../scripts/service_exists.sh --service=tor`);
$is_vpn_running = intval(`../../scripts/service_exists.sh --service=openvpn`);
$services = array();
if ($is_tor_running) $services[] = "tor";
if ($is_vpn_running) {
	$services[] = "vpn";
	$external_interfaces['tun0'] = null;
}


$internal_interfaces=array("wlan1"=>null);


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


// internet settings
foreach (array_keys($external_interfaces) as $interface) {
	$interface_status = $external_interfaces[$interface];
	
	$interface_status["connected"] = false;
	$interface_status["type"] = 'ethernet';
	$interface_status["mac"] = `../../scripts/interface_mac.sh --interface=$interface`;
	
	$is_wifi = intval(`../../scripts/is_wifi.sh --interface=$interface`);
	
	if ($is_wifi) {
		$interface_status["type"] = "wireless";
		$interface_status["ssid"] = false;
	}
	if ($interface == "vpn") {
		unset($interface_status["type"]);
	}
	
	$isup = intval(`../../scripts/interface_exists.sh --interface=$interface`);
	if ($isup == 1) {
		// vpn does not count as an internet connection
		if ($interface != "tun0") {
			$internet_connected = true;
		}
		$interface_status["connected"] = true;
		$interface_status["address"] = `../../scripts/interface_ip.sh --interface=$interface`;
		$interface_status["gateway"] = `sudo ../../scripts/interface_gateway.sh --interface=$interface`;
		
	
		if ($is_wifi) {
			$interface_status["ssid"] = `sudo ../../scripts/get_live_ssid.sh --interface=$interface`;
			$interface_status["bssid"] = `sudo ../../scripts/get_live_bssid.sh --interface=$interface`;
			$interface_status["channel"] = `sudo ../../scripts/get_live_ap_channel.sh --interface=$interface`;
			
		}
	}
	$external_interfaces[$interface] = $interface_status;
	
}

// local network

foreach (array_keys($internal_interfaces) as $interface) {
	$interface_status = $internal_interfaces[$interface];
	
	$interface_status["type"] = "wireless";
	$isup = intval(`../../scripts/interface_exists.sh --interface=$interface`);
	$interface_status["mac"] = `../../scripts/interface_mac.sh --interface=$interface`;

	if ($isup == 1) {
		$interface_status["connected"] = true;
		$interface_status["address"] = `../../scripts/interface_ip.sh --interface=$interface`;
	
		$is_wifi = intval(`../../scripts/is_wifi.sh --interface=$interface`);
	

		if ($is_wifi) {
			$interface_status["type"] = "wireless";
			$interface_status["ssid"] = `sudo ../../scripts/get_live_ssid.sh --interface=$interface`;
			$interface_status["channel"] = `sudo ../../scripts/get_live_ap_channel.sh --interface=$interface`;
			

			

			$interface_status['hidden'] = false;
			$is_hidden = intval(`../../scripts/report_setting.sh --file=$hostapd_file --setting=ignore_broadcast_ssid`);
			if ($is_hidden) {
				$interface_status['hidden'] = true;
			}
			
			$interface_status['encryption'] = `../../scripts/report_setting.sh --file=$hostapd_file --setting=wpa_pairwise`;
		}
	}

	$internal_interfaces[$interface] = $interface_status;

}

$accesspoint_clients = array();
$raw_connection_text = `sudo ../../scripts/accesspoint/connections.sh --interface=wlan1`;
echo($raw_connection_text);
$raw_connection_list = split($raw_connection_text, "\n");
foreach ($raw_connection_list as $raw_connection_pair) {
	$raw_connection = split($raw_connection_pair, " ");
	$macaddress = $raw_connection[0];
	$ipaddress = $raw_connection[1];
	$accesspoint_clients[$macaddress] = $ipaddress;
}

$response = array(
	"firmware_version" => $firmware_version,
	"internet_connected" => $internet_connected,
	"wan" => $external_interfaces,
	"lan" => $internal_interfaces,
	"accesspoint_clients" => $accesspoint_clients,
	"services" => $services
);

success_response($response);



?>
