#!/bin/bash
# coding:UTF-8

printMessage "Configuration de motion" "motion"

# Activation du module V4l2 pour que la camera PI soit reconnue par Motion
#-------------------------------------
if ! grep -e "^bcm2835-v4l2$" /etc/modules > /dev/null ; then
	echo 'bcm2835-v4l2' >> /etc/modules
fi

installedVersion=`dpkg --status motion | grep "Version" | cut -d ':' -f 2 | cut -d '.' -f "1 2" | sed "s/ //g"`
currentVersion="$installedVersion"

if [ ! "$currentVersion" = "$verMotion" ] ; then
	[ -d "$varInstalPath/motion/$currentVersion" ] ||	currentVersion="$verMotionDefault"

	# copie des fichiers et gestion des permissions
	mv `ls $varInstalPath/motion/$currentVersion/DBinsertMotion*` "$varInstalPath/.input/" || printError "$?"

	mv `ls $varInstalPath/motion/$currentVersion/MOTIONparam*` "$varInstalPath/.input/" || printError "$?"

	chgrp w3 "$varInstalPath/motion/$currentVersion/viewReglages.php" || printError "$?"

	chmod 774 "$varInstalPath/motion/$currentVersion/viewReglages.php" || printError "$?"

	mv "$varInstalPath/motion/$currentVersion/viewReglages.php" "/var/www/html/view/" || printError "$?"

 	# clear motion config tables
	if [ ! "$varFirstInstal" = "true" ] ; then
		rm `ls $varInstalPath/.input/DBinsert_*` > /dev/null 2>&1
		sqlite3 /var/www/nichoir.db "DELETE from config" > /dev/null 2>&1
		sqlite3 /var/www/nichoir.db "DELETE from configRange" > /dev/null 2>&1
		sqlite3 /var/www/nichoir.db "DELETE from configAlias" > /dev/null 2>&1

		source "$varInstalPath/.instalModel/DBinsertRecord.sh"

	else
		# configuration du démon
		sed "/etc/default/motion" -i -e "s/^start_motion_daemon=no/start_motion_daemon=yes/g"

		# configuration du démon
		printMessage "activation de motion" "motion"
		systemctl enable motion || printError "$?"
	fi

	motionPath="/etc/motion/motion.conf"

	# modification de motion.conf
	source "$varInstalPath/.instalModel/CONFIGmotionConf.sh"

	updateParameter "$varInstalPath/.config/versions.sh" "verMotion" "$installedVersion"
fi
#
# # création du fichier config local de motion
# #cp /etc/motion/motion.conf /usr/local/etc/motion.conf
# #printError "$?"
#
# # chmod 666 /var/log/motion/motion.log
# # printError "$?"
# # TODO vérifier quelle est la version installée dans le fichier versions.sh
# # TODO en fonction de la version installée, de la version de motion, adapter
# # 		les fichiers d'instal
# motionPath="/etc/motion/motion.conf"
#
# # modification de motion.conf
# source "$varInstalPath/.instalModel/CONFIGmotionConf.sh"
#
# # configuration du démon
# sed "/etc/default/motion" -i -e "s/^start_motion_daemon=no/start_motion_daemon=yes/g"
#
# # configuration du démon
# printMessage "activation de motion" "motion"
# systemctl enable motion
# printError "$?"
