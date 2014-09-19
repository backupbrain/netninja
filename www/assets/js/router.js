
var changes = {
	'internet':false,
	'accesspoint':false,
	'vpn':false,
	'security':false	
};
var success = {
	'internet':false,
	'accesspoint':false,
	'vpn':false,
	'security':false	
}

$( document ).ready(function() {


	$('#navtap a').click(function (e) {
	  e.preventDefault();
	  $(this).tab('show');
	})	
	


	$('#savesettings').click(function (e) {
	  e.preventDefault();
	  savesettings();
	})	



	if ($("#wifi_enabled").is(':checked')) {
		$("#wifi-group").show(0);
	} else {
		$("#wifi-group").hide(0);
	}
	

	
	if ($("#vpntype-private").is(':checked')) {
		$("#private-vpn-group").show(50);
	} else {
		$("#private-vpn-group").hide(50);
	}
	
	$("#vpntype-none").change(function() {
		if ($("#vpntype-none").is(':checked')) {
			$("#private-vpn-group").hide(50);
		}
	});
	
	$("#vpntype-tor").change(function() {
		if ($("#vpntype-tor").is(':checked')) {
			$("#private-vpn-group").hide(50);
		}
	});
	
	$("#vpntype-private").change(function() {
		if ($("#vpntype-private").is(':checked')) {
			$("#private-vpn-group").show(50);
		}
	});
	
	
	
	$("#wifi_enabled").change(function() {
		if ($("#wifi_enabled").is(':checked')) {
			$("#wifi-group").show(50);
		} else {
			$("#wifi-group").hide(50);
		}
	});
	
	$("#vpn_protocol_select_tcp").click(function(event) {
		value = 'tcp';
		$("#vpn_protocol_text").text(value.toUpperCase());
		$("#vpn_protocol").val(value);
	});
	
	$("#vpn_protocol_select_udp").click(function(event) {
		value = 'udp';
		$("#vpn_protocol_text").text(value.toUpperCase());
		$("#vpn_protocol").val(value);
	});
	
	
	$("#wifi_encryption_wpa").click(function(event) {
		value = 'wpa';
		$("#wifi_encryption_text").text(value.toUpperCase());
		$("#wifi_encryption").val(value);
		$("#client_wifi_password").show(50);
	});
	
	$("#wifi_encryption_wep").click(function(event) {
		value = 'wep';
		$("#wifi_encryption_text").text(value.toUpperCase());
		$("#wifi_encryption").val(value);
		$("#client_wifi_password").show(50);
	});
	
	$("#wifi_encryption_none").click(function(event) {
		value = 'none';
		$("#wifi_encryption_text").text(value.toUpperCase());
		$("#wifi_encryption").val(value);
		$("#client_wifi_password").hide(50);
	});
	
	
	
	$("#accesspoint_channel_select_1").click(function(event) {
		value = 1;
		$("#accesspoint_channel_text").text('Channel '+value);
		$("#accesspoint_channel").val(value);
	});
	$("#accesspoint_channel_select_2").click(function(event) {
		value = 2;
		$("#accesspoint_channel_text").text('Channel '+value);
		$("#accesspoint_channel").val(value);
	});
	$("#accesspoint_channel_select_3").click(function(event) {
		value = 3;
		$("#accesspoint_channel_text").text('Channel '+value);
		$("#accesspoint_channel").val(value);
	});
	$("#accesspoint_channel_select_4").click(function(event) {
		value = 4;
		$("#accesspoint_channel_text").text('Channel '+value);
		$("#accesspoint_channel").val(value);
	});
	$("#accesspoint_channel_select_5").click(function(event) {
		value = 5;
		$("#accesspoint_channel_text").text('Channel '+value);
		$("#accesspoint_channel").val(value);
	});
	$("#accesspoint_channel_select_6").click(function(event) {
		value = 6;
		$("#accesspoint_channel_text").text('Channel '+value);
		$("#accesspoint_channel").val(value);
	});
	$("#accesspoint_channel_select_7").click(function(event) {
		value = 7;
		$("#accesspoint_channel_text").text('Channel '+value);
		$("#accesspoint_channel").val(value);
	});
	$("#accesspoint_channel_select_8").click(function(event) {
		value = 8;
		$("#accesspoint_channel_text").text('Channel '+value);
		$("#accesspoint_channel").val(value);
	});
	$("#accesspoint_channel_select_9").click(function(event) {
		value = 9;
		$("#accesspoint_channel_text").text('Channel '+value);
		$("#accesspoint_channel").val(value);
	});
	$("#accesspoint_channel_select_10").click(function(event) {
		value = 10;
		$("#accesspoint_channel_text").text('Channel '+value);
		$("#accesspoint_channel").val(value);
	});
	$("#accesspoint_channel_select_11").click(function(event) {
		value = 11;
		$("#accesspoint_channel_text").text('Channel '+value);
		$("#accesspoint_channel").val(value);
	});
	
	// know which section changed
	$(".tab-pane").find("input,textarea").click(function() {
		key = $(this).closest(".tab-pane").attr('id').substring("tab-".length);
		changes[key] =true;
	});
	
	$('#navtab a[href="#status"]').tab('show') // Select tab by name
	
	updateStatus();
});


