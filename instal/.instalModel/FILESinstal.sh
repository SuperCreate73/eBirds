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
IMAGE_DIR_REAL="/home/pi/images"
IMAGE_DIR_LINK="$WEB_PATH/public/cameraShots"
VIDEO_DIR_REAL="/home/pi/videos"
VIDEO_DIR_LINK="$WEB_PATH/public/cameraFilms"
WEB_DIR_ORIGINAL="eBirds/html_working"
BACKEND_DIR_ORIGINAL="eBirds/backend"
LOG_DIR_ORIGINAL="eBirds/log"
VIDEO_DIR_ORIGINAL="$WEB_DIR_ORIGINAL/public/cameraFilms"
IMAGE_DIR_ORIGINAL="$WEB_DIR_ORIGINAL/public/cameraShots"

function makeCameraStorage()
{
	# create directories and symlinks to store camera image & films

	if [ ! -L "$2" ] ; then		# dir exist & not a symlink
	# if target exists and not a symlink, copy files to real dir
		printMessage "Sauvegarde des fichiers existants" "$2/*"
		copyFiles "$2" "$1" || printError "$?"  # dir not empty -> backup files
	fi

	printMessage "Création du lien symbolique" "$2"
	createSymLink "$1" "$2" || printError "$?" # create symlink

	return 0
}

# clean-up directory $WEB_PATH if first instal
if [ "$varFirstInstal" ] ; then 	# first instal
	printMessage "Nettoyage du répertoire html" "clearDir /var/www/html/*"
	clearDir "$WEB_PATH" || printError "$?"
fi

# delete from source files (normally not present)
removeDir	"$IMAGE_DIR_ORIGINAL"
removeDir	"$VIDEO_DIR_ORIGINAL"

printMessage "déplacement des fichiers web" "$WEB_PATH"
copyFiles "$WEB_DIR_ORIGINAL" "$WEB_PATH" || printError "$?"

printMessage "Création du dir de stockage des photos" "$IMAGE_DIR_REAL"
createDir "$IMAGE_DIR_REAL" || printError "$?"

printMessage "Création du dir de stockage des videos" "$VIDEO_DIR_REAL"
createDir "$VIDEO_DIR_REAL" || printError "$?"

makeCameraStorage "$IMAGE_DIR_REAL" "$IMAGE_DIR_LINK" 	# create image dir

makeCameraStorage "$VIDEO_DIR_REAL" "$VIDEO_DIR_LINK"		# create video dir

printMessage "déplacement des scripts python" "/var/www/backend"
copyDir "$BACKEND_DIR_ORIGINAL" "$ROOT_PATH" || printError "$?"

if [ ! -d "$ROOT_PATH/log" ] ; then
	printMessage "création du répertoire des log" "$ROOT_PATH/log"
	createDir "$ROOT_PATH/log" || printError "$?"
fi
