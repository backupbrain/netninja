#!/bin/bash


OPTIND=1 # reset in case getopts have been used previously in the shell

username=admin
password=changethispassword

function show_help {
        local me=`basename $0`
        echo 'Usage: '$me' --password=<new password>'
}


for i in "$@"
do
        case $i in
        --help)
                show_help
                exit 0
                ;;
        --password=*)
                password="${i#*=}"
                shift
                ;;
        esac
done

shift $((OPTIND-1))

[ "$1" = "--" ] && shift


echo "$username:$password" | chpasswd

