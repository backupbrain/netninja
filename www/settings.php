<?php
require_once("application/common.php");
if (!check_login()) {
	header("Location: /");
}

$firmware_version = get_setting('application/'.$version_file, 'version');

$wifi_ssid =  `../scripts/wifi_client/get_ssid.sh`;
$wifi_password =  ""; //`../scripts/wifi_client/get_password.sh`;

$wifi_on = intval(`../scripts/interface_exists.sh --interface=wlan0`);
$wifi_encryption = `../scripts/wifi_client/sense_encryption.sh`;


$accesspoint_ssid = `../scripts/report_setting.sh --file=/etc/hostapd/hostapd.conf --setting=ssid`;
$accesspoint_hidden = `../scripts/report_setting.sh --file=/etc/hostapd/hostapd.conf --setting=ignore_broadcast_ssid`;
$accesspoint_password = ""; //`../scripts/report_setting.sh --file=/etc/hostapd/hostapd.conf --setting=wpa_passphrase`;



$is_tor_running = intval(`../scripts/service_exists.sh --service=tor`);
$is_vpn_running = intval(`../scripts/service_exists.sh --service=openvpn`);
$services = array();
if ($is_tor_running) $services[] = "tor";
if ($is_vpn_running) $services[] = "vpn";

$vpn_server = `../scripts/vpn/get_setting.sh --setting=server`;
$vpn_port = `../scripts/vpn/get_setting.sh --setting=port`;
$vpn_protocol = `../scripts/vpn/get_setting.sh --setting=proto`;
$vpn_username = `../scripts/vpn/get_auth_setting.sh --setting=username`;
$vpn_password = ""; //`../scripts/vpn/get_auth_setting.sh --setting=password`;
$vpn_ca_cert = ""; //rtrim(`../scripts/vpn/get_ca_cert.sh`,"\n");


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Router Admin</title>
	
	<meta charset="utf-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta content="width=device-width, initial-scale=1" name="viewport">
	
	<script src="assets/js/jquery-1.11.1.min.js"></script>
	
	<link href="assets/bootstrap-3.1.1-dist/css/bootstrap.min.css" rel=
	"stylesheet">
	<link href="assets/bootstrap-3.1.1-dist/css/bootstrap-theme.min.css" rel=
	"stylesheet">
	<script src="assets/bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
	
	<link href="assets/css/screen.css" rel="stylesheet">
	<script src="assets/js/router.js"></script>
</head>