function updateStatus() {

	post_url = "application/status.php";
	
	$.getJSON( post_url, "", function(data) {
		console.log(data);

		result = data.result

		console.log(result);
		if (result == "success") {
			success["status"] = true;
			
			$(".start_hidden").hide();
			
			response = data.response;
			
			$("#internet_connected").text(response.internet_connected);
			$("#firmware_version").text(response.firmware_version);
			
			wan = response.wan;
			for (iface in wan) {
				for (property in response.wan[iface]) {
					elementId = "#"+iface+"_"+property;
					element = $(elementId);

					$("#"+iface+"_"+property+"_value").text(response.wan[iface][property]);
					
					if (element.length) {
						element.show();
					}
				}
			}
			
			lan = response.lan;
			for (iface in lan) {
				for (property in response.lan[iface]) {
					elementId = "#"+iface+"_"+property;
					element = $(elementId);

					$("#"+iface+"_"+property+"_value").text(response.lan[iface][property]);
					
					if (element.length) {
						element.show();
					}
				}
			}
			
			
			services = response.services;
			for (serviceId in services) {
				service = services[serviceId];
				elementId = "#"+service+"_settings";
				$(elementId).show();
			}
			
			accesspoint_clients = response.accesspoint_clients;
			num_clients = accesspoint_clients.length;
			$("#accesspoint_client_number_value").text(num_clients);

			$('#access_point_clients tbody').empty();
			for (index in accesspoint_clients) {
				mac = accesspoint_clients[index].mac;
				ip = accesspoint_clients[index].ip;
				mac_cell = $("<td/>").text(mac);
				ip_cell = $("<td/>").text(ip);
				row = $("<tr/>");
				row.append(mac_cell);
				row.append(ip_cell);
				$('#access_point_clients tbody').append(row);
			}
			$("#access_point_clients").show();
			
			
		} else if (result == "warning"){
			notify_warning(data);
		} else {
			success["status"] = false;
			notify_error(data);
		}
		$( ".result" ).html( data );
		
	});
}


function savesettings() {
	clear_messages();
	
	if (changes.length <= 0) {
		updateStatus();
	}
	
	if (changes["internet"]) {
		saveInternet();
	}
	if (changes["accesspoint"]) {
		saveAccesspoint();
	}
	if (changes["vpn"] && !changes["internet"]) {
		saveVPN();
	}
	if (changes["security"]) {
		savePassword();
	}
}

function saveInternet() {
	notify_pending_change();
	
	wifi_enabled = $("#wifi_enabled").is(":checked");
	ssid = $("#client_wifi_ssid").val();
	password = $("#client_wifi_password").val();
	encryption = $("#wifi_encryption").val();
	

	formdata = {
		"wifi_enabled": wifi_enabled,
		"ssid": ssid,
		"password": password,
		"encryption": encryption
	}

	post_url = "application/internet.php";

		
	$.post(post_url, formdata, function( data ) {
		result = data.result
		if (result == "success") {
			success["internet"] = true;
			//notify_success(data);
			if (!changes["vpn"]) {
				saveVPN();
			}
		} else if (result == "warning"){
			notify_warning(data);
		} else {
			success["internet"] = false;
			notify_error(data);
		}
		$( ".result" ).html( data );		

	}, 'json');
	
	
}

