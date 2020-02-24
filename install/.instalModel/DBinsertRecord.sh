#!/bin/bash
# coding:UTF-8

# Insertion des champs dans la DB
#######################################################################
# insertion des paramètres dans la base de données
#######################################################################
printMessage "insertion des paramètres - table 'config'" "nichoir.db"

# TODO adapt path for table creation file

oldIFS=$IFS

for varFile in $(ls /home/pi/.instal/.input/DBinsert*) ; do
	while read varLine ; do
    IFS="+"
		read tmpMain tmpRef <<< "$varLine"

		if [ -n "$tmpRef" ] ; then
			IFS=":" read tmpTable tmpFields tmpValues <<< "$tmpRef"
			ref1=$(sqlite3 ~/nichoir.db "SELECT $tmpFields FROM $tmpTable WHERE $tmpValues ;")
			tmpMain=$(echo "$tmpMain" | sed 's/_REF1_/'"$ref1"'/' )
		fi

		IFS=":"
		read table fields values <<< "$tmpMain"

		if [ -n "$table" ] ; then
			sqlite3 ~/nichoir.db "INSERT INTO $table $fields VALUES $values ;"
			printError "$?"
		fi

	done < $varFile
done

IFS=$oldIFS
