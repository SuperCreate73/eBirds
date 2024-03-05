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
	# $4 - separator - defaut '='

	[ -f "$1" ] || return "$BAD_INPUT_FILE"

	local separator='='

	[ ! -z "$4" ] && separator="$4"
	# if parameter already exist
	if grep -q "$2" "$1" ; then
		substitute "^$2$separator.*$:$2$separator$3" "$1" || return "$?"
#		sed "$1" -i -e 's/^'"$2$separator"'.*$/'"$2$separator$3"'/g' > /dev/null || return "$?"
	else
		echo "$2$separator$3" >> $1 || return "$?"
	fi
	return 0
}

function readInputFile()
{
	# $1  pattern of inputFiles to apply
	# $2  function to call for input treatment


	local inputFiles="$1" ; shift
	local callProcess="$1" ; shift
	local parameters="$@"
	local currentLine=""

	# control input files can be retrieved
	if [ `ls "$inputFiles"* 2> /dev/null | wc -l` -eq 0 ] ; then
		return $WRONG_PARAMETER
	fi

	# read lines without comments and send it to the function $2
	# exit on error
	while read -r currentLine ; do
		"$callProcess" "$currentLine" "$parameters" || return $PROCESSING_LINE_ERROR
		#"$callProcess" "$currentLine" "$parameters" 2> /dev/null || return $PROCESSING_LINE_ERROR
	done <<< $(grep -h -e '^[^(#|;|//).*]' `ls "$inputFiles"*`)

	return 0
}

function prgInstallation()
{
	local PRGname=`cut -d ':' -f 1 <<< $*`
	local PRGdescription=`cut -d ':' -f 2 <<< $*`
	local localError=""

	if  ! dpkg -V "$PRGname" > /dev/null 2>&1 ; then  # look if prg already installed
		printMessage "$PRGdescription" "$PRGname"
		apt-get -q -o=Dpkg::Use-Pty=0 --assume-yes install "$PRGname" >> "$LOG_FILE" 2>&1 || return "$INSTALLATION_ERROR"
				# installation avec option 'assume-yes' (oui à toutes les questions)
				# -q -o=Dpkg::Use-Pty=0 - réduit le nombre d'affichages (mode quiet)
				# et écriture de la sortie dans le fichier log (mode ajout)
	fi

	return 0
}

function pythonInstallation()
{
	local PRGname=`cut -d ':' -f 1 <<< $*`
	local PRGdescription=`cut -d ':' -f 2 <<< $*`
	local localError=""

	if [ "$varCheckBib" = true ] || grep -q "$PRGname" <<< $(pip3 freeze) ; then
		printMessage "$PRGdescription" "$PRGname"

		# installation with pip3 utility
	 	pip3 install "$PRGname" >> "$LOG_FILE" 2>&1

		# error processing
		localError=$?
		if [ "$localError" -gt 0 ] ; then
			printError "$localError"
			return $INSTALLATION_ERROR
		fi

	fi

	return 0
}

function createTable()
{
	local DBtable=`cut -d ':' -f 1 <<< $*`
	local DBfields=`cut -d ':' -f 2 <<< $*`

	sqlite3 "$DB_FILE" "CREATE TABLE IF NOT EXISTS $DBtable $DBfields ;" >> "$LOG_FILE" 2>&1 || return "$CREATE_TABLE_ERROR"

	return 0
}

function insertAdmin()
{
	if [ `sqlite3 "$1" "SELECT count() FROM users"` -gt 0 ] ; then
		return 0 	# table not empty
	fi

  # calculate MD5 password
  local adminPwd=$(echo -n "admin" | md5sum | cut -d ' ' -f 1)

	# insert in table
  sqlite3 "$1" "INSERT INTO users ('login', 'password') VALUES ('admin', '$adminPwd');" || return "$INSERT_DB_ERROR"

	return 0
}

function insertRecord()
{
	# insert record from input files given as parameters :
	# $x : input file(s)

	local tmpMain=`cut -d '+' -f 1 <<< $1`
	local tmpRef=`cut -d '+' -f 2 <<< $1`

	if [ -n "$tmpRef" ] ; then
		local tmpTable=`cut -d ':' -f 1 <<< $tmpRef`
		local tmpFields=`cut -d ':' -f 2 <<< $tmpRef`
		local tmpValues=`cut -d ':' -f 3 <<< $tmpRef`

		local ref1=$(sqlite3 "$DB_FILE" "SELECT $tmpFields FROM $tmpTable WHERE $tmpValues ;")
		tmpMain=$(echo "$tmpMain" | sed 's/_REF1_/'"$ref1"'/' )
	fi

	local table=`cut -d ':' -f 1 <<< $tmpMain`
	local fields=`cut -d ':' -f 2 <<< $tmpMain`
	local values=`cut -d ':' -f 3 <<< $tmpMain`

	if [ -n "$table" ] ; then
		sqlite3 "$DB_FILE" "INSERT INTO $table $fields VALUES $values ;" 2> /dev/null || return "$INSERT_DB_ERROR"
	fi

	return 0
}

function substitute()
{
	# substitute string in file using sed command
	# $1 target file
	# $2 original string and substitude string separated by ':'

	local targetFile="$2"
	local originName=`cut -d ':' -f 1 <<< $1`
	local substituteName=`cut -d ':' -f 2 <<< $1`

	# parameters control
	[ -f "$targetFile" ] || return "$WRONG_PARAMETER"  # targetFile is a normal file
	[ -n "$originName" ] || return "$WRONG_PARAMETER"  # original string not null (substitute could be null)

	sed "$targetFile" -i -e "s:$originName:$substituteName:g" || return "$?"

	return 0
}

function motionConfig()
{
	local commentOut=`cut -d ':' -f 1 <<< $1`
	local searchString=`cut -d ':' -f 2 <<< $1`
	local value=`cut -d ':' -f 3 <<< $1`

	if [ "$commentOut" = "C" ] ; then
		commentOut='; '
	else
		commentOut=""
	fi

	substitute "^\(#\|;\)\? \?$searchString.*$:$commentOut$searchString $value" "$2" || return "$?"
	return 0
}
