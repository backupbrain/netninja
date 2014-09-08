<?php
require_once("application/common.php");
if (!check_login()) {
	header("Location: /");
}


$wifi_ssid =  rtrim(`../scripts/wifi_client/get_ssid.sh`,"\n");
$wifi_password =  rtrim(`../scripts/wifi_client/get_password.sh`,"\n");

$wifi_on = intval(`../scripts/interface_exists.sh --interface=wlan0`);



$accesspoint_ssid = rtrim(`../scripts/report_setting.sh --file=/etc/hostapd/hostapd.conf --setting=ssid`,"\n");
$accesspoint_password = rtrim(`../scripts/report_setting.sh --file=/etc/hostapd/hostapd.conf --setting=wpa_passphrase`,"\n");



$is_tor_running = intval(`../scripts/service_exists.sh --service=tor`);
$is_vpn_running = intval(`../scripts/service_exists.sh --service=openvpn`);
$services = array();
if ($is_tor_running) $services[] = "tor";
if ($is_vpn_running) $services[] = "vpn";

$vpn_server = rtrim(`../scripts/vpn/get_setting.sh --setting=server`,"\n");
$vpn_port = rtrim(`../scripts/vpn/get_setting.sh --setting=port`,"\n");
$vpn_protocol = rtrim(`../scripts/vpn/get_setting.sh --setting=proto`,"\n");
$vpn_username = rtrim(`../scripts/vpn/get_auth_setting.sh --setting=username`,"\n");
$vpn_password = rtrim(`../scripts/vpn/get_auth_setting.sh --setting=password`,"\n");
$vpn_ca_cert = rtrim(`../scripts/vpn/get_ca_cert.sh`,"\n");

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
			<a class="navbar-brand" href="#">Router Admin</a>
		</div>


		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav" id="navtab">
				<li class="active">
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
			<div class="tab-pane active" id="tab-internet">
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


		<div>&nbsp;</div>
		<div class="input-group">
			<button class="btn btn-success" type="button" id="savesettings">Save Router
			Settings</button>
		</div>
	</div>
	<!-- /container -->
</body>
</html>