<body>
	<a class="sr-only sr-only-focusable" href="#content">Skip to main
	content</a> <!-- Static navbar -->


	<div class="container">
		<div id="error_banner" class="input-error">
			<p>There were problems saving your configuration.
			Please check your settings.
			</p>
		</div>

		<div id="success_banner" class="input-error">
			<p>Your settings were saved
			</p>
		</div>


		<div id="pendingchange_banner" class="input-error">
			<p>Please wait while your changes are being saved...
			</p>
		</div>

	</div>

	<div class="navbar navbar-default navbar-static-top container">
		<div class="navbar-header">
			<button class="navbar-toggle" data-target=".navbar-collapse"
			data-toggle="collapse" type="button"><span class="sr-only">Toggle
			navigation</span> <span class="icon-bar"></span> <span class=
			"icon-bar"></span> <span class="icon-bar"></span></button>
			<a class="navbar-brand" href="/">Router Admin</a>
		</div>


		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav" id="navtab">
				<li class="active">
					<a data-toggle="tab" href="#tab-status">Status</a>
				</li>
				<li>
					<a data-toggle="tab" href="#tab-internet">Internet</a>
				</li>
				<li>
					<a data-toggle="tab" href="#tab-accesspoint">Access
					Point</a>
				</li>
				<li>
					<a data-toggle="tab" href="#tab-vpn">VPN</a>
				</li>
				<li>
					<a data-toggle="tab" href="#tab-security">Security</a>
				</li>
			</ul>


			<ul class="nav navbar-nav navbar-right">
				<li>
					<a href="logout.php">Logout</a>
				</li>
			</ul>
		</div>
		<!--/.nav-collapse -->
	</div>


	<div class="container">
		<!-- Tab panes -->


		<div class="tab-content">
			

			<div class="tab-pane active" id="tab-status">
				<h2>Status</h2>


				<div class="wrapper">
					<div class="content-main col-xs-6 col-md-4">
						<h3>Internet</h3>
						


					    <div class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th colspan="2">Internet</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Connected</td>
										<td id="internet_connected">loading...</td>
									</tr>
								</tbody>
							</table>
					    </div>



					    <div id="ethernet_settings" class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th colspan="2">Ethernet</th>
									</tr>
									<tr>
										<th>Setting</th>
										<th>Value</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Connected</td>
										<td id="eth0_connected_value">loading...</td>
									</tr>
									<tr>
										<td>Type</td>
										<td id="eth0_type_value">loading...</td>
									</tr>
									<tr>
										<td>MAC Address</td>
										<td id="eth0_mac_value">loading...</td>
									</tr>
									<tr id="eth0_address" class="start_hidden">
										<td>IP Address</td>
										<td id="eth0_address_value">loading...</td>
									</tr>
									<tr id="eth0_gateway" class="start_hidden">
										<td>Gateway</td>
										<td id="eth0_gateway_value">loading...</td>
									</tr>
								</tbody>
							</table>
					    </div>


					    <div id="wifi_settings" class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th colspan="2">Wifi</th>
									</tr>
									<tr>
										<th>Setting</th>
										<th>Value</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Connected</td>
										<td id="wlan0_connected_value">loading...</td>
									</tr>
									<tr>
										<td>Type</td>
										<td id="wlan0_type_value">loading...</td>
									</tr>
									<tr id="wlan0_ssid" class="start_hidden">
										<td>Access Point Name</td>
										<td id="wlan0_ssid_value">loading...</td>
									</tr>
									<tr id="wlan0_bssid" class="start_hidden">
										<td>Access Point MAC</td>
										<td id="wlan0_bssid_value">loading...</td>
									</tr>
									<tr id="wlan0_channel" class="start_hidden">
										<td>Access Point Channel</td>
										<td id="wlan0_channel_value">loading...</td>
									</tr>
									<tr>
										<td>MAC Address</td>
										<td id="wlan0_mac_value">loading...</td>
									</tr>
									<tr id="wlan0_address" class="start_hidden">
										<td>IP Address</td>
										<td id="wlan0_address_value">loading...</td>
									</tr>
									<tr id="wlan0_gateway" class="start_hidden">
										<td>Gateway</td>
										<td id="wlan0_gateway_value">loading...</td>
									</tr>
								</tbody>
							</table>
					    </div>

						
						
						<div id="tor_settings" class="start_hidden">
							<h3>TOR</h3>
						    <div class="table-responsive">
								<table class="table table-bordered">
									<thead>
										<tr>
											<th colspan="2">TOR</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>Connected</td>
											<td id="vpn_type_status">loading...</td>
										</tr>
									</tbody>
								</table>
						    </div>
						</div>
						
						<div id="vpn_settings" class="start_hidden">
							<h3>VPN</h3>
						    <div class="table-responsive">
								<table class="table table-bordered">
									<thead>
										<tr>
											<th colspan="2">VPN</th>
										</tr>
										<tr>
											<th>Setting</th>
											<th>Value</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>Connected</td>
											<td id="tun0_connected_value">loading...</td>
										</tr>
										<tr id="tun0_address" class="start_hidden">
											<td>IP Address</td>
											<td id="tun0_address_value">loading...</td>
										</tr>
										<tr id="tun0_gateway" class="start_hidden">
											<td>Gateway</td>
											<td id="tun0_gateway_value">loading...</td>
										</tr>
									</tbody>
								</table>
						    </div>
						</div>
						
					</div><!-- #content-main -->
					<div class="content-secondary col-xs-6 col-md-4">
						<h3>Router</h3>
						

					    <div id="ethernet_settings" class="table-responsive">
							<table class="table table-bordered">
								<tbody>
									<tr>
										<td>Version</td>
										<td id="firmware_version"><?php echo $firmware_version; ?></td>
									</tr>
								</tbody>
							</table>
					    </div>
						
						
				  		<h3>Access Point</h3>
						

					    <div id="access_point_settings" class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>Setting</th>
										<th>Value</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Type</td>
										<td id="wlan1_type_value">loading...</td>
									<tr id="wlan1_ssid">
										<td>Name</td>
										<td id="wlan1_ssid_value">loading...</td>
									<tr id="wlan1_channel">
										<td>Channel</td>
										<td id="wlan1_channel_value">loading...</td>
									<tr>
										<td>MAC Address</td>
										<td id="wlan1_mac_value">loading...</td>
									</tr>
									<tr id="wlan1_address">
										<td>IP Address</td>
										<td id="wlan1_address_value">loading...</td>
									<tr id="wlan1_hidden">
										<td>Hidden</td>
										<td id="wlan1_hidden_value">loading...</td>
									<tr id="wlan1_encryption">
										<td>Encryption</td>
										<td id="wlan1_encryption_value">loading...</td>
									</tr>
								</tbody>
							</table>
					    </div>
						
						
						
					</div><!-- #content-secondary -->
					
					

					<div class="content-tertiary col-xs-6 col-md-4">
						<h3>Clients</h3>
						

					    <div id="access_point_client_number" class="table-responsive">
							<table class="table table-bordered">
								<tbody>
									<tr>
										<td>Current Connections</td>
										<td id="accesspoint_client_number_value">loading...</td>
									</tr>
								</tbody>
							</table>
					    </div>
						

					    <div id="access_point_clients" class="table-responsive start_hidden">
							<table id="" class="table table-bordered">
								<thead>
									<tr>
										<th>MAC Address</th>
										<th>IP Address</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
					    </div>
						
						
						
					</div><!-- #content-secondary -->
					
				</div><!-- #wrapper -->



			</div>
			
			
			<div class="tab-pane" id="tab-internet">
				<h2>Internet Settings</h2>


				<div class="input-group">
					<label for="wifi_enabled"><input id="wifi_enabled" name="enablewifi" type="checkbox" value="1" <?php if ($wifi_on) {?>checked="true" <?php } ?>> Connect to the internet using
					WiFi</label>
				</div>


				<div id="wifi-group" style="display:none">
					<div class="input-group">
						<input id="client_wifi_ssid" class="form-control" placeholder=
						"SSID/Network Name" value="<?= addslashes($wifi_ssid); ?>" type="text">
					<div id="error-client_wifi_ssid" class="input-error">Not a valid network name</div>
					</div>


					<div class="input-group">
						<div class="dropdown">
						  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
						    <span id="wifi_encryption_text"><?php if ($wifi_encryption) { echo(strtoupper(htmlentities($wifi_encryption))); } else { ?>Encryption<?php } ?></span>
						    <span class="caret"></span>
						  </button>
						  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
						    <li role="presentation"><a role="menuitem" tabindex="-1" id="wifi_encryption_wpa" href="#">WPA</a></li>
						    <li role="presentation"><a role="menuitem" tabindex="-1" id="wifi_encryption_wep" href="#">WEP</a></li>
						    <li role="presentation"><a role="menuitem" tabindex="-1" id="wifi_encryption_none" href="#">None</a></li>
						  </ul>
						</div>
						
						<input id="wifi_encryption" class="form-control" placeholder="WPA, WEP, or None"  value="<?= addslashes($wifi_encryption); ?>" type="hidden">

						<div id="error-vpn_protocol" class="input-error">Invalid protocol</div>
					</div>

					<div class="input-group">
						<input id="client_wifi_password" class="form-control" placeholder="password"
						value="<?= addslashes($wifi_password); ?>" type="password">
					<div id="error-client_wifi_password" class="input-error">Not a valid password</div>
					</div>
					
					
					

					<!--div class="input-group">
						<label for="mobilehotspot"><input id="mobilehotspot"
						name="mobilehotspot" type="checkbox" value="1"> Phone
						Hotspot Compatibility Mode</label>
					</div-->
					
				</div>
			</div>


			<div class="tab-pane" id="tab-accesspoint">
				<h2>Access Point Settings</h2>


				<p>Set up your private access point</p>


				<div class="input-group">
					<input id="accesspoint_wifi_ssid" class="form-control" placeholder="SSID/Network Name" value="<?= addslashes($accesspoint_ssid); ?>"
					type="text">

					<div id="error-accesspoint_wifi_ssid" class="input-error">Not a valid network name</div>
				</div>


				<div class="input-group">
					<input id="accesspoint_wifi_password" class="form-control" placeholder="password" value="<?= addslashes($accesspoint_password); ?>" type="password">

					<div id="error-accesspoint_wifi_password" class="input-error">Not a valid password</div>
				</div>
				


				<div class="input-group">
					<label for="accesspoint_hidden"><input id="accesspoint_hidden" name="accesspoint_hidden" type="checkbox" value="1" <?php if ($accesspoint_hidden) {?>checked="true" <?php } ?>> Hide this access point</label>
				</div>
			</div>


			<div class="tab-pane" id="tab-vpn">
				<h2>VPN Settings</h2>


				<p>A VPN will tunnel all your internet traffic to another
				computer, making it difficult to see what web sites you are
				surfing.</p>


				<p>TOR tunnels each individual packet of traffic randomly to a
				different VPN.</p>


				<div class="input-group">
					<label for="vpntype-none"><input id="vpntype-none" name=
					"vpntype" type="radio" value="none" <?php if (count($services) <= 0) { ?>checked="true"<?php } ?>> No VPN</label>
				</div>


				<div class="input-group">
					<label for="vpntype-private"><input id="vpntype-private"
					name="vpntype" type="radio" value="private" <?php if (in_array("vpn", $services)) { ?>checked="true"<?php } ?>> Private
					VPN</label>
				</div>


				<div id="private-vpn-group">
					<div class="input-group">
						<input id="vpn_server" class="form-control" placeholder=
						"VPN Server address" value="<?= addslashes($vpn_server); ?>" type="text">

						<div id="error-vpn_server" class="input-error">Invalid server address</div>
					</div>

					<div class="input-group">
						<input id="vpn_port" class="form-control" placeholder=
						"port e.g. 1194"  value="<?= addslashes($vpn_port); ?>" type="text">

						<div id="error-vpn_port" class="input-error">Invalid port</div>
					</div>


					<div class="input-group">
						<div class="dropdown">
						  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
						    <span id="vpn_protocol_text"><?php if ($vpn_protocol) { echo(strtoupper(htmlentities($vpn_protocol))); } else { ?>Protocol<?php } ?></span>
						    <span class="caret"></span>
						  </button>
						  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
						    <li role="presentation"><a role="menuitem" tabindex="-1" id="vpn_protocol_select_tcp" href="#">TCP</a></li>
						    <li role="presentation"><a role="menuitem" tabindex="-1" id="vpn_protocol_select_udp" href="#">UDP</a></li>
						  </ul>
						</div>
						
						<input id="vpn_protocol" class="form-control" placeholder="TCP or UDP"  value="<?= addslashes($vpn_protocol); ?>" type="hidden">

						<div id="error-vpn_protocol" class="input-error">Invalid protocol</div>
					</div>


					<div class="input-group">
						<input id="vpn_username" class="form-control" placeholder="username"  value="<?= addslashes($vpn_username); ?>" type="text">

						<div id="error-vpn_username" class="input-error">Invalid username</div>
					</div>


					<div class="input-group">
						<input id="vpn_password" class="form-control" placeholder="password"  value="<?= addslashes($vpn_password); ?>" type="password">
						<div id="error-vpn_password" class="input-error">Invalid password</div>
					</div>


					<div class="input-group">
						<textarea id="vpn_ca_cert" class="form-control" placeholder=
						"cert text goes here"><?php echo(htmlentities($vpn_ca_cert)); ?></textarea>
					</div>
						<div id="error-vpn_ca_cert" class="input-error">Invalid certificate text</div>
				</div>


				<div class="input-group">
					<label for="vpntype-tor"><input id="vpntype-tor" name=
					"vpntype" type="radio" value="tor" <?php if (in_array("tor", $services)) { ?>checked="true"<?php } ?>> TOR</label>
				</div>
			</div>


			<div class="tab-pane" id="tab-security">
				<h2>Security Settings:</h2>


				<p>Change your router web administration password.</p>

				<form action="#" method="post" id="security-form">
					<div class="input-group">
						<input id="old_password" name="old_password" class="form-control" placeholder="current password"
						type="password">
						<div id="error-old_password" class="input-error">Password was invalid</div>
					</div>


					<div class="input-group">
						<input id="new_password" name="new_password" class="form-control" placeholder="new password"
						type="password">
						<div id="error-new_password" class="input-error">Password was invalid</div>
					</div>


					<div class="input-group">
						<input id="new_password_verify" name="new_password_verify" class="form-control" placeholder=
						"retype new password" type="password">
					</div>
					<div id="error-new_password_verify" class="input-error">New passwords didn't match</div>
				</form>
			</div>
		</div>


		<div class="clear">&nbsp;</div>
		<div class="input-group">
			<button class="btn btn-success" type="button" id="savesettings">Save Router
			Settings</button>
		</div>
	</div>
	<!-- /container -->
</body>
</html>