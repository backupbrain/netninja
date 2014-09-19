<?php

require_once("common.php");

$source = "upgrade";
	
$latest_version_file = get_setting($version_file, 'versionfile');

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
	

$current_version = get_setting($version_file, 'version');
$latest_version = file_get_contents($latest_version_file);

$upgrade_router = false;
if ($latest_version) {
	if(compareVersions($current_version, $latest_version)) {
		$upgrade_router = true;
	}
}

$version_compare = compareVersions($current_version, $latest_version);

	
$upgrade_os= rtrim(`../../scripts/upgrade/checkos.sh`,"\n");

if ($upgrade_router) {
	`sudo ../../scripts/upgrade/router.sh`;
}
if ($upgrade_os) {
	`sudo ../../scripts/upgrade/os.sh`;
}
$current_version = get_setting($version_file, 'version');
$latest_version = file_get_contents($latest_version_file);

if (!$latest_version) {
	$errors['internet'] = true;
	$response = array(
		"errors" => $errors
	);
	error_response($response);
	return;
}

$version_compare = compareVersions($current_version, $latest_version);

$response = array(
	'current_version' => $current_version,
	'latest_version' => $latest_version,
	'version_compare' => $version_compare
);


success_response($response);

?>
