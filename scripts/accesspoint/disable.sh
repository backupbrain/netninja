#!/bin/bash

accesspoint_interface=wlan1

update-rc.d bind9 disable 
update-rc.d hostapd disable 
update-rc.d isc-dhcp-server disable 

service bind9 stop
service hostapd stop
service isc-dhcp-server stop

ifdown $accesspoint_interface
