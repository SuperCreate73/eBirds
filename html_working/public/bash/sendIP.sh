#!/bin/bash
# coding:UTF-8

if [ $# -gt 0 ] ; then
  sleep 120
fi

MACaddress=$(sudo ifconfig | grep -i -m 1  ether | cut -f 10 -d ' ')
IPlocale=$(sudo ifconfig | grep -i -m 1  "netmask 255.255.255.0" | cut -f 10 -d ' ')
IPexterne=$(dig TXT +short -4 o-o.myaddr.1.google @ns1.google.com | cut -f 2 -d '"')
Name=$(hostname)

curl --data "ID=$MACaddress&IPEXT=$IPexterne&IPINT=$IPlocale&NAME=$Name" https://ebirds.be/donnees/identify
