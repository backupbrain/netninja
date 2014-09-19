#!/bin/bash

OPTIND=1 # reset in case getopts have been used previously in the shell

CHANNEL_MAX = 11
CHANNEL_MIN = 1

ssid="snoopsafe"
wpa_passphrase="change this password"
channel=1

function show_help {
	local me=`basename $0`
	echo 'Usage: '$me' --ssid="<ACCESS POINT NAME>" --passphrase="<ACCESS POINT PASSWORD>" --channel=integer'
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
	--channel=*)
		channel="${i#*=}"
		shift
		;;
	esac
done

shift $((OPTIND-1))

[ "$1" = "--" ] && shift

if [ "$channel" -lt $CHANNEL_MIN ] || [ "$channel" -gt $CHANNEL_MAX ]; then
	channel=1
fi

#echo "ssid=$ssid, wpa_passsphrase=$wpa_passphrase', Leftovers: $@"

# replace the ssd and the passphrase in the hostapd config file
sed -i "s/^\(ssid=\).*\$/\1$ssid/" /etc/hostapd/hostapd.conf
sed -i "s/^\(wpa_passphrase=\).*\$/\1$wpa_passphrase/" /etc/hostapd/hostapd.conf
sed -i "s/^\(channel=\).*\$/\1$channel/" /etc/hostapd/hostapd.conf




