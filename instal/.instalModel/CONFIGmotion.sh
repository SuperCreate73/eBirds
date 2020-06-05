#!/bin/bash
# coding:UTF-8

#################################################################################
# functions
#################################################################################

function doMotionVersion()
{
	# update input file with option names of current motion verion
	# $1 fichier de paramètres Motion à traiter
	# $2 fichier dans lequel effectuer les remplacements

	OLDIFS="$IFS"
	local tmpContent=$(grep -e '^[^(#|;).*]' "$1")
	[ "$varDebug" ] && echo "doMotionVersion $1 $2" >> $DEBUG_FILE
	[ "$varDebug" ] && echo "$tmpContent" >> $DEBUG_FILE
	while IFS=: read referenceName substituteName ; do
		sed "$2" -i -e "s/$referenceName/$substituteName/g" || printError "$?"
	done <<< $(grep -e '^[^(#|;).*]' "$1")

	IFS="$OLDIFS"
	return 0
}

function updateConfigMotion()
{
	# si la version courante de Motion a des noms de paramètres différents
	# -> testé par l'existance du répertoire
	if [ -d "$INSTALL_PATH/motion/$currentVersion" ] ; then

		# modif des fichiers input : DBinsert_Motion_*
		printMessage "modification du fichier de paramètres" "DBinsertMotion_*.txt"
		for varFile in $(ls "$INSTALL_PATH"/.input/DBinsertMotion_*) ; do
			doMotionVersion "$INSTALL_PATH/motion/$currentVersion/MOTIONcompare.txt" "$varFile"
		done

		# modif des fichiers input : MOTIONparam_*
		printMessage "modification du fichier de paramètres" "MOTIONparam_*.txt"
		for varFile in $(ls "$INSTALL_PATH"/.input/MOTIONparam_*) ; do
			doMotionVersion "$INSTALL_PATH/motion/$currentVersion/MOTIONcompare.txt" "$varFile"
		done

		# modif de la vue
		printMessage "modification de la vue du site web" "viewReglages.php"
		doMotionVersion "$INSTALL_PATH/motion/$currentVersion/MOTIONcompare.txt" "/var/www/html/view/viewReglages.php"
	fi

	# insertion des paramètres motion dans la DB
	printMessage "modifications des tables Motion" "nichoir.db"
	doInsertRecord $(ls "$INSTALL_PATH"/.input/DBinsertMotion*)
}


#################################################################################
# script begin
#################################################################################
printMessage "Configuration de motion" "motion"

# Activation du module V4l2 pour que la camera PI soit reconnue par Motion,
#+si pas encore fait uniquement (testé par GREP)
if ! grep -q -e "^bcm2835-v4l2$" /etc/modules ; then
	echo 'bcm2835-v4l2' >> /etc/modules
fi

# get installed version (number part) of motion
installedVersion=`dpkg --status motion | grep "Version" | cut -d ':' -f 2 | cut -d '.' -f "1 2" | sed "s/ //g"`
currentVersion="$installedVersion"

[ "$varDebug" ] && echo "Entering Motion config" >> $DEBUG_FILE

