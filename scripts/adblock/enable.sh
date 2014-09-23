#!/bin/bash

update_script=update-adblock.sh
cwd=`DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"`
echo $cwd
source_script=$cwd/$update_script

cron_script=/etc/cron.weekly/$update_script

ln -s $source_script $cron_script

$source_script