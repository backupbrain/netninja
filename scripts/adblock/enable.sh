#!/bin/bash

update_script=update-adblock.sh
cwd=$( cd "$( dirname "$0" )" && pwd )
echo $cwd
source_script=$cwd/$update_script


cron_script=/etc/cron.weekly/$update_script

dhcp_file="/etc/dhcp/dhcpd.conf"

if [ ! -f $cron_script ]; then
	ln -sf $source_script $cron_script
fi


sed -i -e "s/\(option domain-name-servers \).*\$/\1192.168.10.1/" $dhcp_file

update-rc.d bind9 enable
$source_script
