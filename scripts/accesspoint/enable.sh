#!/bin/bash

accesspoint_interface=wlan1

update-rc.d bind9 enable 
update-rc.d hostapd enable 
update-rc.d isc-dhcp-server enable 

ifup $accesspoint_interface

service bind9 start
service hostapd start
service isc-dhcp-server start

