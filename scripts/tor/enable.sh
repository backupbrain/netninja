#!/bin/bash

OPTIND=1 # reset in case getopts have been used previously in the shell

external_interface=eth0
local_interface=wlan1

function show_help {
        local me=`basename $0`
        echo 'Usage: '$me' --external_interface=<inet interface> --local_interface=<inet interface>'
}


# shut down VPN
function stop_vpn {
	sudo service openvpn stop
	sudo update-rc.d openvpn disable
}

# clear routing table
function clear_routing {
	sudo iptables -F
	sudo iptables -X
	sudo iptables -t nat -F
	sudo iptables -t nat -X
	sudo iptables -t mangle -F
	sudo iptables -t mangle -X
	sudo iptables -P INPUT ACCEPT
	sudo iptables -P FORWARD ACCEPT
	sudo iptables -P OUTPUT ACCEPT
}

# set up VPN
function start_tor {
	sudo service tor start
	sudo update-rc.d tor enable
}

# set up tor routing
function tor_routing {
	local external_interface=$1
	local local_interface=$2
	sudo iptables -t nat -A POSTROUTING -o $external_interface -j MASQUERADE
	sudo iptables -A FORWARD -i $external_interface -o $local_interface -m state --state RELATED,ESTABLISHED -j ACCEPT
	sudo iptables -A FORWARD -i $local_interface -o $external_interface -j ACCEPT

	sh -c "iptables-save > /etc/iptables.ipv4.nat"
	
	service ntp restart
}


for i in "$@"
do
        case $i in
        --help)
                show_help
                exit 0
                ;;
	--external_interface=*)
		external_interface="${i#*=}"
		shift
		;;
	--local_interface=*)
		local_interface="${i#*=}"
		shift
		;;
	esac
done

shift $((OPTIND-1))

[ "$1" = "--" ] && shift

stop_vpn
clear_routing
start_tor
tor_routing $external_interface $local_interface

