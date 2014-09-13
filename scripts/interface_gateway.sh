#!/bin/bash

OPTIND=1 # reset in case getopts have been used previously in the shell

interface=eth0

function show_help {
	local me=`basename $0`
	echo 'Usage: '$me' --interface=<inet interface>'
}

function device_gateway {
	local interface=$1
	route -n |awk '/^(0.0.0.0)/ {print $0}'| awk '/'$interface'$/ {print $2}' | tr -d '\n'
	
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

device_gateway $interface


