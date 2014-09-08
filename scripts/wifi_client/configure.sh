#!/bin/bash

test "$(whoami)" != 'root' && (echo you are using a non-privileged account; exit 1)


OPTIND=1 # reset in case getopts have been used previously in the shell

ssid="snoopsafe"
wpa_passphrase="change this password"

function show_help {
	local me=`basename $0`
	echo 'Usage: '$me' --ssid="<ACCESS POINT NAME>" --passphrase="<ACCESS POINT PASSWORD>"'
}

for i in "$@"
do
	case $i in
	--help)
		show_help
		exit 0
		;;
	--ssid=*)
		ssid="${i#*=}"
		shift
		;;
	--passphrase=*)
		wpa_passphrase="${i#*=}"
		shift
		;;
	esac
done

shift $((OPTIND-1))

[ "$1" = "--" ] && shift

#echo "ssid=$ssid, wpa_passsphrase=$wpa_passphrase', Leftovers: $@"

# replace the ssd and the passphrase in the hostapd config file
sed -i "s/\(wpa-ssid \).*\$/\1\"$ssid\"/" /etc/network/interfaces
sed -i "s/\(wpa-psk \).*\$/\1\"$wpa_passphrase\"/" /etc/network/interfaces

