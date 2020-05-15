#!/bin/bash
# coding:UTF-8
# TODO TODO enlever les fichiers d'instal ? vérifier si tout est OK
#######################################################################
# gestion des fichiers sources et copie dans les bons répertoires
#######################################################################
# nettoyage de /var/www/html si nichoir pas encore installé
# déterminé avec répertoire 'camerashot' et base de donnée 'nichoir.db'
if [ -d /var/www/html ] && [ ! `ls -A /var/www/html | wc -c` -eq 0 ] ; then
	if [ ! -d /var/www/html/public/cameraShots ] || [ ! -e /var/www/nichoir.db ] ; then
		printMessage "Nettoyage du répertoire html" "rm -r /var/www/html/*"
		rm -r /var/www/html/* >> $LOG_FILE 2>&1
		printError "$?"
	fi
fi

# vérifie que les répertoires photo et film sont bien absent des fichiers
# avant de copier la nouvelle copie des fichiers sources
if [ -d "eBirds/html_working/public/cameraShots" ] ; then
	rm -r -d "eBirds/html_working/public/cameraShots" > /dev/null 2>&1
fi

if [ -d "eBirds/html_working/public/cameraFilms" ] ; then
	rm -r -d "eBirds/html_working/public/cameraFilms" > /dev/null 2>&1
fi

printMessage "déplacement des fichiers web" "/var/www/html"
sudo cp -r --force eBirds/html_working/* /var/www/html/ >> $LOG_FILE 2>&1 || printError "$?"

# Crée des répertoires vide si non existant
if [ ! -d "/var/www/html/public/cameraShots" ] ; then
	sudo mkdir "/var/www/html/public/cameraShots" >> "$LOG_FILE" 2>&1
fi

if [ ! -d "/var/www/html/public/cameraFilms" ] ; then
	sudo mkdir "/var/www/html/public/cameraFilms" >> "$LOG_FILE" 2>&1
fi

printMessage "déplacement des scripts python" "/var/www/backend"
sudo cp -r --force eBirds/backend /var/www/ >> "$LOG_FILE" 2>&1 || printError "$?"

if [ ! -d "/var/www/log" ] ; then
	printMessage "déplacement du répertoire log" "/var/www/log"
	sudo mv --force eBirds/log /var/www/ >> "$LOG_FILE" 2>&1 || printError "$?"
fi
