#!/bin/bash


OPTIND=1 # reset in case getopts have been used previously in the shell

config_file="/etc/network/interfaces"
wifi_client_interface=wlan0

function show_help {
        local me=`basename $0`
        echo 'Usage: '$me' --interface=<interface name>'
}



for i in "$@"
do
        case $i in
        --help)
                show_help
                exit 0
                ;;
        --interface=*)
                wifi_client_interface="${i#*=}"
                shift
                ;;
        esac
done



shift $((OPTIND-1))

[ "$1" = "--" ] && shift


wifi_on=0
if grep '^auto '$wifi_client_interface $config_file > /dev/null; then
	wifi_on=1
fi

echo $wifi_on | tr -d "\n"

