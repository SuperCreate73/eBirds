#!/bin/bash
# coding:UTF-8

oldIFS="$IFS"

for varFile in $(ls "$varInstalPath"/.input/PRGlist*) ; do
	while IFS=: read program description ; do

		if [ "${program::1}" != '#' ] && [ ${#program} -gt 0 ]  ; then

			if  ! dpkg -V "$program" > /dev/null 2>&1 ; then
				printMessage "$description" "$program"
				# installation avec option 'assume-yes' (oui à toutes les questions)
				# -q -o=Dpkg::Use-Pty=0 - réduit le nombre d'affichages (mode quiet)
				# et écriture de la sortie dans le fichier log (mode ajout)
				apt-get -q -o=Dpkg::Use-Pty=0 --assume-yes install "$program" >> "$varLogFile" 2>&1

				# gestion des erreurs éventuelles
				printError "$?"
			fi
		fi

	done < "$varFile"
done

IFS="$oldIFS"

printMessage "vérification des dépendances" "tous paquets"
sudo apt-get install --fix-missing >> "$varLogFile" 2>&1
printError "$?"