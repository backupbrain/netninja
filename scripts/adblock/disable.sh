#!/bin/bash

update_script=update-adblock.sh
cwd=`DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"`

ad_file="/etc/bind/named.conf.local"

cron_script=/etc/cron.weekly/$update_script

if [ -f $cron_script ]; then
	rm $cron_script
fi
echo "" > $ad_file

# remove zone list

service bind9 stop
update-rc.d bind9 disable
