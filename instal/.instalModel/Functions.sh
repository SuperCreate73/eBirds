#!/bin/bash
# coding:UTF-8

function usage()
{
# affichage de l'aide
#--------------------
#
	echo -e "\nUsage: sudo $0 [OPTION] "
	echo -e "\nInstalle, met à jour et configure le nichoir"
	echo ""
	echo "Les fichiers nécessaires sont automatiquement téléchargés du serveur ebirds"
	echo ""
	echo "Le script initial doit être copié localement avec la permission d'exécution.  Il"
	echo "est ensuite lancé avec la commande sudo.\n"
	echo "Après l'installation initiale, le programme est accessible depuis une console avec"
	echo "la commande 'sudo nichoir [OPTION]'"
	echo -e "\nVersion : $VERSION"

	echo -e "\n\nOptions de mise à jour:"
	echo "-----------------------"
	echo "  -u    update - télécharge et installe les dernières mises à jours"
	echo "        Comportement par défaut si le nichoir est déjà installé localement"
	echo "  -i    update Install script - réinstallation du script d'installation"
	echo "  -w    update web App - Réinitialisation de l'appli web du nichoir"
	echo "  -s    sanity check - contrôle les versions des programmes et des librairies externes"
	echo "        Programmes installés et librairies python"
	echo "  -m    motion - force la réinitialisation de motion"
	echo "          /!\ les données locales sont préservées mais pas les options de réglages"
	echo "  -f    force install - identique aux options -w, -m et -s"

	echo -e "\n\nAutres options:"
	echo "---------------"
	echo "  -h, -?, ?, --help      affichage de l'aide"
	echo "  -v    verbose - affichage des opérations effectuées"
	echo "  -e    error - affiche uniquement les erreurs dans la console (aussi affichées"
	echo "				en mode 'verbose')"
	echo "  -U    upgrade - upgrade du sytème Linux après installation du nichoir"
	echo "  -l    réinitialisation du fichier log"
	echo "  -d, --debug    log technique de déboggage /usr/local/etc/instal/debug.log"

	echo -e "\n\nExemple d'utilisation:"
	echo "----------------------"
	echo -e "	$0 -uv    installation du nichoir en mode verbeux avec upgrade du système\n"

	return 0
}

function printMessage()
{
# affichage des messages dans le terminal et/ou dans le fichier log
#-------------------------------------------------------------------
# $1 texte explicatif à afficher
# $2 programme à installer

	# préparation de la chaine de texte pour le fichier Log
	varMessage="$1 -- $2 --"

	# affichage dans la console si mode verbeux
	[ "$varVerbose" = true ] &&  echo "$varMessage"

	# préparation de la chaine de séparation '-----------'
	str=""
	(( count = 0 ))

	while [ $count -lt ${#varMessage} ] ; do
		str="$str-"
		(( count += 1 ))
	done

	# écriture dans le fichier log
	echo -e "\n$varMessage \n$str " >> $LOG_FILE
	return 0
}

function printError()
{
# impression des erreurs en console et dans le fichier log
#--------------------------------------------------------
# $1 code d'erreur

	# si le code d'erreur est != 0 -> traitement de l'erreur
	if [ $1 -gt 0 ] ; then
		# incrément du compteur d'erreurs
		(( varErrorCount += 1 ))
		# impression dans logfile
		echo "    ERROR - $varMessage - Error Code $1" >> $LOG_FILE
		# affichage en console si option activée
		[ ! "$varError" ] || echo "    Error on execution - $varMessage - Error Code $1"
	fi
	return 0
}

function updateParameter()
{
	# update des paramètres dans le fichier de config
	#--------------------------------------------------------
	# $1 - fichier de config
	# $2 - paramètre
	# $3 - value
	printMessage "mise à jour du fichier de config - $2" "$1"

	if grep "$2" "$1" ; then
		sed "$1" -i -e 's/^'"$2"'=.*$/'"$2"'='"$3"'/g' > /dev/null || printError "$?"
	else
		echo "$2=$3" >> $1
	fi
	return 0
}


function optionAnalyse()
{
	# boucle parcourant les paramètres fournis
	while [ 1 -le "$#" ] ; do

		if [ "${1:0:2}" = "--" ] ; then
			case "${1:2}" in
				"first")
					# si installateur manuellement downloadé et pas la première instal
					if [ "$varFirstInstal" != true ] ; then
						varCheckBib=true
						varScriptInstal=true
					fi
					;;
				"recall")
					varRecall=true
					;;
				"debug")
					varDebug=true
					echo "####################################################" >> $DEBUG_FILE
					echo $(date)" - instal nichoir debug log" >> $DEBUG_FILE
					echo "####################################################" >> $DEBUG_FILE
					;;
			esac

		# test de la valeur de la variable - regex testant une chaine commançant par - et contenant
		# zero ou une occurance de chacune des lettres autorisées
	elif [[ "$1" =~ ^[-]([delmuUvswfi]+)$ ]]  ; then

			#	affecte le string des paramètres à 'variables' en enlevant le premier '-'
			variables=${1:1}

			#	parcours de la chaine de caractère des paramètres et affectation des
			#   variables d'état des paramètres + tests des combinaisons de paramètres
			for var_count in `seq 0 ${#variables}` ; do
				case ${variables:$var_count:1} in
					"d")
						varDebug=true
						echo "####################################################" >> $DEBUG_FILE
						echo $(date)" - instal nichoir debug log" >> $DEBUG_FILE
						echo "####################################################" >> $DEBUG_FILE
						;;
					"e")
						varError=true
						;;
					"m")
						varMotion=true
						;;
					"u")
						varUpdate=true
						;;
					"l")
						varResetLog=true
						;;
					"U")
						varUpgrade=true
						;;
					"v")
						varVerbose=true
						varError=true
						;;
					"f")
						varCheckBib=true
						varCheckDB=true
						varMotion=true
						varWebAppInstal=true
						;;
					"s")
						varCheckBib=true
						;;
					"i")
						varScriptInstal=true
						;;
					"w")
						varWebAppInstal=true
						varMotion=true
						;;
				esac
			done
