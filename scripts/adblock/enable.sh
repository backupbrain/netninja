#!/bin/bash

update_script=update-adblock.sh
cwd=$( cd "$( dirname "$0" )" && pwd )
echo $cwd
source_script=$cwd/$update_script

cron_script=/etc/cron.weekly/$update_script

ln -s $source_script $cron_script

$source_script