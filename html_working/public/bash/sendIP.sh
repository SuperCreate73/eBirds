#!/bin/bash
# coding:UTF-8

MACaddress=$(sudo ifconfig | grep -i -m 1  ether | cut -f 10 -d ' ')
IPlocale=$(sudo ifconfig | grep -i -m 1  "netmask 255.255.255.0" | cut -f 10 -d ' ')
IPexterne=$(dig TXT +short -4 o-o.myaddr.1.google @ns1.google.com | cut -f 2 -d '"')

curl --data "ID=$MACaddress&IPEXT=$IPexterne&IPINT=$IPlocale" https://ebirds.be/donnees/identify
