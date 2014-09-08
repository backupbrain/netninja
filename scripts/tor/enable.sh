#!/bin/bash

OPTIND=1 # reset in case getopts have been used previously in the shell

local_interface=wlan1

function show_help {
        local me=`basename $0`
        echo 'Usage: '$me' --interface=<inet interface>'
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
	local local_interface=$1
	sudo iptables -t nat -A POSTROUTING -o tun0 -j MASQUERADE
	sudo iptables -A FORWARD -i tun0 -o $local_interface -m state --state RELATED,ESTABLISHED -j ACCEPT
	sudo iptables -A FORWARD -i $local_interface -o tun0 -j ACCEPT

	sh -c "iptables-save > /etc/iptables.ipv4.nat"
}


for i in "$@"
do
        case $i in
        --help)
                show_help
                exit 0
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
tor_routing $local_interface

