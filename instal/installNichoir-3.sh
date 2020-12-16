#!/bin/bash
# coding:UTF-8

# date : 27/05/2019
# créateur : bibi
# license : dwyw (do what you want)

#
# Le script est installé dans le répertoire /usr/local/etc/instal. Un lien
# symbolique est généré dans le répertoire /usr/local/bin pour permettre l'appel
# depuis la console. Le nom du lien symbolique est 'nichoir'
#
# Lancer le script en sudo : $ sudo nichoir avec les options
# éventuelles.
# Pour plus d'informations, lancer 'nichoir' avec l'option -h, --help ou ?
#
#   ###########################################

# IDEA lire un fichier avec la configuration d'installation :
# 			Quels capteurs ?
# 			Quels types ?
# 			Fonction 'read' à utiliser (propre à chaque capteur)
# 			Paramètres et format à donner à la fonction 'read' (pin, ?)
#
# IDEA modifier la BD pour faire une seule table capteur
# 			table de correspondance ID - label, unité (degré, %, ?), fonction read et paramètres
#
# TODO Installation de PHP à revoir -> php installe par défaut un serveur WEB
# 			qui entre en conflit avec Lighttpd
#
# TODO existing record protection when inserting in DB
#
#   ###########################################

#######################################################################
# initialisation des variaPROCESSING_LINE_ERROR=71 # unable to process input linebles, paramètres et constantes
#######################################################################
# config constant
INSTALL_ROOTPATH="/usr/local/etc/instal"
WEBAPP_ROOTPATH="/var/www"
DB_FILE="$WEBAPP_ROOTPATH/nichoir.db"
WEB_PATH="$WEBAPP_ROOTPATH/html"

SCRIPT_FILE="installNichoir-3.sh"
LOG_FILE="$INSTALL_ROOTPATH/logInstal.log"
DEBUG_FILE="$INSTALL_ROOTPATH/debug.log"
VERSION="1.1 - 01-06-2020"

# error constant
BAD_OPTION=65			# unknow option used
BAD_USER=66				# No root user
GIT_ERROR=67			# unable to install git
SOURCES_ERROR=68	# source files not found
WRONG_PARAMETER=69	# wrong parameter sent to function
BAD_INPUT_FILE=70 # bad input file sent to function
PROCESSING_LINE_ERROR=71 # unable to process input line
INSTALLATION_ERROR=72 # program installation process error
CREATE_DIR_ERROR=73 # unable to create dir
CREATE_SYMLINK_ERROR=74 # unable to create symLink
CREATE_TABLE_ERROR=75	# error creating DB table
INSERT_DB_ERROR=76 # Error when inserting in DB
SUBSTITUTE_ERROR=77	# Error during sed substitution

# options variables
varVerbose=false	# display status messages on terminal
varError=false		# display errors on terminal
varResetLog=false	# clean log file
varDebug=false		# debug messages
varUpgrade=false	# linux system upgrade
varUpdate=false		# default, not used
varRecall=false		# internal - in case of instal script update
varCheckBib=false  # instal python library
varMotion=false		# configure motion tables, configFile & viewReglages
varCheckDB=false	# initialize or update DB & tables (no records, only structure)
varWebAppInstal=false # instal local web app
varScriptInstal=false # (re)instal update script
varCopyConfig=false # (re)init config file with template

# script variables
varMessage=""	  # message to display on screen or save in log file
varErrorCount=0		# error count
varFirstInstal=true   # first installation flag -> false if DB already exists

#######################################################################
# contrôle du user et des paramètres du script
#######################################################################

# déclaration des fonctions de base du script - usage, printLog, ...
source "$INSTALL_ROOTPATH/.instalModel/Functions.sh"
source "$INSTALL_ROOTPATH/.instalModel/FunctionsHelpers.sh"

# check for help option
if [ "$1" = "-h" ] || [ "$1" = "--help" ] || [ "$1" = "?" ] || [ "$1" = "-?" ] ; then
	usage
	exit 0
fi

# check for superUser right
if [ "$EUID" -ne 0 ] ; then
	echo -e "Ce script doit être exécuté avec les droits de SuperUser." 1>&2
	exit "$BAD_USER"
fi

# script parameter analyse
#-------------------------
# look if first instal by checking if DB_file exist
if [ -e "$DB_FILE" ] ; then
	varFirstInstal=false
