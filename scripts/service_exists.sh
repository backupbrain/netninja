#!/bin/bash

OPTIND=1 # reset in case getopts have been used previously in the shell

service=unknownservice

function show_help {
        local me=`basename $0`
        echo 'Usage: '$me' --service=<service name>'
}


function check_service {
	local service=$1
	check_service_return=0
	if P=$(pgrep $service)
	then
		check_service_return=1
	else
		check_service_return=0
	fi
}


for i in "$@"
do
        case $i in
        --help)
                show_help
                exit 0
                ;;
        --service=*)
                service="${i#*=}"
                shift
                ;;
        esac
done



shift $((OPTIND-1))

[ "$1" = "--" ] && shift

check_service $service

echo $check_service_return