function saveAccesspoint() {
	notify_pending_change();
	
	ssid = $("#accesspoint_wifi_ssid").val();
	is_hidden = $("#accesspoint_hidden").is(":checked");
	password = $("#accesspoint_wifi_password").val();
	password = $("#accesspoint_channel").val();
	

	formdata = {
		"ssid": ssid,
		"is_hidden": is_hidden,
		"password": password,
		"channel": channel
	}

	post_url = "application/accesspoint.php";

		
	$.post(post_url, formdata, function( data ) {
		result = data.result
		if (result == "success") {
			success["accesspoint"] = true;
			notify_success(data);
		} else if (result == "warning"){
			notify_warning(data);
		} else {
			success["accesspoint"] = false;
			notify_error(data);
		}
		$( ".result" ).html( data );
		updateStatus();
	}, 'json');
	

	
}

function saveVPN() {
	notify_pending_change();
	is_tor_on = $("#vpntype-tor").is(":checked");
	is_vpn_on = $("#vpntype-private").is(":checked");
	
	service = "none";
	if (is_tor_on) {
		service = "tor";
	} else if (is_vpn_on) {
		service = "private";
	}
	
	
	server = $("#vpn_server").val();
	port = $("#vpn_port").val();
	protocol = $("#vpn_protocol").val();
	username = $("#vpn_username").val();
	password = $("#vpn_password").val();
	ca_cert = $("#vpn_ca_cert").val();

	formdata = {
		"service": service,
		"server": server,
		"port": port,
		"protocol": protocol,
		"username": username,
		"password": password,
		"ca_cert": ca_cert
	}

	post_url = "application/vpn.php";

		
	$.post(post_url, formdata, function( data ) {
		result = data.result
		if (result == "success") {
			success["vpn"] = true;
			notify_success(data);
		} else if (result == "warning"){
			notify_warning(data);
		} else {
			success["vpn"] = false;
			notify_error(data);
		}
		$( ".result" ).html( data );
		updateStatus();
	}, 'json');
	
	
}


function savePassword() {
	oldPassword = $("#old_password").val();
	newPassword = $("#new_password").val();
	newPassword_verify = $("#new_password_verify").val();
	
	if (!oldPassword && !newPassword && !newPassword_verify) {
		return;
	}

	notify_pending_change();
	
	formdata = {
		"old_password": oldPassword,
		"new_password": newPassword,
		"new_password_verify": newPassword_verify
	}

	post_url = "application/security.php";
		
	$.post(post_url, formdata, function( data ) {
		result = data.result
		if (result == "success") {
			success["security"] = true;
			notify_success(data);
		} else if (result == "warning"){
			notify_warning(data);
		} else {
			success["security"] = false;
			notify_error(data);
		}
		$( ".result" ).html( data );
	}, 'json');
	
	
	$("#old_password").val("");
	$("#new_password").val("");
	$("#new_password_verify").val("");
	
}

function notify_error(data) {
	$("#pendingchange_banner").hide();
	$("#error_banner").show();
	if (data.response.length) {
		formErrors = data.response.errors
		if (formErrors["unauthorized"] == true) {
			document.location.href = "/";
			return;
		}
		for (formError in formErrors) {
			$("#error-"+formError).show();
		}	
	}
}
function notify_success(data) {
	ready = true;
	// figure out which changes were made
	for (index in changes) {
		if (success[index] != changes[index]) {
			ready = false;
		}
	}
	if (ready) {
		// reset change index
		for (index in changes) {
			changes[index] = false;
			success[index] = false;
		}

		$("#pendingchange_banner").hide();
		$("#success_banner").show(50);
	}
}
function notify_pending_change() {
	$("#pendingchange_banner").show(50);
}
function notify_warning() {
	
}
function clear_messages() {
	$(".input-error").hide();
}
function show_message_banner() {
	
}