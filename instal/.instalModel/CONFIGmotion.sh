#!/bin/bash
# coding:UTF-8

#################################################################################
# functions
#################################################################################

function updateConfigMotion()
{
	# si la version courante de Motion a des noms de paramètres différents
	# -> testé par l'existance du répertoire
	if [ -f "$INSTALL_PATH/motion/$currentVersion/MOTIONcompare.txt" ] ; then

		# modif des fichiers input : DBinsert_Motion_*
		printMessage "modification du fichier de paramètres" "DBinsertMotion_*.txt"
		readInputFile "$INSTALL_PATH/motion/$currentVersion/MOTIONcompare.txt" "substitute" "$(ls $INSTALL_PATH/.input/DBinsertMotion_*.txt)" || printError "$?"

		# modif des fichiers input : MOTIONparam_*
		printMessage "modification du fichier de paramètres" "MOTIONparam_*.txt"
		readInputFile "$INSTALL_PATH/motion/$currentVersion/MOTIONcompare.txt" "substitute" "$(ls $INSTALL_PATH/.input/MOTIONparam_*.txt)" || printError "$?"

		# modif de la vue
		printMessage "modification de la vue du site web" "viewReglages.php"
		readInputFile "$INSTALL_PATH/motion/$currentVersion/MOTIONcompare.txt" "substitute" "/var/www/html/view/viewReglages.php" || printError "$?"
	fi

	# insertion des paramètres motion dans la DB
	printMessage "modifications des tables Motion" "nichoir.db"
	doInsertRecord $(ls "$INSTALL_PATH"/.input/DBinsertMotion*)
}


#################################################################################
# script begin
#################################################################################
printMessage "Configuration de motion" "motion"

# Activation V4l2 module for using PI camera with Motion
grep -q -e "^bcm2835-v4l2\$" /etc/modules || echo 'bcm2835-v4l2' >> /etc/modules

# get installed version (number part) of motion
installedVersion=`dpkg --status motion | grep "Version" | cut -d ':' -f 2 | cut -d '.' -f "1 2" | sed "s/ //g"`
currentVersion="$installedVersion"

[ "$varDebug" ] && echo "Entering Motion config" >> $DEBUG_FILE

# gestion des fichiers input
# si changement de version ou si explicitement demandé
if [ ! "$currentVersion" = "$verMotion" ] || [ "$varMotion" ] ; then

	# Copie des fichiers sources : DBinsert_Motion_*; MOTIONparam_*
	printMessage "copie des fichiers sources" "MOTIONparam_*.txt; DBinsert_Motion_*"
	copyFiles "$INSTALL_PATH/motion/$verMotionDefault" "$INSTALL_PATH/.input" || printError "$?"

	[ "$varDebug" ] && echo "Entering Motion - reinit existing instal" >> $DEBUG_FILE
	[ "$varDebug" ] && echo "$varFirstInstal" >> $DEBUG_FILE

############################################################################
# New
############################################################################
	if [ "$varFirstInstal" ] ; then
		# mise à jour de première instal
		[ "$varDebug" ] && echo "Entering Motion - first instal config" >> $DEBUG_FILE

		# configuration du démon
		printMessage "Configuration initiale de motion" "motion"
		substitute "start_motion_daemon=no:start_motion_daemon=yes" "/etc/default/motion" || printError "$?"

		printMessage "activation de motion" "motion"
		systemctl enable motion || printError "$?"
	fi

	# DB tables initialisation
	sqlite3 "$DB_FILE" "DELETE from config" > /dev/null 2>&1
	sqlite3 "$DB_FILE" "DELETE from configRange" > /dev/null 2>&1
	sqlite3 "$DB_FILE" "DELETE from configAlias" > /dev/null 2>&1

	copie et gestion des permissions de la vue viewReglages.php
	sudo cp --force "eBirds/html_working/view/viewReglages.php" "/var/www/html/view/viewReglages.php" >> $LOG_FILE 2>&1 || printError "$?"
	chgrp w3 "/var/www/html/view/viewReglages.php" || printError "$?"
	chmod 774 "/var/www/html/view/viewReglages.php" || printError "$?"

	updateConfigMotion



	############################################################################
	# End New
	############################################################################


	#	if [ -d "$INSTALL_PATH/motion/$currentVersion" ] || [ -d "$INSTALL_PATH/motion/$verMotion" ] ; then
			# re-initialisation des tables motion

			# reinitialisation de la vue "viewReglages.php"

	#	fi

# elif [ -d "$INSTALL_PATH/motion/$currentVersion" ] && [ "$varMotion" ] ; then
#
# 	[ "$varDebug" ] && echo "Entering Motion - reinit instal phase 2" >> $DEBUG_FILE
#
# 	# modif de la vue
# 	printMessage "modification de la vue du site web" "viewReglages.php"
# 	doMotionVersion "$INSTALL_PATH/motion/$currentVersion/MOTIONcompare.txt" "/var/www/html/view/viewReglages.php"
fi

[ "$varDebug" ] && echo "Entering Motion - reinit instal final" >> $DEBUG_FILE

[ -d "$INSTALL_PATH/motion/$currentVersion" ] || currentVersion="$verMotionDefault"

printMessage "paramétrage" "motion.conf"
readInputFile "$INSTALL_PATH/.input/MOTIONparam" "motionConfig" "/etc/motion/motion.conf" || printError "$?"

printMessage "mise à jour du fichier de config - verMotion" "$INSTALL_PATH/.config/versions.sh"
updateParameter "$INSTALL_PATH/.config/versions.sh" "verMotion" "$installedVersion"
