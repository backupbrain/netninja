#!/bin/bash

config_file="/etc/network/interfaces"
wireless_encryption="none"


wpa_enabled=`sed -n '/^[[:space:]]wpa-ssid "\(.*\)"$/s//\1/p' < $config_file`

wep_enabled=`sed -n '/^[[:space:]]wireless-key \(.*\)$/s//\1/p' < $config_file`

open_enabled=`sed -n '/^[[:space:]]wireless-mode \(.*\)$/s//\1/p' < $config_file`


if [[ ! -z "$wpa_enabled" ]];then
	wireless_encryption="wpa"
elif [[ ! -z "$open_enabled" ]]; then
	wireless_encryption="none"
else
	wireless_encryption="wep"
fi

echo $wireless_encryption

