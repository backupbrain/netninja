<?php

require_once("common.php");

$source = "security";

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
	
	
	$username = get_setting($password_file, "username");
	$newPassword = $postdata['new_password'];
	$newPassword_verify = $postdata['new_password_verify'];

	// is the newPassword empty?
	if (!$newPassword) {
		$formErrors["new_password"] = true;
		$authorized = false;
	}
	
	// check that the passwords match
	if ($newPassword != $newPassword_verify) {
		$formErrors["new_password_verify"] = true;
		$authorized = false;
	}

	// check the user's password
	$old_hashed_password = hash_password($postdata['old_password'], $username);
	$current_password_hash = get_setting($password_file, "password");
	if ($old_hashed_password != $current_password_hash) {
		$formErrors["old_password"] = true;
		$authorized = false;
	}

	// if everything is ok, change the password
	if ($authorized) {
		$hashed_newPassword = hash_password($newPassword, $username);
		$settings = array(
			"username" => get_setting($password_file, "username"),
			"password" => $hashed_newPassword
		);
		save_settings($settings, $password_file);

		// log the user in
		login($username, $hashed_newPassword);
		
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
