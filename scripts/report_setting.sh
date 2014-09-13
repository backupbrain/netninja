#!/bin/bash

OPTIND=1 # reset in case getopts have been used previously in the shell

config_file=/etc/hostapd/hostapd.conf
config_setting=ssid

function show_help {
	local me=`basename $0`
	echo 'Usage: '$me' --file=<config file> --setting=<setting>'
}


function report_config {
	local config_file=$1
	local config_setting=$2
	config_result=`sed -n '/^'$config_setting'=\(.*\)$/s//\1/p' < $config_file | tr -d '\n'`
}



for i in "$@"
do
	case $i in
		--help)
			show_help
			exit 0
			;;
		--file=*)
			config_file="${i#*=}"
			shift
			;;
		--setting=*)
			config_setting="${i#*=}"
			shift
			;;
	esac
done

shift $((OPTIND-1))

[ "$1" = "--" ] && shift

report_config $config_file $config_setting

echo $config_result | tr -d '\n'