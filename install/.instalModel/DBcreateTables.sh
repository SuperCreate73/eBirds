#!/bin/bash
# coding:UTF-8

#######################################################################
# création de la base de donnée vide
#######################################################################
printMessage "creation de la base de données" "nichoir.db"
# TODO adapt path for table creation file

oldIFS=$IFS

for varFile in $(ls /home/pi/.instal/.input/DBtables*) ; do
	while IFS=: read table fields ; do
		if test -n "$table" ; then
			sqlite3 /var/www/nichoir.db "CREATE TABLE IF NOT EXISTS $table $fields ;"
			printError "$?"
		fi
	done < $varFile
done

IFS=$oldIFS
