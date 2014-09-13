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
	
	# this message is routed to stderr so we don't have to test for it
	local testString="$interface:      no wireless extensions.\n"

	local result=$(/sbin/iwconfig $interface)


	# check for null string:
	# http://timmurphy.org/2010/05/19/checking-for-empty-string-in-bash/
	if [[ -z "$result" ]];then
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

echo $has_wifi_result | tr -d "\n"
