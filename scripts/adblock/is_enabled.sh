#!/bin/bash

update_script=update-adblock.sh
source_script=$cwd/$update_script


cron_script=/etc/cron.weekly/$update_script


if [ -f $cron_script ]; then
	result=1
else
	result=0
fi

echo $result | tr -d "\n"


#sed -i -e "s/\(option domain-name-servers \).*\$/\1192.168.10.1;/" $dhcp_file

#update-rc.d bind9 enable
#$source_script
