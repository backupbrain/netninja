<?php
session_start();

require_once("settings/settings.php");

function hash_password($password, $username) {
	return crypt($password, md5($username.$password));
}

function get_setting($file, $key) {
	$ini_array = parse_ini_file($file);

	$retval = $ini_array[$key];

	return $retval;
}

// http://stackoverflow.com/a/5695202
function save_settings($settingsarray, $file) {
	$res = array();
	foreach ($settingsarray as $key=>$val) {
		if (is_array($val)) {
			$res[] = "[$key]";
			foreach ($val as $skey => $sval) $res[] = _save_settings_format_keypair($skey, $sval);
		}
		else $res[] = _save_settings_format_kepyair($key, $val);
	}
	$output = implode("\r\n", $res);
	$retval = file_put_contents($file, $output);
	if ($retval === false) {
		trigger_error("Could not write to file: '".$file."'", E_USER_ERROR);
	}
}
function _save_settings_format_kepyair($key, $val) {
	$output = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
	return $output;
}


function login($username, $hashed_password) {
	$login_string = md5($username.$hashed_password);
	$_SESSION['username'] = $username;
	$_SESSION['login_string'] = $login_string;

	// drop a cookie
	/*
	$cookie_timeout = time() + (3600*30);
	setcookie("username", $username);
	setcookie("login_string", $login_string);
	/* */

}

function check_login() {
	$logged_in = true;

	if (!isset($_SESSION['login_string']) or !$_SESSION['login_string']) {
		$logged_in = false;
	}
	/*
	// read cookie parameters
	$username = $_COOKIE['username'];
	$login_string = $_COOKIE['login_string'];

	if (!$username or !$login_string) {
		$logged_in = false;
	}

	if ($username != $_SESSION['username']) {
		$logged_in = false;
	}
	if ($login_string != $_SESSION['login_string']) {
		$logged_in = false;
	}
	/* */

	return $logged_in;
}

function logout() {
	session_unset();

	/*
	// http://stackoverflow.com/a/2310591
	$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
	foreach($cookies as $cookie) {
		$parts = explode('=', $cookie);
		$name = trim($parts[0]);
		setcookie($name, '', time()-1000);
		setcookie($name, '', time()-1000, '/');
	}
	/* */
}


function success_response($response=null) {
	$output = array();
	$output["result"] = "success";
	if ($response) {
		$output["response"] = $response;
	}
	$json_response = json_encode($output);
	echo($json_response);
}

function error_response($response) {
	$output = array();
	$output["result"] = "error";
	if ($response) {
		$output["response"] = $response;
	}
	$json_response = json_encode($output);
	echo($json_response);
}


?>
