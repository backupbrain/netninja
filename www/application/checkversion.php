<?php

require_once("common.php");

$source = "checkversion";
	
$latest_version_file = get_setting($version_file, 'versionfile');

$authorized = true;

function compareVersions($version1, $version2) {
	$version1 = explode(".", $version1);
	$version2 = explode(".", $version2);
	
	$version1_length = count($version1);
	$version2_length = count($version2);
	
	if ($version1_length > $version2_length) {
		for ($i=$version2_length; $i<$version1_length; $i++) {
			$version2[] = 0;
		}
	} else {
		for ($i=$version1_length; $i<$version2_length; $i++) {
			$version1[] = 0;
		}
	}
	
	foreach ($version1 as $key=>$version1_value) {
		$version1_value = intval($version1_value);
		$version2_value = intval($version2[$key]);
		if ($version1_value < $version2_value) {
			return -1;
		}
		if ($version1_value > $version2_value) {
			return 1;
		}
	}
	return 0;
}

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
