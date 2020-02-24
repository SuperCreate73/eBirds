#!/bin/bash
# coding:UTF-8

oldIFS=$IFS

for varFile in $(ls /home/pi/.instal/.input/PYTHONlist*) ; do
	while IFS=: read description program ; do

		if test -n "$program" ; then
			printMessage "$description" "$program"

			# installation avec option pip3
			pip3 install $program >> $varLogFile 2>&1

			# gestion des erreurs éventuelles
			printError "$?"
		fi

	done < $varFile
done

IFS=$oldIFS