#	motif de paramètres non reconnu
		else
			return "$BAD_OPTION"
		fi

	#	passage au paramètre suivant
		shift

	done

	[ $varDebug ] && echo "General options" >> $DEBUG_FILE
	[ $varDebug ] && echo "Config var - varDebug=$varDebug" >> $DEBUG_FILE
	[ $varDebug ] && echo "Config var - varError=$varError" >> $DEBUG_FILE
	[ $varDebug ] && echo "Config var - varVerbose=$varVerbose" >> $DEBUG_FILE
	[ $varDebug ] && echo "Config var - varResetLog=$varResetLog" >> $DEBUG_FILE
	[ $varDebug ] && echo "Config var - varUpgrade=$varUpgrade" >> $DEBUG_FILE
	[ $varDebug ] && echo "Config var - varRecall=$varRecall" >> $DEBUG_FILE
	[ $varDebug ] && echo "System Update Management" >> $DEBUG_FILE
	[ $varDebug ] && echo "Config var - varUpdate=$varUpdate" >> $DEBUG_FILE
	[ $varDebug ] && echo "Config var - varCheckBib=$varCheckBib" >> $DEBUG_FILE
	[ $varDebug ] && echo "Config var - varCheckDB=$varCheckDB" >> $DEBUG_FILE
	[ $varDebug ] && echo "Config var - varScriptInstal=$varScriptInstal" >> $DEBUG_FILE
	[ $varDebug ] && echo "Config var - varWebAppInstal=$varWebAppInstal" >> $DEBUG_FILE
	[ $varDebug ] && echo "Config var - varMotion=$varMotion" >> $DEBUG_FILE

	return 0

}


function doInsertRecord()
{
	# insert record from input files given as parameters
	# $x : input file(s)

	oldIFS="$IFS"

	for varFile in $* ; do
		while read varLine ; do
		    IFS="+"
				read tmpMain tmpRef <<< "$varLine"

				if [ "${tmpMain::1}" != '#' ] ; then
					if [ -n "$tmpRef" ] ; then
						IFS=":" read tmpTable tmpFields tmpValues <<< "$tmpRef"
						ref1=$(sqlite3 "$DB_FILE" "SELECT $tmpFields FROM $tmpTable WHERE $tmpValues ;")
						tmpMain=$(echo "$tmpMain" | sed 's/_REF1_/'"$ref1"'/' )
					fi

					IFS=":"
					read table fields values <<< "$tmpMain"
					printMessage "insertion des paramètres - table: $table - record: F$fields V$values" "nichoir.db"
					if [ -n "$table" ] ; then
						sqlite3 "$DB_FILE" "INSERT INTO $table $fields VALUES $values ;"
						printError "$?"
					fi
			fi

		done < "$varFile"
	done

	IFS="$oldIFS"

	return 0

}
