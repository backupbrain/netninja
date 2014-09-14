#!/bin/bash

test "$(whoami)" != 'root' && (echo you are using a non-privileged account; exit 1)


OPTIND=1 # reset in case getopts have been used previously in the shell

readonly config_file=/etc/network/interfaces
ssid="snoopsafe"
passphrase=""
encryption=""

function show_help {
	local me=`basename $0`
	echo 'Usage: '$me' --ssid="<ACCESS POINT NAME>" --passphrase="<ACCESS POINT PASSWORD>" --encryption=none/wep/wpa'
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
		passphrase="${i#*=}"
		shift
		;;
	--encryption=*)
		encryption="${i#*=}"
		shift
		;;
	
	esac
done

shift $((OPTIND-1))

[ "$1" = "--" ] && shift

# lowercase the encryption
encryption=`echo $encryption | tr '[A-Z]' '[a-z']`

#echo "ssid=$ssid, wpa_passsphrase=$wpa_passphrase', Leftovers: $@"

# replace the ssd and the passphrase in the hostapd config file
sed -i "s/\(wpa-ssid \).*\$/\1\"$ssid\"/" $config_file
sed -i "s/\(wpa-psk \).*\$/\1\"$passphrase\"/" $config_file


sed -i "s/\(wpa-essid \).*\$/\1\"$ssid\"/" $config_file
sed -i "s/\(wpa-key \).*\$/\1\"$passphrase\"/" $config_file

if [[ $encryption = "wpa" ]]; then
	sed -i -e 's/^\twireless-essid/\#\twireless-essid/g' $config_file
	sed -i -e 's/^\twireless-key/\#\twireless-key/g' $config_file
	sed -i -e 's/^\twireless-mode/\#\twireless-mode/g' $config_file

	sed -i -e 's/^\#\twpa-ssid/\twpa-ssid/g' $config_file
	sed -i -e 's/^\#\twpa-psk/\twpa-psk/g' $config_file
elif [[ $encryption = "wep" ]]; then
	sed -i -e 's/^\twpa-ssid/\#\twpa-ssid/g' $config_file
	sed -i -e 's/^\twpa-psk/\#\twpa-psk/g' $config_file
	sed -i -e 's/^\twireless-mode/\#\twireless-mode/g' $config_file

	sed -i -e 's/^\#\twpa-essid/\twpa-essid/g' $config_file
	sed -i -e 's/^\#\twpa-key/\twpa-key/g' $config_file
else
	sed -i -e 's/^\twpa-ssid/\#\twpa-ssid/g' $config_file
	sed -i -e 's/^\twpa-psk/\#\twpa-psk/g' $config_file
	sed -i -e 's/^\twireless-key/\#\twireless-key/g' $config_file

	sed -i -e 's/^\#\twireless-essid/\twireless-essid/g' $config_file
	sed -i -e 's/^\#\twireless-mode/\twireless-mode/g' $config_file
fi
	

