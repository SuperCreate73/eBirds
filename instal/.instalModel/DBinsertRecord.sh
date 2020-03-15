#!/bin/bash
# coding:UTF-8

# Insertion des champs dans la DB
#######################################################################
# insertion des paramètres dans la base de données
#######################################################################
printMessage "insertion des paramètres - table 'config'" "nichoir.db"

oldIFS="$IFS"

for varFile in $(ls "$varInstalPath"/.input/DBinsert*) ; do
	while read varLine ; do
	    IFS="+"
			read tmpMain tmpRef <<< "$varLine"

			if [ "${tmpMain::1}" != '#' ] ; then
				if [ -n "$tmpRef" ] ; then
					IFS=":" read tmpTable tmpFields tmpValues <<< "$tmpRef"
					ref1=$(sqlite3 /var/www/nichoir.db "SELECT $tmpFields FROM $tmpTable WHERE $tmpValues ;")
					tmpMain=$(echo "$tmpMain" | sed 's/_REF1_/'"$ref1"'/' )
				fi

				IFS=":"
				read table fields values <<< "$tmpMain"

				if [ -n "$table" ] ; then
					sqlite3 /var/www/nichoir.db "INSERT INTO $table $fields VALUES $values ;"
					printError "$?"
				fi
		fi

	done < "$varFile"
done

IFS="$oldIFS"
