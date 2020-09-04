#!/bin/bash
# coding:UTF-8
# TODO TODO enlever les fichiers d'instal ? vérifier si tout est OK
#######################################################################
# gestion des fichiers sources et copie dans les bons répertoires
#######################################################################
# nettoyage de /var/www/html si nichoir pas encore installé
# déterminé avec répertoire 'camerashot' et base de donnée 'nichoir.db'
IMAGE_PATH_REAL="/home/pi/images"
IMAGE_PATH_LINK="$WEB_PATH/public/cameraShots"
VIDEO_PATH_REAL="/home/pi/videos"
VIDEO_PATH_LINK="$WEB_PATH/public/cameraFilms"
WEB_PATH_ORIGINAL="eBirds/html_working"
BACKEND_PATH_ORIGINAL="eBirds/backend"
LOG_PATH_ORIGINAL="eBirds/log"
VIDEO_PATH_ORIGINAL="$WEB_PATH_ORIGINAL/public/cameraFilms"
IMAGE_PATH_ORIGINAL="$WEB_PATH_ORIGINAL/public/cameraShots"

function makeCameraStorage()
{
	# create directories and symlinks to store camera image & films
	filePathReal="$1"
	filePathLink="$2"

	printMessage "Création des répertoires" "$filePathReal"

	# make 'real' directory if not exist
	[ ! -d "$filePathReal" ] &&	mkdir "$filePathReal" >> "$LOG_FILE" 2>&1 || printError "$?"

	# make 'symlink' directory if not exist.  Otherwise, copy files in 'real' directory before creating symlink
	# option backup is used to avoid loosing data
	if [ ! -d "$filePathLink" ] ; then 		# dir do not exist -> create symlink

		ln -s "$filePathReal" "$filePathLink" >> "$LOG_FILE" 2>&1  || printError "$?" 	# create symlink

	elif [ ! -L "$filePathLink"] ; then		# dir exist & not a symlink

		[ ! `ls -A "$filePathLink" | wc -c` -eq 0 ] && mv -b "$filePathLink/*" "$filePathReal" >> "$LOG_FILE" 2>&1 # dir not empty -> backup files
		fi

		rm -r "$filePathLink"	>> "$LOG_FILE" 2>&1 || printError "$?"	# remove directory
		ln -s "$filePathReal" "$filePathLink" >> "$LOG_FILE" 2>&1 || printError "$?" # create symlink
	fi

	exit 0

}

# clean-up directory $WEB_PATH if dir camera files or dbfile don't exist (first instal)
if [ -d "$WEB_PATH" ] && [ ! `ls -A "$WEB_PATH" | wc -c` -eq 0 ] ; then 	# si le répertoire existe et n'est pas vide
	if [ ! -d "$IMAGE_PATH_LINK" ] || [ ! -e "$DB_FILE" ] ; then # si le dir camera n'existe pas ou
		printMessage "Nettoyage du répertoire html" "rm -r /var/www/html/*"								#+si la db n'existe pas
		rm -r -d "$WEB_PATH/*" >> "$LOG_FILE" 2>&1 || printError "$?"
	fi
fi

# vérifie que les répertoires photo et film sont bien absent des fichiers sources
#+avant de les copier pour éviter d'écraser des photos/films existants
[ -d "$IMAGE_PATH_ORIGINAL" ] && rm -r -d "$IMAGE_PATH_ORIGINAL" > /dev/null 2>&1
[ -d "$VIDEO_PATH_ORIGINAL" ] && rm -r -d "$VIDEO_PATH_ORIGINAL" > /dev/null 2>&1

printMessage "déplacement des fichiers web" "$WEB_PATH"
sudo cp -r --force "$WEB_PATH_ORIGINAL/*" "$WEB_PATH" >> "$LOG_FILE" 2>&1 || printError "$?"

makeCameraStorage "$IMAGE_PATH_REAL" "$IMAGE_PATH_LINK" 	# create image dir

makeCameraStorage "$VIDEO_PATH_REAL" "$VIDEO_PATH_LINK"		# create video dir

printMessage "déplacement des scripts python" "/var/www/backend"
cp -r --force "$BACKEND_PATH_ORIGINAL" "$ROOT_PATH" >> "$LOG_FILE" 2>&1 || printError "$?"

if [ ! -d "/var/www/log" ] ; then
	printMessage "déplacement du répertoire log" "/var/www/log"
	sudo mv --force "$LOG_PATH_ORIGINAL" "$ROOT_PATH" >> "$LOG_FILE" 2>&1 || printError "$?"
fi
