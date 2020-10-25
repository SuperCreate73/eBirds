#!/bin/bash
# coding:UTF-8

function usage()
{
# print help :
#

	echo -e "\nUsage: sudo $0 [OPTION] "

	echo -e "\nInstalle, met à jour et configure le nichoir"
	echo "Les fichiers nécessaires sont automatiquement téléchargés du serveur ebirds."

	echo -e "\nLe script initial doit être copié localement avec la permission d'exécution.  Il"
	echo "est ensuite lancé avec la commande sudo."

	echo -e "\nAprès l'installation initiale, le programme est accessible depuis la console avec"
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
	echo "  -f    force install - identique aux options -w, -m, -V et -s"
	echo "  -V    Ecrase le fichier version.sh avec la version du serveur"

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
# print messages on terminal and/or log file :
# $1 description of current operation
# $2 item

	varMessage="$1 -- $2 --" 		# message format

	[ "$varVerbose" = true ] &&  echo "$varMessage"  	# write in console if applicable

	# prepare separation string '-----------'
	str=""
	(( count = 0 ))

	while [ $count -lt ${#varMessage} ] ; do
		str="$str-"
		(( count += 1 ))
	done

	echo -e "\n$varMessage \n$str " >> $LOG_FILE		# write in logfile

	return 0
}

function printError()
{
# print errors on console and log file :
# $1 error number
#

	if [ $1 -gt 0 ] ; then 	# if error number > 0
		(( varErrorCount += 1 ))	# error count increment
		echo "#### ERROR #### $varMessage - Error Code $1" >> $LOG_FILE			# write in logfile
		[ "$varError" ] && echo "    Error on execution - $varMessage - Error Code $1"	# write in console if applicable
	fi

	return 0
}

function updateParameter()
{
	# parameter update in config file :
	# $1 - config file
	# $2 - parameter
	# $3 - value

	if grep -q "$2" "$1" ; then
		sed "$1" -i -e 's/^'"$2"'=.*$/'"$2"'='"$3"'/g' > /dev/null || printError "$?"
	else
		echo "$2=$3" >> $1
	fi
	return 0
}


function optionAnalyse()
{
	# analyse of command line Options :
	#

	while [ 1 -le "$#" ] ; do

		if [ "${1:0:2}" = "--" ] ; then
			case "${1:2}" in
				"first")	# manual download of install script
					if [ "$varFirstInstal" = false ] ; then 	# not the first install
						varCheckBib=true
						varScriptInstal=true
					fi
					;;
				"recall")		# restart of install script in case of main install update
					varRecall=true
					;;
				"debug")		# debug option
					varDebug=true
					echo -e "\n\n####################################################" >> $DEBUG_FILE
					echo $(date)" - instal nichoir debug log" >> $DEBUG_FILE
					echo "####################################################" >> $DEBUG_FILE
					;;
			esac

		# check the parameter value - regex beginning with '-' and containing zero or one authorised option
		elif [[ "$1" =~ ^[-]([delmuUvVswfi]+)$ ]]  ; then

			variables=${1:1}	#	parameters string affected to 'variables' without first character '-'

			for var_count in `seq 0 ${#variables}` ; do  	# iteration char by char of the parameters string
				case ${variables:$var_count:1} in		# variables initialisation
					"d")
						varDebug=true
						echo -e "\n\n####################################################" >> $DEBUG_FILE
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
						varCopyConfig=true
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
					"V")
						varCopyConfig=true
						;;
				esac
			done

		else	#	unknow paramter -> error
			return "$BAD_OPTION"
		fi

		shift		#	next parameters
	done

	# variables status written to debug file
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
	# insert record from input files given as parameters :
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
