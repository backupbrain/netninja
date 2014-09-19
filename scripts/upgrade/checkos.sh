#!/bin/bash

updates=`apt-get upgrade -s | grep -i security`

result=0
if [[ ! -z "$updates" ]];then
	result=1
fi

echo $result | tr -d "\n"