fi

# script parameter analyse
if [ "$#" -gt 0 ] ; then  # if number of script parameter > 0

	varAllParams="$@" 	# backup of original parameter -> used if script is re-run in case of script update

	optionAnalyse "$@" 	# call of function that analyse script parameters (Functions.sh)

	if [ "$?" = "$BAD_OPTION" ] ; then	# error in script parameters
			echo -e "\nOption(s) non reconnue(s)"
			echo "used options : $varAllParams "
			echo "------------------------"
			usage
			[ "$varDebug" ] && echo "Bad option error -> list=$varAllParams" >> $DEBUG_FILE
			exit "$BAD_OPTION"
	fi
else
	optionAnalyse "-u"   # default behaviour
fi

[ "$varDebug" ] && echo "Options analyse successfull-> list=$varAllParams" >> $DEBUG_FILE

# look if first instal by checking if DB_file exist
if [ "$varFirstInstal" ] ; then
	optionAnalyse "-fli" # init variables for first install
fi

# initialisation des variables de version (issues du fichier local des versions)
if [ -e "$INSTALL_ROOTPATH/.config/versions.sh" ] ; then
	source "$INSTALL_ROOTPATH/.config/versions.sh" || printError "$?"  # upload of module version variables

	[ "$varDebug" ] && echo "version.sh loaded" >> $DEBUG_FILE
else
	verInstal=""
	optionAnalyse "-iV" # true for varScriptInstal & varCopyConfig

	[ "$varDebug" ] && echo "version.sh not present - update is configured" >> $DEBUG_FILE
fi

#######################################################################
# Initialisation
#######################################################################

# create and define encoding of Log file
#---------------------------------------
[ "$varResetLog" = true ] && [ -e "$LOG_FILE" ] && removeFile "$LOG_FILE"
if [ ! -e "$LOG_FILE" ] ; then
	printMessage "Création et paramétrage du fichier log" "$LOG_FILE"
	createFile "$LOG_FILE" "# coding:UTF-8 " || printError "$?"

	[ "$varDebug" ] && echo "Log File created" >> $DEBUG_FILE
fi

# print separator and current date in logfile
echo -e "\n\n########################################## \n$(date)\n\n" >> "$LOG_FILE"

#######################################################################
# installation & configuration of modules
#######################################################################

# Upload sources & instal script update
#-------------------------------------
if [ ! "$varRecall" = true ] ; then
	printMessage "Mise à jour du système linux" "update"
	apt-get --quiet --assume-yes update >> $LOG_FILE 2>&1 || printError "$?"

	[ "$varDebug" ] && echo "Update linux system done" >> $DEBUG_FILE

	# installe GIT si pas déjà fait
	if  ! dpkg -V "git" > /dev/null 2>&1 ; then
		printMessage "Installation de GIT" "git"
		# installation avec option 'assume-yes' (oui à toutes les questions)
		# -q -o=Dpkg::Use-Pty=0 - réduit le nombre d'affichages (mode quiet)
		# et écriture de la sortie dans le fichier log (mode ajout '>>' )
		# si une erreur est générée, exécute 'printError'
		apt-get -q -o=Dpkg::Use-Pty=0 --assume-yes install git >> "$LOG_FILE" 2>&1 || (printError "$?" ; exit "$GIT_ERROR")
	fi

	[ "$varDebug" ] && echo "Install GIT done" >> $DEBUG_FILE

	# téléchargement des fichiers eBirds dans le répertoire courant
	printMessage "téléchargement des fichiers sources depuis GitHub" "https://github.com/SuperCreate73/eBirds.git"
	git clone --quiet "https://github.com/SuperCreate73/eBirds.git" >> $LOG_FILE 2>&1 || (printError "$?" ; exit "$SOURCES_ERROR")

	[ "$varDebug" ] && echo "Download files from GitHub done" >> $DEBUG_FILE

	# Extraction de la version de l'installateur de GitHub
	lvTempVersion=`grep "verInstal" "eBirds/instal/.config/versions.sh" | cut -d '=' -f 2`

	### Script files installation ###
	if [ ! "$lvTempVersion" = "$verInstal" ] || [ "$varScriptInstal" = true ] ; then

		# Copy script files
		copyFiles "eBirds/instal/$SCRIPT_FILE" "$INSTALL_ROOTPATH" || printError "$?"
		copyDir "eBirds/instal/.instalModel" "$INSTALL_ROOTPATH" || printError "$?"
		copyDir "eBirds/instal/.input" "$INSTALL_ROOTPATH" || printError "$?"
		copyDir "eBirds/instal/motion" "$INSTALL_ROOTPATH" || printError "$?"

		# Copy version file
		if [ "$varCopyConfig" = true ] ; then
			copyFiles "eBirds/instal/.config/versions_init.sh" "$INSTALL_ROOTPATH/.config/versions.sh" || printerror "$?"
		fi

		# create link to $SCRIPT_FILE -> new bash command
		printMessage "création du lien symbolique" "/usr/local/bin/nichoir"
		createSymLink "$INSTALL_ROOTPATH/$SCRIPT_FILE" /usr/local/bin/nichoir || printError "$?"

		# permission to execute to all .sh files
		# donne la permission en exécution aux fichiers .sh
		# 	-> recherche des fichiers *.sh dans le répertoire d'install
		printMessage "gestion des permissions des fichiers d'instal" "chmod 755"
		find "$INSTALL_ROOTPATH" -name "*.sh" -exec chmod 755 {} \; || printError "$?"

		printMessage "mise à jour du fichier de config - verInstal" "$INSTALL_ROOTPATH/.config/versions.sh"
		updateParameter "$INSTALL_ROOTPATH/.config/versions.sh" "verInstal" "$lvTempVersion" || printError "$?"

		[ "$varDebug" ] && echo "Modify instal script done -> reloading script" >> $DEBUG_FILE

		# recall nichoir with initial parameters for applying new install version
		sudo nichoir "--recall" "$varAllParams"

		exit 0

	fi
