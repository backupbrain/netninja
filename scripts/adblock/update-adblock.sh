#!/bin/bash
 
ad_list_url="http://pgl.yoyo.org/adservers/serverlist.php?hostformat=bind&showintro=0&mimetype=plaintext"
pixelserv_ip="192.168.10.1"
ad_file="/etc/bind/named.conf.local"
temp_ad_file="/tmp/named.conf.local"
 
curl $ad_list_url | awk '{print "zone \"" $0 "\" { type master; file \"zones/dummy-block.com.zone\"; };"}' > $temp_ad_file
 
if [ -f "$temp_ad_file" ]
then
	#sed -i -e '/www\.favoritesite\.com/d' $temp_ad_file
	mv $temp_ad_file $ad_file
else
	echo "Error building the ad list, please try again."
	exit
fi
 
service bind9 restart