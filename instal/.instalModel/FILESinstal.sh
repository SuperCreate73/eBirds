#!/bin/bash
# coding:UTF-8

#######################################################################
# gestion des fichiers sources et copie dans les bons répertoires
#######################################################################
if [ -d /var/www/html ] ; then
	if [ ! -d /var/www/html/public/cameraShots ] || [ ! -e /var/www/nichoir.db] ; then
		printMessage "Nettoyage du répertoire html" "rm -r /var/www/html/*"
		rm -r /var/www/html/* >> $varLogFile 2>&1
		printError "$?"
	fi
fi

if [ "$varGit" == "true" ] ; then
	printMessage "téléchargement des fichiers sources depuis GitHub" "https://github.com/SuperCreate73/eBirds.git"
	git clone --quiet https://github.com/SuperCreate73/eBirds.git >> $varLogFile 2>&1
	printError "$?"

	# vérifie que les répertoires photo et film sont bien absent des fichiers
	# avant de copier la nouvelle copie des fichiers sources
	if [ ! -d eBirds/html_working/public/cameraShots ] ; then
		rm -r -d eBirds/html_working/public/cameraShots 2>&1
	fi

	if [ ! -d eBirds/html_working/public/cameraFilms ] ; then
		rm -r -d eBirds/html_working/public/cameraFilms 2>&1
	fi

	printMessage "déplacement des fichiers web" "/var/www/html"
	mv --force eBirds/html_working/* /var/www/html/ >> $varLogFile 2>&1
	printError "$?"

	# Crée des répertoires vide si non existant
	if [ ! -d /var/www/html/public/cameraShots ] ; then
		mkdir /var/www/html/public/cameraShots 2>&1
	fi

	if [ ! -d /var/www/html/public/cameraFilms ] ; then
		mkdir /var/www/html/public/cameraFilms 2>&1
	fi

	printMessage "déplacement des scripts python" "/var/www/backend"
	mv --force eBirds/backend /var/www/ >> $varLogFile 2>&1
	printError "$?"

	printMessage "déplacement du répertoire log" "/var/www/log"
	mv --force eBirds/log /var/www/ >> $varLogFile 2>&1
	printError "$?"

	printMessage "nettoyage des fichiers résiduels" "rm -r eBirds"
	rm -r eBirds
	printError "$?"
fi
# else
# 	printMessage "décompression des fichiers sources" "nichoir"
# 	tar -xJf $varSourceWeb >> $varLogFile 2>&1
# 	printError "$?"
#
# 	printMessage "déplacement des fichiers web" "/var/www/html"
# 	mv --force web/html/ /var/www/ >> $varLogFile 2>&1
# 	printError "$?"
#
# 	printMessage "nettoyage des fichiers résiduels" "rm -r eBirds"
# 	rm -r web
# 	printError "$?"
