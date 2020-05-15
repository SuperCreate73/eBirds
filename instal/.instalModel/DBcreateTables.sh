#!/bin/bash
# coding:UTF-8

#######################################################################
# création de la base de donnée vide
#######################################################################
printMessage "creation de la base de données" "nichoir.db"

oldIFS="$IFS"

for varFile in $(ls "$INSTALL_PATH/.input/DBtables*") ; do
	while IFS=: read table fields ; do
		if [ "${table::1}" != '#' ] && [ ${#table} -gt 0 ] ; then
			sqlite3 "$DB_FILE" "CREATE TABLE IF NOT EXISTS $table $fields ;" || printError "$?"
		fi
	done < "$varFile"
done

IFS="$oldIFS"