# gestion des fichiers input
# si changement de version
if [ ! "$currentVersion" = "$verMotion" ] || [ "$varMotion" ] ; then

	# Copie des fichiers sources : DBinsert_Motion_*; MOTIONparam_*
	cp --force "$INSTALL_PATH"/motion/"$verMotionDefault"/DBinsertMotion_* "$INSTALL_PATH/.input/"
	cp --force "$INSTALL_PATH"/motion/"$verMotionDefault"/MOTIONparam_* "$INSTALL_PATH/.input/"

	[ "$varDebug" ] && echo "Entering Motion - reinit existing instal" >> $DEBUG_FILE
	[ "$varDebug" ] && echo "$varFirstInstal" >> $DEBUG_FILE

	# si pas une nouvelle install et version installée ou ancienne version avec paramètres modifiés -> mise à jour
	if [ "$varFirstInstal" != true ] ; then

		[ "$varDebug" ] && echo $([ -d "$INSTALL_PATH/motion/$currentVersion" ]) >> $DEBUG_FILE
		[ "$varDebug" ] && echo $([ -d "$INSTALL_PATH/motion/$verMotion" ]) >> $DEBUG_FILE

		# BUG programme ne semble pas vider et copier les tables, pourquoi ?
		#

		if [ -d "$INSTALL_PATH/motion/$currentVersion" ] || [ -d "$INSTALL_PATH/motion/$verMotion" ] ; then
			# re-initialisation des tables motion
			sqlite3 "$DB_FILE" "DELETE from config" > /dev/null 2>&1
			sqlite3 "$DB_FILE" "DELETE from configRange" > /dev/null 2>&1
			sqlite3 "$DB_FILE" "DELETE from configAlias" > /dev/null 2>&1

			# reinitialisation de la vue "viewReglages.php"
			sudo cp --force "eBirds/html_working/view/viewReglages.php" "/var/www/html/view/viewReglages.php" >> $LOG_FILE 2>&1 || printError "$?"

			updateConfigMotion

		fi

	else
		# mise à jour de première instal
		[ "$varDebug" ] && echo "Entering Motion - reinit existing instal - else" >> $DEBUG_FILE

		# configuration du démon
		sed "/etc/default/motion" -i -e "s/^start_motion_daemon=no/start_motion_daemon=yes/g"
		printMessage "activation de motion" "motion"
		systemctl enable motion || printError "$?"

		updateConfigMotion
	fi


elif [ -d "$INSTALL_PATH/motion/$currentVersion" ] && [ "$varMotion" ] ; then
	# TODO Ajouter un flag pour forcer la mise à jour de la vue, true si les fichiers du nichoir ont été mis à jour.
	[ "$varDebug" ] && echo "Entering Motion - reinit instal phase 2" >> $DEBUG_FILE

	# modif de la vue
	printMessage "modification de la vue du site web" "viewReglages.php"
	doMotionVersion "$INSTALL_PATH/motion/$currentVersion/MOTIONcompare.txt" "/var/www/html/view/viewReglages.php"

	chgrp w3 "/var/www/html/view/viewReglages.php" || printError "$?"

	chmod 774 "/var/www/html/view/viewReglages.php" || printError "$?"


fi

[ "$varDebug" ] && echo "Entering Motion - reinit instal final" >> $DEBUG_FILE

	[ -d "$INSTALL_PATH/motion/$currentVersion" ] || currentVersion="$verMotionDefault"

	# TODO condition à revoir :
	#		> first_instal - si version < reference -> modif des fichiers, sinon référence
	#		> modif - effacer les tables motion, puis idem first_instal
	# 	Attention à la vue settings, qui sera PE modifiée à chaque mise à jour

	motionPath="/etc/motion/motion.conf"

	# modification de motion.conf
	source "$INSTALL_PATH/.instalModel/CONFIGmotionConf.sh"

	updateParameter "$INSTALL_PATH/.config/versions.sh" "verMotion" "$installedVersion"

# # création du fichier config local de motion
# #cp /etc/motion/motion.conf /usr/local/etc/motion.conf
# #printError "$?"
#
# # chmod 666 /var/log/motion/motion.log
# # printError "$?"
# motionPath="/etc/motion/motion.conf"
#
# # modification de motion.conf
# source "$INSTALL_PATH/.instalModel/CONFIGmotionConf.sh"
#
# # configuration du démon
# sed "/etc/default/motion" -i -e "s/^start_motion_daemon=no/start_motion_daemon=yes/g"
#
# # configuration du démon
# printMessage "activation de motion" "motion"
# systemctl enable motion
# printError "$?"


# if [ -z "$1" ] ; then  # test si au moins un paramètre est passé
