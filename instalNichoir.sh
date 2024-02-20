#!/bin/bash
# coding:UTF-8

# check superUser right
if [[ $EUID -ne 0 ]] ; then
	echo -e "Ce script doit être exécuté avec les droits de SuperUser." 1>&2
	exit 1
fi

SCRIPT_FILE="installNichoir-3.sh"

# get line of marker ARCHIVE
txtLine=$(grep --text --line-number "^ARCHIVE$" "$0") 2> /dev/null

# extract line number
numLine=${txtLine%:*}

# extract archive
tail -n +$((numLine + 1)) $0 | tar zx 2> /dev/null

#
# TODO tester si une installation existe déjà
# TODO ne copier que le fichier d'instal et les dépendances, pas les paramètres

if [ ! -d /usr/local/etc/instal ] ; then
	# move all in usr/local/etc/
	mv instal/ /usr/local/etc/
	mv --force /usr/local/etc/instal/.config/versions_init.sh /usr/local/etc/instal/.config/versions.sh
else
	cp --force instal/$SCRIPT_FILE /usr/local/etc/instal
	cp -r --force instal/.instalModel /usr/local/etc/instal/
	rm -r instal
	rm /usr/local/bin/nichoir
fi
# create link to $SCRIPT_FILE -> new bash command
ln -s -f /usr/local/etc/instal/$SCRIPT_FILE /usr/local/bin/nichoir

# remove self-extracting archive
rm nichoir.sh 1>&2

# permission to execute to all .sh files
find "/usr/local/etc/instal" -name "*.sh" -exec chmod 755 {} \;

# start nichoir installation
nichoir $1

exit 0

ARCHIVE
