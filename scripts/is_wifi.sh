#!/bin/bash

OPTIND=1 # reset in case getopts have been used previously in the shell

interface=eth0

function show_help {
        local me=`basename $0`
        echo 'Usage: '$me' --interface=<inet interface>'
}

function has_wifi {
	local interface=$1

	has_wifi_result=0
	local testString="$interface:\tno wireless extensions.\n"

	local result=$(/sbin/iwconfig $interface)



	if [ "$result" == "$testString" ];then
		has_wifi_result=0
	else
		has_wifi_result=1
	fi
}

for i in "$@"
do
        case $i in
        --help)
                show_help
                exit 0
                ;;
        --interface=*)
                interface="${i#*=}"
                shift
                ;;
        esac
done

shift $((OPTIND-1))

[ "$1" = "--" ] && shift

has_wifi $interface

echo $has_wifi_result
