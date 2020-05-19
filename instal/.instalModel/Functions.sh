#!/bin/bash
# coding:UTF-8

function usage()
{
# affichage de l'aide
#--------------------
#
	echo "\nUsage: sudo $0 [OPTION] "
	echo "\nInstalle, met à jour et configure le nichoir"
	echo ""
	echo "Les fichiers nécessaires sont automatiquement téléchargés du serveur ebirds"
	echo ""
	echo "Le script initial doit être copié localement avec la permission d'exécution.  Il"
	echo "est ensuite lancé avec la commande sudo.\n"
	echo "Après l'installation initiale, le programme est accessible depuis une console avec"
	echo "la commande 'sudo nichoir [OPTION]'"
	echo "\n\nOptions:"
	echo "-------"
	echo "  -e    error - affiche uniquement les erreurs dans la console (aussi affichées"
	echo "				en mode 'verbose')"
	echo "  -h    help - affichage de l'aide"
	echo "  -m    mise à jour - télécharge et installe les dernières mises à jours"
	echo "        Comportement par défaut si le nichoir est déjà installé localement"
	echo "  -r    reset - réinitialisation du fichier log"
	echo "  -u    upgrade - upgrade du sytème Linux après installation du nichoir"
	echo "  -v    verbose - affichage des opérations effectuées"
	echo "  -c    check versions - contrôle les versions des programmes et des librairies externes"
	echo "        Programmes installés et librairies python"
	echo "  -f    force install - force la réinstallation complète du nichoir, les données locales étant préservées"
	echo "  -s    sanity check - identique aux options -c et -f"
	echo "  -i    force update Install script - force la réinstallation du programme d'installation"
	echo "\nExemple d'utilisation :"
	echo "	$0 -uv    installation du nichoir en mode verbeux avec upgrade du système\n"

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
						varForceInstal=true
						varLoadInstal=true
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
	elif [[ "$1" =~ ^[-]([deglmruvcsfi]+)$ ]]  ; then

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
					"g")
						varGit=true
						;;
					"l")
						varLocal=true
						;;
					"m")
						varUpdate=true
						;;
					"r")
						varReset=true
						;;
					"u")
						varUpgrade=true
						;;
					"v")
						varVerbose=true
						varError=true
						;;
					"c")
						varCheckBib=true
						;;
					"f")
						varForceInstal=true
						;;
					"s")
						varCheckBib=true
						varForceInstal=true
						varLoadInstal=true
						;;
					"i")
						varLoadInstal=true
						;;
				esac
			done
#	motif de paramètres non reconnu
		else
			exit "$BAD_OPTION"
		fi

	#	passage au paramètre suivant
		shift
	done
	return 0

}

function doMotionVersion ()
{
	# update input file with option names of current motion verion
	# $1 fichier de paramètres Motion à traiter
	# $2 fichier dans lequel effectuer les remplacements

	OLDIFS="$IFS"

	while IFS=: read referenceName substituteName ; do
		sed "$2" -i -e "s/$referenceName/$substituteName/g"
	done < $(grep -e '^[^(#|;).*]' "$1")

	IFS="$OLDIFS"
	return 0
}

function doInsertRecord ()
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

}
