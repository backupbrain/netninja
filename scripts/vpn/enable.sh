#!/bin/bash

OPTIND=1 # reset in case getopts have been used previously in the shell

local_interface=wlan1

function show_help {
        local me=`basename $0`
        echo 'Usage: '$me' --interface=<inet interface>'
}


# shut down VPN
function stop_tor {
	service tor stop
	update-rc.d tor disable
}

# clear routing table
function clear_routing {
	iptables -F
	iptables -X
	iptables -t nat -F
	iptables -t nat -X
	iptables -t mangle -F
	iptables -t mangle -X
	iptables -P INPUT ACCEPT
	iptables -P FORWARD ACCEPT
	iptables -P OUTPUT ACCEPT
}


# set up VPN
function start_vpn {
	service openvpn start
	update-rc.d openvpn enable
}

# set up vpn routing
function vpn_routing {
	local local_interface=$1
	iptables -t nat -A POSTROUTING -o tun0 -j MASQUERADE
	iptables -A FORWARD -i tun0 -o $local_interface -m state --state RELATED,ESTABLISHED -j ACCEPT
	iptables -A FORWARD -i $local_interface -o tun0 -j ACCEPT
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
	--local_interface=*)
		local_interface="${i#*=}"
		shift
		;;
	esac
done

shift $((OPTIND-1))

[ "$1" = "--" ] && shift

stop_tor
clear_routing
start_vpn
vpn_routing $local_interface
