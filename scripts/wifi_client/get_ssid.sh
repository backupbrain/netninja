#!/bin/bash

config_file="/etc/network/interfaces"
config_setting="wpa-ssid"


config_result=`sed -n '/^[[:space:]]'$config_setting' "\(.*\)"$/s//\1/p' < $config_file`


if [[ ! -z "$config_result" ]];then
	config_result=`sed -n '/^[[:space:]]'$config_setting' "\(.*\)"$/s//\1/p' < $config_file`
fi

echo $config_result

