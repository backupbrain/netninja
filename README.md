Installing NetNinja Router
#######################

This tutorial presumes that you have a short-range wifi on wlan0 and a long-range wifi on wlan1

Drivers:
========

Edimax EW-7811 UN
-----------------

$ wget https://github.com/jenssegers/RTL8188-hostapd/archive/v1.1.tar.gz
$ tar -zxvf v1.1.tar.gz
$ cd RTL8188-hostapd-1.1/hostapd
$ sudo make
$ sudo make install


Installing Software:
====================

- hostapd for wifi hotspot
- isc-dhcp-server for dhcp server
- bind9 for name server and ad-blocking
- openvpn for vpn client
- tor for tor client


Set up Wifi Client:
-------------------
$ sudo nano -w /etc/network/interfaces

In /etc/network/interfaces, change settings for wlan0:
allow-hotplug wlan0
auto wlan0
iface wlan0 inet dhcp
	wpa-ssid "myhostnetwork"
	wpa-psk "myhostpassword"

$ sudo ifdown wlan0
$ sudo ifup wlan0

Setting up a DNS server:
========================

Install bind:

$ sudo apt-get install bind9

Configure DNS forwarding rules

$ sudo nano -w /etc/bind/named.conf.options

In /etc/bind/named.conf.options, add:

version "4.9.3"; # lie about the version
dnssec-validation auto;
auth-nxdomain no;    # conform to RFC1035
listen-on { 192.168.10.1; }; # restrict to internal network
forwarders {
	8.8.8.8;
	8.8.4.4;
};
allow-query {
	192.168.10/24;
	127.0.0.1;
};
allow-transfer {
	192.168.10/24;
	127.0.0.1;
};

Make a dummy-block.com zone to handle loopback domains:

$ sudo mkdir /etc/bind/zones
$ sudo nano -w /etc/bind/zones/dummy-block.com.zone

In /etc/bind/zones/dummy-block.com.zone:
$TTL 24h
@       IN SOA dnsserverdummy-block.com. webmaster.dummy-block.com. (
                  2003052800  86400  300  604800  3600 )
@       IN      NS   dnsserver.dummy-block.com.
@       IN      A    127.0.0.1
*       IN      A    127.0.0.1

We will be programming our bind server to route ad traffic to the dummy zone
For now:
$ sudo nano -w /etc/bind/named.conf.local

In /etc/bind/named.conf.local:
include "/etc/bind/rndc.key";
zone  "101com.com" {type master; file "/etc/bind/zones/dummy-block.com.zone"; };


Automatic Ad-block updates:
---------------------------


DHCP Server for connecting clients:
----------------------------------
We will tell our dhcp server about the IP address range it has control over,
That it is an authoritative server, and that the local dns is the 
one to check for domain names

$ sudo apt-get install isc-dhcp-server
$ sudo nano /etc/dhcp/dhcpd.conf

In /etc/dhcp/dhcp.conf, add
# option domain-name "example.org";
# option domain-name-servers ns1.example.org, ns2.example.org;
authoritative
subnet 192.168.10.0 netmask 255.255.255.0 {
 range 192.168.10.10 192.168.10.200;
 option broadcast-address 192.168.10.255;
 option routers 192.168.10.1;
 default-lease-time 600;
 max-lease-time 7200;
 option domain-name "netninjarouter.com";
 option domain-name-servers 192.168.10.1;
}

$ sudo nano -w /etc/default/isc-dhcp-server

in /etc/default/isc-dhcp-server, add:
INTERFACES="wlan1"


$ sudo ifdown wlan1
$ sudo sudo nano /etc/network/interfaces

In /etc/network/interfaces, change settings for wlan1:
iface wlan1 inet static
  address 192.168.10.1
  netmask 255.255.255.0
  
$ sudo service isc-dhcp-server start
$ sudo update-rc.d isc-dhcp-server enable


Wifi Hotstpot:
--------------

$ sudo apt-get install hostapd

$ sudo nano /etc/hostapd/hostapd.conf

In /etc/hostapd/hostapd.conf, change:
interface=wlan1
ssid=myroutername
wpa_passphrase=mySecurePassword
ignore_broadcast_ssid=0

$ sudo service hostapd start
$ sudo update-rc.d hostapd enable 



