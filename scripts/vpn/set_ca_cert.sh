#!/bin/bash

test "$(whoami)" != 'root' && (echo you are using a non-privileged account; exit 1)


OPTIND=1 # reset in case getopts have been used previously in the shell

config_file=/etc/openvpn/client.conf
cert_file=/etc/openvpn/ca.crt

ca_cert=""

function show_help {
	local me=`basename $0`
	echo 'Usage: '$me' --ca-cert="<VPN CA Certificate>"'
}

for i in "$@"
do
	case $i in
	--help)
		show_help
		exit 0
		;;
	--ca_cert=*)
		ca_cert="${i#*=}"
		shift
		;;
	esac
done

shift $((OPTIND-1))

[ "$1" = "--" ] && shift

# replace the settings in the vpn config

if [[ -n "$ca_cert" ]]; then
	sed -i -e 's/^\#ca ca.crt/ca ca.crt/g' $config_file
	echo "$ca_cert" > $cert_file
else
	sed -i -e 's/^ca ca.crt/\#ca ca.crt/g' $config_file
	echo "" > $cert_file
fi


