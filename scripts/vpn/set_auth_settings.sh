#!/bin/bash

test "$(whoami)" != 'root' && (echo you are using a non-privileged account; exit 1)


OPTIND=1 # reset in case getopts have been used previously in the shell

config_file=/etc/openvpn/client.conf
auth_file=/etc/openvpn/auth.txt

username=""
password=""

function show_help {
	local me=`basename $0`
	echo 'Usage: '$me' --username="<VPN USERNAME>" --password="<VPN PASSWORD>"'
}

for i in "$@"
do
	case $i in
	--help)
		show_help
		exit 0
		;;
	--username=*)
		username="${i#*=}"
		shift
		;;
	--password=*)
		password="${i#*=}"
		shift
		;;
	esac
done

shift $((OPTIND-1))

[ "$1" = "--" ] && shift

# replace the settings in the vpn config

if [ -n "$username" ] && [ -n "$password" ]; then
	sed -i -e 's/^\#auth-user-pass auth.txt/auth-user-pass auth.txt/g' $config_file
	echo "$username" > $auth_file
	echo "$password" >> $auth_file
else
	sed -i -e 's/^auth-user-pass auth.txt/\#auth-user-pass auth.txt/g' $config_file
	echo "" > $auth_file
fi


