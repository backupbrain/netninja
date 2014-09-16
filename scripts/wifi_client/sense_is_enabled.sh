#!/bin/bash

config_file="/etc/network/interfaces"
wifi_client_interface=wlan0

wifi_on=0
if grep '^auto '$wifi_client_interface $config_file > /dev/null; then
	wifi_on=1
fi

echo $wifi_on | tr -d "\n"

