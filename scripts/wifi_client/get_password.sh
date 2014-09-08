#!/bin/bash

config_file="/etc/network/interfaces"
config_setting="wpa-psk"


config_result=`sed -n '/^[[:space:]]'$config_setting' "\(.*\)"$/s//\1/p' < $config_file`

echo $config_result

