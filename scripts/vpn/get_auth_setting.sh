#!/bin/bash

OPTIND=1 # reset in case getopts have been used previously in the shell

config_file=/etc/openvpn/auth.txt
config_setting=username

function show_help {
	local me=`basename $0`
	echo 'Usage: '$me' --setting=<setting>'
}


function report_config {
	local config_setting=$1
	
	temp_result=`cat $config_file`

	IFS=' ' read -r username password <<< "$temp_result"
	
	if [[ $config_setting == 'username' ]]; then
		config_result=`cat $config_file |sed -n 1p`
	fi
	
	if [[ $config_setting == 'password' ]]; then
		config_result=`cat $config_file |sed -n 2p`
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