#!/bin/bash

test "$(whoami)" != 'root' && (echo you are using a non-privileged account; exit 1)


OPTIND=1 # reset in case getopts have been used previously in the shell

server="vpn-oregon.snoopsafebox.com"
port="1194"
proto="tcp"

function show_help {
	local me=`basename $0`
	echo 'Usage: '$me' --server="<VPN SERVER>" --port="<VPN PORT>" --proto="<VPN PROTOCOL>"'
}

for i in "$@"
do
	case $i in
	--help)
		show_help
		exit 0
		;;
	--server=*)
		server="${i#*=}"
		shift
		;;
	--port=*)
		port="${i#*=}"
		shift
		;;
	--proto=*)
		proto="${i#*=}"
		shift
		;;
	esac
done

shift $((OPTIND-1))

[ "$1" = "--" ] && shift

# replace the settings in the vpn config
sed -i "s/\(remote \).*\$/\1$server $port/" /etc/openvpn/client.conf
sed -i "s/\(proto \).*\$/\1$proto/" /etc/openvpn/client.conf

