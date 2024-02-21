#!/bin/bash
# coding:UTF-8

#################################################################################
# script begin
#################################################################################
printMessage "Configuration de motion" "motion"

# Activation V4l2 module for using PI camera with Motion
grep -q -e "^bcm2835-v4l2\$" /etc/modules || echo 'bcm2835-v4l2' >> /etc/modules

# get installed version (number part) of motion
installedVersion=`dpkg --status motion | grep "Version" | cut -d ':' -f 2 | cut -d '.' -f "1 2" | sed "s/ //g"`
currentVersion="$installedVersion"

# create log directory, log file and set permission and ownership
if [! -d "/var/www/log/motion" ] ; then
	createDir "/var/www/log/motion" || printError "$?"
	touch /var/www/log/motion/motion.log
	chown -R root:w3 "/var/www/log/motion" && chmod -R 666 "/var/www/log/motion"
fi

[ "$varDebug" ] && echo "Entering Motion config" >> $DEBUG_FILE

# gestion des fichiers input
# si changement de version ou si explicitement demandé
if [ ! "$currentVersion" = "$verMotion" ] || [ "$varMotion" ] ; then

	# Copie des fichiers sources : DBinsert_Motion_*; MOTIONparam_*
	printMessage "copie des fichiers sources" "MOTIONparam_*.txt; DBinsert_Motion_*"
	copyFiles "$INSTALL_ROOTPATH/motion/$verMotionDefault" "$INSTALL_ROOTPATH/.input" || printError "$?"

	# copy motion.conf in default dir /usr/local/etc/
	cp /etc/motion/motion.conf /usr/local/etc/

	[ "$varDebug" ] && echo "Entering Motion - reinit existing instal" >> $DEBUG_FILE
	[ "$varDebug" ] && echo "$varFirstInstal" >> $DEBUG_FILE

############################################################################
	if [ "$varFirstInstal" ] ; then
		# mise à jour de première instal
		[ "$varDebug" ] && echo "Entering Motion - first instal config" >> $DEBUG_FILE

		# configuration du démon
		printMessage "Configuration initiale de motion" "motion"
		substitute "daemon on:daemon off" "/usr/local/etc/motion.conf" || printError "$?"

		printMessage "activation de motion" "motion"
		systemctl enable motion || printError "$?"
	fi

	# DB tables initialisation
	sqlite3 "$DB_FILE" "DELETE FROM config" > /dev/null 2>&1
	sqlite3 "$DB_FILE" "DELETE FROM configRange" > /dev/null 2>&1
	sqlite3 "$DB_FILE" "DELETE FROM configAlias" > /dev/null 2>&1

	copie et gestion des permissions de la vue viewReglages.php
	copyFiles "eBirds/html_working/view/viewReglages.php" "/var/www/html/view/viewReglages.php" || printError "$?"
	chgrp w3 "/var/www/html/view/viewReglages.php" || printError "$?"
	chmod 774 "/var/www/html/view/viewReglages.php" || printError "$?"

	############################################################################
	# si la version courante de Motion a des noms de paramètres différents
	# -> testé par l'existance du répertoire
	if [ -f "$INSTALL_ROOTPATH/motion/$currentVersion/MOTIONcompare.txt" ] ; then

		# modif des fichiers input : DBinsert_Motion_*
		printMessage "modification du fichier de paramètres" "DBinsertMotion_*.txt"
		readInputFile "$INSTALL_ROOTPATH/motion/$currentVersion/MOTIONcompare.txt" "substitute" "$(ls $INSTALL_ROOTPATH/.input/DBinsertMotion_*.txt)" || printError "$?"

		# modif des fichiers input : MOTIONparam_*
		printMessage "modification du fichier de paramètres" "MOTIONparam_*.txt"
		readInputFile "$INSTALL_ROOTPATH/motion/$currentVersion/MOTIONcompare.txt" "substitute" "$(ls $INSTALL_ROOTPATH/.input/MOTIONparam_*.txt)" || printError "$?"

		# modif de la vue
		printMessage "modification de la vue du site web" "viewReglages.php"
		readInputFile "$INSTALL_ROOTPATH/motion/$currentVersion/MOTIONcompare.txt" "substitute" "/var/www/html/view/viewReglages.php" || printError "$?"
	fi

	# insertion des paramètres motion dans la DB
	printMessage "modifications des tables Motion" "nichoir.db"
	readInputFile "$INSTALL_ROOTPATH/.input/DBinsertMotion" "insertRecord" "$(ls $INSTALL_ROOTPATH/.input/MOTIONparam_*.txt)" || printError "$?"
fi
############################################################################

[ "$varDebug" ] && echo "Motion config - end reached" >> $DEBUG_FILE

[ -d "$INSTALL_ROOTPATH/motion/$currentVersion" ] || currentVersion="$verMotionDefault"

printMessage "paramétrage" "motion.conf"
readInputFile "$INSTALL_ROOTPATH/.input/MOTIONparam" "motionConfig" "/usr/local/etc/motion.conf" || printError "$?"

printMessage "mise à jour du fichier de config - verMotion" "$INSTALL_ROOTPATH/.config/versions.sh"
updateParameter "$INSTALL_ROOTPATH/.config/versions.sh" "verMotion" "$installedVersion"
