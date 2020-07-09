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

# Paramétrage du fichier /boot/config.txt
#----------------------------------------
# activation de la caméra
grep -q -e "^start_x=1$" /boot/config.txt || sudo echo -e "\n# activation de la caméra \nstart_x=1" >> /boot/config.txt
# allocation de mémoire
grep -q -e "^gpu_mem=128$" /boot/config.txt || sudo echo "gpu_mem=128" >> /boot/config.txt
# désactiver la led de la caméra
grep -q -e "^disable_camera_led=1$" /boot/config.txt || sudo echo "disable_camera_led=1" >> /boot/config.txt

# configuration de lighttpd
#--------------------------
# teste si la ligne de configuration existe déjà
printMessage "paramétrage" "lighttpd"
sudo grep -q -e '^ *"mod_fastcgi",$' /etc/lighttpd/lighttpd.conf  || sudo sed -i -e '/^ *server.modules$/a\    "mod_fastcgi",' /etc/lighttpd/lighttpd.conf
printError "$?"

########################################################################
# gestion des groupes et des permissions
########################################################################
printMessage "gestion des permissions des répertoires" ".motion & /var/www"

addgroup w3 > /dev/null 2>&1 || printError "$?"

adduser pi w3 > /dev/null 2>&1 || printError "$?"
adduser motion w3 > /dev/null 2>&1 || printError "$?"
adduser www-data w3 > /dev/null 2>&1 || printError "$?"

echo "www-data ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers.d/localsudo

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

printError "$?"