Routing:
--------

Enable IP routing:
$ sudo nano /etc/sysctl.conf

in /etc/sysctl.conf, add:
net.ipv4.ip_forward=1

$ sudo sh -c "echo 1 > /proc/sys/net/ipv4/ip_forward"
$ sudo ifup wlan1

Clear existing routing table
$ sudo iptables -F
$ sudo iptables -X
$ sudo iptables -t nat -F
$ sudo iptables -t nat -X
$ sudo iptables -t mangle -F
$ sudo iptables -t mangle -X
$ sudo iptables -P INPUT ACCEPT
$ sudo iptables -P FORWARD ACCEPT
$ sudo iptables -P OUTPUT ACCEPT

Set up routing for ethernet to wlan0:

$ sudo iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE
$ sudo iptables -A FORWARD -i eth0 -o wlan1 -m state --state RELATED,ESTABLISHED -j ACCEPT
$ sudo iptables -A FORWARD -i wlan1 -o eth0 -j ACCEPT

Save settings for next reboot
$ sudo sh -c "iptables-save > /etc/iptables.ipv4.nat"
$ sudo nano -w /etc/network/interfaces

In /etc/network/interfaces, add:
up iptables-restore < /etc/iptables.ipv4.nat

Installing TOR
--------------
$ sudo apt-get install tor

$ sudo service tor start
$ sudo update-rc.d tor enable 

Installing OpenVPN Client:
--------------------------

install openvpn:
$ sudo apt-get install openvpn

Download your client certificate
$ sudo wget https://netninja.com/settings/ca.crt
$ sudo mv ca.crt /etc/openvpn

Download your client settings
$ sudo wget https://netninja.com/settings/client.conf
$ sudo mv client.conf /etc/openvpn

Add your credentials
$ sudo echo myvpnusername > /etc/openvpn/auth.txt
$ sudo echo myvpnpassword >> /etc/openvpn/auth.txt


$ sudo service openvpn start
$ sudo update-rc.d openvpn enable 

Set up Apache/PHP for router administrative web site
-----------------------------------------------------
Install php5 and php5-sqlite

$ sudo apt-get install php5 php5-sqlite

Change apache to only listen on the local network:

$ sudo nano -w /etc/apache2/ports.conf

In http.conf, change 

Listen 80
Listen 443

to

Listen 192.168.10.1:80
Listen 192.168.10.1:443

$ sudo service apache2 restart



Set up web site supporting script permissions:
----------------------------------------------

You can use sudo:

<?php `sudo /your/script`; ?>

You should allow executing your script without password prompt. Run sudo visudo in console and add the following string to the end:

nobody ALL = NOPASSWD: /your/script

You must set up file mode properly to ensure that no one can modify this script and put dangerous contents into it:

$ sudo chown root:root /your/script
$ sudo chmod 755 /your/script

General Security
-----------------

Disable ping response:

$ sudo echo 1 >/proc/sys/net/ipv4/icmp_echo_ignore_all

Disable ssh:

$ update-rc.d ssh disable 

Turn off Apache signature:

$ sudo nano -w /etc/apache/apache2.conf

ServerSignature Off
ServerTokens Prod


$ sudo nano -w /etc/php5/apache2/php.ini

Turn off php signature
Turn off display_errors in php.ini



change max_execution_time to 120s

change session.name = PHPSESSID to router_sessionid

Speed up Bood process:
----------------------

Disable sound card

$ sudo nano -w /etc/modules

comment out 

# snd-bcm2835

Turn off unused services

$ sudo nano -w /boot/cmdline.txt

dwc_otg.lpm_enable=0 root=/dev/mmcblk0p2 rootfstype=ext4 rootflags=commit=120,data=writeback elevator=deadline noatime nodiratime  data=writeback rootwait quiet

$ sudo nano -w /etc/dhcp/dhclient.conf

change timeeout 10;

$ sudo nano -w /etc/sysctl.conf:
vm.dirty_background_ratio = 20
vm.dirty_expire_centisecs = 0
vm.dirty_ratio = 80
vm.dirty_writeback_centisecs = 1200
vm.overcommit_ratio = 2
vm.laptop_mode = 5
vm.swappiness = 10


