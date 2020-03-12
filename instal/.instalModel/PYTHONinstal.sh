#!/bin/bash
# coding:UTF-8

oldIFS=$IFS

for varFile in $(ls $varInstalPath/.input/PYTHONlist*) ; do
	while IFS=: read program description ; do

		if [ ${program::1} != '#' ]  &&  [ ${#program} -gt 0 ]  ; then
			printMessage "$description" "$program"

			# installation avec option pip3
			pip3 install $program >> $varLogFile 2>&1

			# gestion des erreurs éventuelles
			printError "$?"
		fi

	done < $varFile
done

IFS=$oldIFS
