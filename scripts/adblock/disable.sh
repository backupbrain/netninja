#!/bin/bash

update_script=update-adblock.sh
cwd=`DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"`

ad_file="/etc/bind/named.conf.local"

cron_script=/etc/cron.weekly/$update_script

rm $cron_script
echo "" > $ad_file

# remove zone list

service bind restart