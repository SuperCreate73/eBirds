#!/bin/bash
# coding:UTF-8

#######################################################################
# installation et configuration des programmes
#######################################################################

# Activation du service PHP fastcgi
#-----------------------------
printMessage "activation du service fast-cgi" "PHP"
sudo lighty-enable-mod fastcgi-php >> "$LOG_FILE" 2>&1
(( locError=$? ))
printError "$locError"
if [ $locError = 0 ] ; then
	sudo service lighttpd force-reload
	printError "$?"
fi

# Paramétrage de PHP - cgi.fix_pathinfo
#-------------------------------------
# TODO check when php 7.3 is loaded ?
printMessage "paramétrage" "php-cgi"
sudo sed /etc/php/7.*/cli/php.ini -i -e "s/^;cgi\.fix_pathinfo=1/cgi\.fix_pathinfo=1/g"

# activation de la caméra
#------------------------
# teste si les lignes de configuration existent déjà
printMessage "activation de la caméra" "/boot/config.txt"
if grep -e "^start_x=1$" /boot/config.txt ; then
	printMessage "caméra déjà activée" "/boot/config.txt"
else
	sudo echo -e "\n# activation de la caméra" >> /boot/config.txt
	sudo echo "start_x=1" >> /boot/config.txt
	# allocation de mémoire
	sudo echo "gpu_mem=128" >> /boot/config.txt
	# désactiver la led de la caméra
	sudo echo "disable_camera_led=1" >> /boot/config.txt
fi

# configuration de lighttpd
#--------------------------
# teste si la ligne de configuration existe déjà
printMessage "paramétrage" "lighttpd"
if sudo grep -e "^	\"mod_fastcgi\",$" /etc/lighttpd/lighttpd.conf ; then
	printMessage "mod_fastcgi déjà autorisé" "lighttpd"
else
	# renomme le fichier de configuration sous lighttpd.conf.bak
	sudo mv /etc/lighttpd/lighttpd.conf /etc/lighttpd/lighttpd.conf.bak
	# crée le nouveau fichier de configuration en ajoutant la ligne requise
# TODO replace by SED command
	sudo awk '/server.modules/ { print; print "	\"mod_fastcgi\","; next }1' /etc/lighttpd/lighttpd.conf.bak > /etc/lighttpd/lighttpd.conf
fi


########################################################################
# gestion des groupes et des permissions
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

echo "www-data ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers.d/localsudo

chgrp -R w3 /var/www/
printError "$?"

chmod -R 774 /var/www/
printError "$?"

mkdir /home/pi/.motion
printError "$?"

chgrp -R w3 /home/pi/.motion/
printError "$?"

chmod -R 770 /home/pi/.motion/
printError "$?"

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

touch /etc/cron.d/ebirdsIP
chmod 644 /etc/cron.d/ebirdsIP
chown root:root /etc/cron.d/ebirdsIP
echo "0 */6 * * *  root	/var/www/html/public/bash/sendIP " >> /etc/cron.d/ebirdsIP
echo "@reboot      root	/var/www/html/public/bash/sendIP --delay " >> /etc/cron.d/ebirdsIP

touch /etc/cron.d/ebirdsSensors
chmod 644 /etc/cron.d/ebirdsSensors
chown root:root /etc/cron.d/ebirdsSensors
echo "*/30 * * * *  root	/var/www/backend/sensorStart " >> /etc/cron.d/ebirdsSensors
echo "@reboot      root 	/var/www/backend/sensorStart --delay " >> /etc/cron.d/ebirdsSensors

touch /etc/cron.d/ebirdsInOut
chmod 644 /etc/cron.d/ebirdsInOut
chown root:root /etc/cron.d/ebirdsInOut
echo "@reboot      root  bash /var/www/backend/ebirds_start --delay " >> /etc/cron.d/ebirdsInOut

printError "$?"
