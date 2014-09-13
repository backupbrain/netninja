#!/bin/bash

OPTIND=1 # reset in case getopts have been used previously in the shell

interface=eth0

function show_help {
        local me=`basename $0`
        echo 'Usage: '$me' --interface=<inet interface>'
}

function check_interface_exists {
	local interface=$1

	check_interface_exists_result=0
	local testString="$interface: link beat detected"

	local result=$(/usr/sbin/ifplugstatus -a |grep -E "$interface" |grep -e ": link beat detected")



	if [ "$result" = "$testString" ]
	then
		check_interface_exists_result=1
	else
		check_interface_exists_result=0
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

check_interface_exists $interface

echo $check_interface_exists_result | tr -d "\n"
