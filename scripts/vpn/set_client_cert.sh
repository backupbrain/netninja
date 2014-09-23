#!/bin/bash

test "$(whoami)" != 'root' && (echo you are using a non-privileged account; exit 1)


OPTIND=1 # reset in case getopts have been used previously in the shell

config_file=/etc/openvpn/client.conf
cert_file=/etc/openvpn/client.crt
key_file=/etc/openvpn/client.key

client_cert=""
client_key=""

function show_help {
	local me=`basename $0`
	echo 'Usage: '$me' --client-cert="<VPN Client Certificate>" --client-key="<VPN Client key>"'
}

for i in "$@"
do
	case $i in
	--help)
		show_help
		exit 0
		;;
	--client_cert=*)
		client_cert="${i#*=}"
		shift
		;;
	--client_key=*)
		client_key="${i#*=}"
		shift
		;;
	esac
done

shift $((OPTIND-1))

[ "$1" = "--" ] && shift

# replace the settings in the vpn config

if [[ -n "$client_cert" ] && [ -n "$client_key" ]]; then
	sed -i -e 's/^\#cert client.crt/cert client.crt/g' $config_file
	sed -i -e 's/^\#key client.key/key client.key/g' $config_file
	echo "$client_cert" > $cert_file
	echo "$client_key" > $key_file
else
	sed -i -e 's/^cert client.crt/\#cert client.crt/g' $config_file
	sed -i -e 's/^key client.key/\#key client.key/g' $config_file
	echo "" > $cert_file
	echo "" > $key_file
fi


