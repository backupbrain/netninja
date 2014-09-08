<?php
require_once("common.php");

$authorized = true;

$postdata = null;


if ($_POST) {
        $postdata = $_POST;
}

if ($postdata) {
	$username = $postdata['username'];
	$hashed_password = hash_password($postdata['password'], $username);

	$real_username = get_setting($password_file, "username");
	$real_hashed_password = get_setting($password_file, "password");

	

	if ($username != $real_username) {
		$authorized = false;
	}
	if ($hashed_password != $real_hashed_password) {
		$authorized = false;
	}

	if ($authorized) {
		// log the user in
		login($username, $hashed_password);
		
		if (check_login()) {
			header("Location: settings.php");
		}
	} else {
		logout();
		$response = array(
			"error" => array(
				"unauthorized" => true
			)
		);
	}
	

}

?>
