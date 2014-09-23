#!/bin/bash

update_script=update-adblock.sh
cwd=$( cd "$( dirname "$0" )" && pwd )
echo $cwd
source_script=$cwd/$update_script

cron_script=/etc/cron.weekly/$update_script

if [ ! -f $cron_script ]; then
	ln -sf $source_script $cron_script
fi

$source_script
