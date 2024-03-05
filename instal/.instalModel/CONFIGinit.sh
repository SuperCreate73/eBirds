#!/bin/bash
# coding:UTF-8

#######################################################################
# installation et configuration des programmes
#######################################################################
# TODO Paramètre pour réinitialiser le module fast-cgi dans PHP
# TODO check parameter cgi.fix_pathinfo in php.ini when php 7.x is loaded


# Activation du service PHP fastcgi
#-----------------------------
printMessage "activation du service fast-cgi" "PHP"
sudo lighty-enable-mod fastcgi-php >> "$LOG_FILE" 2>&1 || printError "$?"
if [ $locError == 0 ] ; then
	sudo service lighttpd force-reload || printError "$?"
fi

# Paramétrage de PHP - cgi.fix_pathinfo
#-------------------------------------
# TODO check when php 7.3 is loaded ?
printMessage "paramétrage" "php-cgi"
sudo sed /etc/php/7.*/cli/php.ini -i -e "s/^;cgi\.fix_pathinfo=1/cgi\.fix_pathinfo=1/g"

# Paramétrage du fichier /boot/config.txt
#----------------------------------------
# activation de la caméra
# TODO
# DONE replace : camera_auto_detect=1 by start_x=1
# DONE comment out : dtoverlay=vc4-kms-v3d
# add : dtoverlay=vc4-fkms-v3d -> line after [pi4]
substitute "/boot/config.txt" "camera_auto_detect=1:start_x=1"
substitute "/boot/config.txt" "dtoverlay=vc4-kms-v3d:#dtoverlay=vc4-kms-v3d"
sed -i -e '/^ *\[pi4\] *$/a\dtoverlay=vc4-fkms-v3d' /boot/config.txt || printError "$?"
#grep -q -e "^start_x=1\$" /boot/config.txt || sudo echo -e "\n# activation de la caméra \nstart_x=1" >> /boot/config.txt
# allocation de mémoire
grep -q -e "^gpu_mem=128\$" /boot/config.txt || sudo echo "gpu_mem=128" >> /boot/config.txt
# désactiver la led de la caméra
grep -q -e "^disable_camera_led=1\$" /boot/config.txt || sudo echo "disable_camera_led=1" >> /boot/config.txt
sudo raspi-config nonint do_legacy 0

# configuration de lighttpd
#--------------------------
# teste si la ligne de configuration existe déjà
if ! grep -q -e '^ *"mod_fastcgi",$' /etc/lighttpd/lighttpd.conf ; then
	printMessage "paramétrage" "lighttpd"
	sudo sed -i -e '/^ *"mod_indexfile",$/a\        "mod_fastcgi",' /etc/lighttpd/lighttpd.conf || printError "$?"
	#sudo sed -i -e '/^ *server.modules = ($/a\        "mod_fastcgi",' /etc/lighttpd/lighttpd.conf || printError "$?"
fi

########################################################################
# gestion des groupes et des permissions
########################################################################
printMessage "gestion des permissions des répertoires" ".motion & /var/www"

addgroup w3 > /dev/null 2>&1 || printError "$?"

adduser pi w3 > /dev/null 2>&1 || printError "$?"
adduser motion w3 > /dev/null 2>&1 || printError "$?"
adduser www-data w3 > /dev/null 2>&1 || printError "$?"

echo "w3 ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers.d/localsudo

#directory /home/pi
chgrp -R w3 /home/pi > /dev/null 2>&1 || printError "$?"
chmod -R 774 /home/pi > /dev/null 2>&1 || printError "$?"

# directory /var/www
chgrp -R w3 /var/www/ > /dev/null 2>&1 || printError "$?"
chmod -R 774 /var/www/ > /dev/null 2>&1 || printError "$?"

# directory /home/pi/.motion
mkdir /home/pi/.motion > /dev/null 2>&1 || printError "$?"
chgrp -R w3 /home/pi/.motion > /dev/null 2>&1 || printError "$?"
chmod -R 770 /home/pi/.motion > /dev/null 2>&1 || printError "$?"

#######################################################################
# configuration de l'envoi de l'adresse IP au serveur
#######################################################################
printMessage "envoi de l'adresse IP au serveur central" "curlIP"

source /var/www/html/public/bash/sendIP

printError "$?"

#######################################################################
# configuration des actions planifiées - cron
#######################################################################
printMessage "config des actions planifiées" "crontab"

echo "0 */6 * * *  root	/var/www/html/public/bash/sendIP " > /etc/cron.d/ebirdsIP
echo "@reboot      root	/var/www/html/public/bash/sendIP --delay " >> /etc/cron.d/ebirdsIP
chmod 644 /etc/cron.d/ebirdsIP
chown root:root /etc/cron.d/ebirdsIP

echo "*/30 * * * *  root	/var/www/backend/sensorStart " > /etc/cron.d/ebirdsSensors
echo "@reboot      root 	/var/www/backend/sensorStart --delay " >> /etc/cron.d/ebirdsSensors
chmod 644 /etc/cron.d/ebirdsSensors
chown root:root /etc/cron.d/ebirdsSensors

echo "@reboot      root  bash /var/www/backend/ebirds_start --delay " >> /etc/cron.d/ebirdsInOut
chmod 644 /etc/cron.d/ebirdsInOut
chown root:root /etc/cron.d/ebirdsInOut

echo "@reboot motion /usr/bin/motion -b" >> /etc/cron.d/motion
chmod 644 /etc/cron.d/motion
chown root:root /etc/cron.d/motion

printError "$?"
