#!/bin/bash
# coding:UTF-8

########################################################################
printMessage "gestion des permissions des répertoires" ".motion & /var/www"
addgroup w3
printError "$?"

adduser pi w3
printError "$?"

adduser motion w3
printError "$?"

adduser www-data w3
printError "$?"

sudo echo "www-data ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

sudo chgrp -R w3 /var/www/
printError "$?"

sudo chmod -R 774 /var/www/
printError "$?"

sudo mkdir /home/pi/.motion
printError "$?"

sudo chgrp -R w3 /home/pi/.motion/
printError "$?"

sudo chmod -R 770 /home/pi/.motion/
printError "$?"

#######################################################################
# configuration de l'envoi de l'adresse IP au serveur
#######################################################################
printMessage "envoi de l'adresse IP au serveur central" "curlIP"

MACaddress=$(sudo ifconfig | grep -i -m 1  ether | cut -f 10 -d ' ')
IPlocale=$(sudo ifconfig | grep -i -m 1  "netmask 255.255.255.0" | cut -f 10 -d ' ')
IPexterne=$(dig TXT +short -4 o-o.myaddr.1.google @ns1.google.com | cut -f 2 -d '"')
Name=$(hostname)

curl --data "ID=$MACaddress&IPEXT=$IPexterne&IPINT=$IPlocale&NAME=$Name&XCOORD=0&YCOORD=0" https://ebirds.be/data/identify
printError "$?"

#######################################################################
# configuration des actions planifiées - cron
#######################################################################
printMessage "config des actions planifiées" "crontab"

sudo touch /etc/cron.d/ebirdsIP
sudo chmod 644 /etc/cron.d/ebirdsIP
sudo chown root:root /etc/cron.d/ebirdsIP
sudo echo "0 */6 * * *  root	/var/www/html/public/bash/sendIP " >> /etc/cron.d/ebirdsIP
sudo echo "@reboot      root	/var/www/html/public/bash/sendIP --delay " >> /etc/cron.d/ebirdsIP

sudo touch /etc/cron.d/ebirdsSensors
sudo chmod 644 /etc/cron.d/ebirdsSensors
sudo chown root:root /etc/cron.d/ebirdsSensors
sudo echo "*/30 * * * *  root	/var/www/backend/sensorStart " >> /etc/cron.d/ebirdsSensors
sudo echo "@reboot      root 	/var/www/backend/sensorStart --delay " >> /etc/cron.d/ebirdsSensors

sudo touch /etc/cron.d/ebirdsInOut
sudo chmod 644 /etc/cron.d/ebirdsInOut
sudo chown root:root /etc/cron.d/ebirdsInOut
sudo echo "@reboot      root  bash /var/www/backend/ebirds_start --delay " >> /etc/cron.d/ebirdsInOut

printError "$?"
