#!/bin/bash

OPTIND=1 # reset in case getopts have been used previously in the shell

readonly SETTING_VISIBLE=0
readonly SETTING_EMPTY=1
readonly SETTING_BLANK=2

readonly config_setting='ignore_broadcast_ssid'

setting=$SETTING_VISIBLE
hidden="false"
continue=false

function show_help {
	local me=`basename $0`
	echo 'Usage: '$me' --hidden=true/false'
}
function error_unknown_argument {
	echo "Error: Unknown argument."
	show_help
}

for i in "$@"
do
	case $i in
	--help)
		show_help
		exit 0
		;;
	--hidden=*)
		hidden="${i#*=}"
		shift
		;;
	esac
done

shift $((OPTIND-1))

[ "$1" = "--" ] && shift

if [[ $hidden == "false"]]; then
	setting=$SETTING_VISIBLE
	continue=true
else if [[ $hidden == "true"]]; then
	setting=$SETTING_EMPTY
	continue=true
else
	error_unknown_argument
fi

if [[$continue == true]]; then
	sed -i "s/^\($config_setting=\).*\$/\1$setting/" /etc/hostapd/hostapd.conf
fi





