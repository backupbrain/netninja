#!/bin/bash

OPTIND=1 # reset in case getopts have been used previously in the shell

interface=eth0

function show_help {
	local me=`basename $0`
	echo 'Usage: '$me' --interface=<inet interface>'
}

# http://stackoverflow.com/a/246523
function list_connections {
	local interface=$1
	arp | awk '/'$interface'$/ {print $1,$3}' | sed 's/:[[:digit:]]\+$//'
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

list_connections $interface


