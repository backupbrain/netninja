#!/bin/bash

config_file="/etc/network/interfaces"
wpa_config_setting="wpa-ssid"
wep_config_setting="wireless-essid"


config_result=`sed -n '/^[[:space:]]'$wpa_config_setting' "\(.*\)"$/s//\1/p' < $config_file`


if [[ -z "$config_result" ]];then
	config_result=`sed -n '/^[[:space:]]*'$wep_config_setting' \(.*\)$/s//\1/p' < $config_file`
fi

echo $config_result

