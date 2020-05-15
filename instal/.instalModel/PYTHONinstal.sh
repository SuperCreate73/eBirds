#!/bin/bash
# coding:UTF-8

# si l'option de vérifier les bibliothèques est activée, lance PIP3 dans tous les cas
# sinon, stocke la liste de bibliothèques dans la variable libraryList et vérifie
# l'existence avant l'installation

oldIFS="$IFS"

for varFile in $(ls "$INSTALL_PATH"/.input/PYTHONlist*) ; do
	# stocke la liste des bibliothèque installées si nécessaire
	[ "$varCheckBib" = true ] || libraryList=$(pip3 freeze)
	while IFS=: read program description ; do

		if [ "${program::1}" != '#' ]  &&  [ ${#program} -gt 0 ]  ; then
			printMessage "$description" "$program"

			if [ ! "$varCheckBib" = true ] && grep "$program" <<< $libraryList > /dev/null ; then
				# itération suivante sans passer par la fin de boucle
				continue
			fi

			# installation avec option pip3
			pip3 install "$program" >> "$LOG_FILE" 2>&1

			# gestion des erreurs éventuelles
			printError "$?"
		fi

	done < "$varFile"
done

IFS="$oldIFS"
