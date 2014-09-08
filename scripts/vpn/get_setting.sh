#!/bin/bash

OPTIND=1 # reset in case getopts have been used previously in the shell

config_file=/etc/openvpn/client.conf
config_setting=proto

function show_help {
	local me=`basename $0`
	echo 'Usage: '$me' --setting=<setting>'
}


function report_config {
	local config_setting=$1
	
	config_lookup=$config_setting
	if [ $config_setting == "server" ] || [ $config_setting == "port" ]; then
		config_lookup="remote"
	fi
	temp_result=`sed -n '/^'$config_lookup' \(.*\)$/s//\1/p' < $config_file`
	
	config_result=$temp_result
	if [[ $config_setting == 'server' ]]; then
		IFS=' ' read -r server port <<< "$temp_result"
		config_result="$server"
	fi
	
	if [[ $config_setting == 'port' ]]; then
		IFS=' ' read -r server port <<< "$temp_result"
		config_result="$port"
	fi
}



for i in "$@"
do
	case $i in
		--help)
			show_help
			exit 0
			;;
		--setting=*)
			config_setting="${i#*=}"
			shift
			;;
	esac
done

shift $((OPTIND-1))

[ "$1" = "--" ] && shift

report_config $config_setting

echo $config_result