fi
#######################################################################
#######################################################################

# external programs installation - internal control if already installed
lvTempVersion=`grep "verPrgInstal" "eBirds/instal/.config/versions.sh" | cut -d '=' -f 2`

if [ ! "$lvTempVersion" = "$verPrgInstal" ] || [ "$varCheckBib" = true ] ; then

	# TODO Installation de PHP à revoir -> php installe par défaut un serveur WEB qui entre en conflit avec Lighttpd

	printMessage "installation des programmes" "$INSTALL_ROOTPATH/.input/PRGlist*.sh"
	readInputFile "$INSTALL_ROOTPATH/.input/PRGlist" "prgInstallation" || printError "$?"

	printMessage "vérification des dépendances" "tous paquets"
	sudo apt-get install --fix-missing >> "$LOG_FILE" 2>&1 || printError "$?"

	printMessage "mise à jour du fichier de config - verPrgInstal" "$INSTALL_ROOTPATH/.config/versions.sh"
	updateParameter "$INSTALL_ROOTPATH/.config/versions.sh" "verPrgInstal" "$lvTempVersion" || printError "$?"

	[ "$varDebug" ] && echo "Program installation done" >> $DEBUG_FILE

fi

#--------------------------------------------------------------------
# installation des capteurs / bibliothèques python - contrôle dans pip3 si déjà existant
lvTempVersion=`grep "verPythonLib" "eBirds/instal/.config/versions.sh" | cut -d '=' -f 2`

if [ ! "$lvTempVersion" = "$verPythonLib" ]  || [ "$varCheckBib" = true ] ; then

	printMessage "installation des bibliothèques python" "$INSTALL_ROOTPATH/.input/PYTHONlist*"
	readInputFile "$INSTALL_ROOTPATH/.input/PYTHONlist" "pythonInstallation" || printError "$?"

	printMessage "mise à jour du fichier de config - verPythonLib" "$INSTALL_ROOTPATH/.config/versions.sh"
	updateParameter "$INSTALL_ROOTPATH/.config/versions.sh" "verPythonLib" "$lvTempVersion" || printError "$?"

	[ "$varDebug" ] && echo "Python Library installation done" >> $DEBUG_FILE

fi

#--------------------------------------------------------------------
# copie des fichiers eBirds - site local
lvTempVersion=`grep "verNichoirFiles" "eBirds/instal/.config/versions.sh" | cut -d '=' -f 2`

