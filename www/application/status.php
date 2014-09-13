<?php

require_once("common.php");

$source = "status";

$hostapd_file = '/etc/hostapd/hostapd.conf';

$external_interfaces = array(
	'eth0' => null,
	'wlan0' => null
);

$is_tor_running = intval(`sudo ../scripts/service_exists.sh --service=tor`);
$is_vpn_running = intval(`sudo ../scripts/service_exists.sh --service=openvpn`);
$services = array();
if ($is_tor_running) $services[] = "tor";
if ($is_vpn_running) {
	echo("VPN IS RUNNING");
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
	
	$isup = intval(`../../scripts/interface_exists.sh --interface=$interface`);
	if ($isup == 1) {
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

$response = array(
	"wan" => $external_interfaces,
	"lan" => $internal_interfaces,
	"services" => $services
);

success_response($response);



?>
