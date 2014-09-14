#!/bin/bash

test "$(whoami)" != 'root' && (echo you are using a non-privileged account; exit 1)


wifi_client_interface=wlan0
config_file=/etc/network/interfaces

sed -i -e 's/^auto '$wifi_client_interface'/\#auto '$wifi_client_interface'/g' $config_file
#sed -i -e 's/^iface '$wifi_client_interface' inet dhcp/\#iface '$wifi_client_interface' inet dhcp/g' $config_file
#sed -i -e 's/^\twpa-ssid/\#\twpa-ssid/g' $config_file
#sed -i -e 's/^\twpa-psk/\#\twpa-psk/g' $config_file
#sed -i -e 's/^\twireless-essid/\#\wireless-essid/g' $config_file
#sed -i -e 's/^\twireless-mode/\#\twireless-mode/g' $config_file
#sed -i -e 's/^\twireless-key/\#\twireless-key/g' $config_file

ifdown $wifi_client_interface
