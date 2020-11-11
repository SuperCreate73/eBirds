#!/bin/bash
# coding:UTF-8

#######################################################################
# Source files of 'nichoir' management
#
# version : v1.0-beta
# date : 6-9-2020
#######################################################################
# First clean-up of html dir, copy of html dir sources, copy of backend
#+dir sources, image and video dir assignments.
#
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
	local filePathReal="$1"
	local filePathLink="$2"

	printMessage "Création des répertoires" "$filePathReal"

	# make 'real' directory if not exist
	createDir "$filePathReal" || printError "$?"

	# make 'symlink' directory if not exist.  Otherwise, copy files in 'real' directory before creating symlink
	# option backup is used to avoid loosing data
	if [ ! -d "$filePathLink" ] ; then 		# dir do not exist -> create symlink

		ln -s "$filePathReal" "$filePathLink" >> "$LOG_FILE" 2>&1  || printError "$?" 	# create symlink

	elif [ ! -L "$filePathLink"] ; then		# dir exist & not a symlink
		[ ! `ls -A "$filePathLink" | wc -c` -eq 0 ] && mv -b "$filePathLink/*" "$filePathReal" >> "$LOG_FILE" 2>&1 # dir not empty -> backup files
		removeDir "$filePathLink" || printError "$?"	# remove directory
		ln -s "$filePathReal" "$filePathLink" >> "$LOG_FILE" 2>&1 || printError "$?" # create symlink
	fi

	return 0
}

# clean-up directory $WEB_PATH if dir camera files or dbfile don't exist (first instal)
if [ -d "$WEB_PATH" ] && [ ! `ls -A "$WEB_PATH" | wc -c` -eq 0 ] ; then 	# si le répertoire existe et n'est pas vide
	if [ ! -d "$IMAGE_PATH_LINK" ] || [ ! -e "$DB_FILE" ] ; then # si le dir camera n'existe pas ou
		printMessage "Nettoyage du répertoire html" "rm -r /var/www/html/*"								#+si la db n'existe pas
		rm -r -d "$WEB_PATH/*" >> "$LOG_FILE" 2>&1 || printError "$?"
	fi
fi

# delete from source files (normally not present)
removeDir	"$IMAGE_PATH_ORIGINAL"
removeDir	"$VIDEO_PATH_ORIGINAL"

printMessage "déplacement des fichiers web" "$WEB_PATH"
copyFiles "$WEB_PATH_ORIGINAL" "$WEB_PATH" || printError "$?"

makeCameraStorage "$IMAGE_PATH_REAL" "$IMAGE_PATH_LINK" 	# create image dir

makeCameraStorage "$VIDEO_PATH_REAL" "$VIDEO_PATH_LINK"		# create video dir

printMessage "déplacement des scripts python" "/var/www/backend"
copyDir "$BACKEND_PATH_ORIGINAL" "$ROOT_PATH" || printError "$?"

if [ ! -d "/var/www/log" ] ; then
	printMessage "copie du répertoire log" "/var/www/log"
	copyDir "$LOG_PATH_ORIGINAL" "$ROOT_PATH" || printError "$?"
fi
