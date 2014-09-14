#!/bin/bash

OPTIND=1 # reset in case getopts have been used previously in the shell

external_interface=eth0
local_interface=wlan1

function show_help {
        local me=`basename $0`
        echo 'Usage: '$me' --internal_interface=<inet interface> --external_interface=<inet interface>'
}


# shut down VPN and TOR
function stop_tor {
	service tor stop
	update-rc.d tor disable
}
function stop_vpn {
	service openvpn stop
	update-rc.d openvpn disable
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



# set up local routing
function local_routing {
	local external_interface=$1
	local local_interface=$2
	iptables -t nat -A POSTROUTING -o $external_interface -j MASQUERADE
	iptables -A FORWARD -i $external_interface -o $local_interface -m state --state RELATED,ESTABLISHED -j ACCEPT
	iptables -A FORWARD -i $local_interface -o $external_interface -j ACCEPT
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

stop_tor
stop_vpn
clear_routing
local_routing $external_interface $local_interface
