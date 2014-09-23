#!/bin/bash

config_file="/etc/openvpn/client.crt"

if [ -f $config_file ]; then
	cat $config_file 
fi