if [ ! "$lvTempVersion" = "$verNichoirFiles" ]  || [ "$varWebAppInstal" = true ] ; then
	source "$INSTALL_ROOTPATH/.instalModel/FILESinstal.sh"

	printMessage "mise à jour du fichier de config - verNichoirFiles" "$INSTALL_ROOTPATH/.config/versions.sh"
	updateParameter "$INSTALL_ROOTPATH/.config/versions.sh" "verNichoirFiles" "$lvTempVersion" || printError "$?"


	[ "$varDebug" ] && echo "Local web site update done" >> $DEBUG_FILE

fi

#--------------------------------------------------------------------
# creation de la base de donnees et initialisation de la table 'user'
lvTempVersion=`grep "verDB" "eBirds/instal/.config/versions.sh" | cut -d '=' -f 2`

if [ ! "$lvTempVersion" = "$verDB" ]  || [ "$varCheckDB" = true ] ; then
	# création de la base de donnée
	printMessage "Creation des tables DB" "$INSTALL_ROOTPATH/.input/DBtables*.sh"
	readInputFile "$INSTALL_ROOTPATH/.input/DBtables" "createTable" || printError "$?"

	[ "$varDebug" ] && echo "DB / table creation done" >> $DEBUG_FILE

	# insert defaut User
	printMessage "insertion de l'utilisateur admin (password = admin)" "$1"
  insertAdmin "$DB_FILE" || printerror "$?"

	[ "$varDebug" ] && echo "DB / admin user insert done" >> $DEBUG_FILE

	printMessage "mise à jour du fichier de config - verDB" "$INSTALL_ROOTPATH/.config/versions.sh"
	updateParameter "$INSTALL_ROOTPATH/.config/versions.sh" "verDB" "$lvTempVersion"
fi

#--------------------------------------------------------------------
# initialisation des tables et config initiale
if [ "$varFirstInstal" = "true" ] ; then
	# remplissage des tables
	# TODO existing record protection

	printMessage "remplissage des tables DB" "nichoir.db"
	readInputFile "$INSTALL_ROOTPATH/.input/DBinsert" "insertRecord" || printError "$?"

	[ "$varDebug" ] && echo "DB / tables insert done" >> $DEBUG_FILE

	source "$INSTALL_ROOTPATH/.instalModel/CONFIGinit.sh"

	[ "$varDebug" ] && echo "Initial config done" >> $DEBUG_FILE

fi

#--------------------------------------------------------------------
# config de motion
source "$INSTALL_ROOTPATH/.instalModel/CONFIGmotion.sh"

[ "$varDebug" ] && echo "Motion config done" >> $DEBUG_FILE

#--------------------------------------------------------------------
# nettoyage des fichiers résiduels
printMessage "nettoyage des fichiers résiduels" "rm -r eBirds"
rm -r eBirds || printError "$?"

[ "$varDebug" ] && echo "File clean up done" >> $DEBUG_FILE

#######################################################################
# upgrade du système linux
#######################################################################
if [ "$varUpgrade" == true ] ; then
	printMessage "Mise à jour du système linux" "upgrade"
	apt-get --quiet --assume-yes upgrade  >> $LOG_FILE 2>&1 || printError "$?"

	[ "$varDebug" ] && echo "Linux upgrade done" >> $DEBUG_FILE

	printMessage "Mise à jour du système linux" "dist-upgrade"
	apt-get --quiet --assume-yes dist-upgrade >> $LOG_FILE 2>&1 || printError "$?"

	[ "$varDebug" ] && echo "Linux dist-upgrade done" >> $DEBUG_FILE

fi

#######################################################################
# sortie du script
#######################################################################
if [ "$varErrorCount" -gt 0 ] ; then
	echo -e "\n\nNombre d'erreurs : $varErrorCount - Consultez le fichier $logFile pour plus d'informations"
	echo -e "\n\nNombre d'erreurs : $varErrorCount" >> $LOG_FILE
	exit 1
elif [ "$varFirstInstal" = "true" ] ; then
	echo -e "\n\nL'installation du nichoir est maintenant terminée (aucune erreur)"
	echo -e "\n\nLe redémarrage du nichoir est vivement conseillé !"
	echo -e "\n\nL'installation du nichoir est maintenant terminée - aucune erreur" >> $LOG_FILE
	exit 0
else
	echo -e "\n\nLa mise à jour du nichoir est terminée - aucune erreur"
	echo -e "\n\n"
	echo -e "\n\nLa mise à jour du nichoir est terminée - aucune erreur" >> $LOG_FILE
	exit 0
fi
