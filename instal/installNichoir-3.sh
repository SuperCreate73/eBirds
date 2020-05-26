#!/bin/bash
# coding:UTF-8

# date : 27/05/2019
# créateur : bibi
# license : dwyw (do what you want)

#
# Le script est installé dans le répertoire /usr/local/etc/instal et un lien
# symbolique est généré dans le répertoire /usr/local/bin pour permettre l'appel
# depuis la console. Le nom du lien symbolique est 'nichoir'
#
# Lancer le script en sudo : $ sudo nichoir avec les options
# éventuelles.
# Pour plus d'informations, lancer 'nichoir' avec l'option -h, --help ou ?
#
#
#
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
# TODO replace AWK by SED command in CONFIGinit.sh
#
#   ###########################################

#######################################################################
# initialisation des paramètres et constantes + contrôle USER et HELP
#######################################################################

# config constant
INSTALL_PATH="/usr/local/etc/instal"
LOG_FILE="$INSTALL_PATH/logInstal.log"
DB_FILE="/var/www/nichoir.db"
DEBUG_FILE="/usr/local/etc/instal/debug.log"

# error constant
BAD_USER=1
BAD_OPTION=2

# déclaration des fonctions de base du script - usage, printLog, ...
source "$INSTALL_PATH/.instalModel/Functions.sh"

# check for help option
if [ "$1" = "-h" ] || [ "$1" = "--help" ] || [ "$1" = "?" ] || [ "$1" = "-?" ] ; then
	usage
	exit 0
fi

# check for superUser right
if [[ "$EUID" -ne 0 ]] ; then
	echo -e "Ce script doit être exécuté avec les droits de SuperUser." 1>&2
	exit "$BAD_USER"
fi

# options variables
varUpgrade=false
varVerbose=false
varReset=false
varError=false
varLocal=false
varGit=true
varServer=false
varUpdate=false
varRecall=false
varCheckBib=false
varForceInstal=false
varDebug=false

# message to display on screen or save in log file
varMessage=""

# error count
varErrorCount=0

# options analyse
#----------------

# test de la présence de paramètres (nombre de paramètres supérieur à 0)
if [ "$#" -gt 0 ] ; then

	# backup of original parameter -> used if script is re-run
	varAllParams="$@"

	# call of function that analyse options and init flags
	optionAnalyse "$@"

	if [ "$?" = "$BAD_OPTION" ] ; then
			echo "Option(s) non reconnue(s)"
			echo "------------------------"
			usage
			[ "$varDebug" ] && echo "Bad option error -> list=$varAllParams" >> $DEBUG_FILE
			exit "$BAD_OPTION"
	fi
fi

[ "$varDebug" ] && echo "Options analyse successfull-> list=$varAllParams" >> $DEBUG_FILE


# look if first instal by checking if DB_file exist
if [ -e "$DB_FILE" ] ; then
	varFirstInstal=false
else
	varFirstInstal=true
fi

echo "Config var - varDebug=$varDebug" > $DEBUG_FILE
[ $varDebug ] && echo "Config var - varFirstInstal=$varFirstInstal" >> $DEBUG_FILE

# initialisation des variables de version (issu du fichier local des versions)
source "$INSTALL_PATH/.config/versions.sh" || printError "$?"

[ "$varDebug" ] && echo "version.sh loaded" >> $DEBUG_FILE


#######################################################################
# Initialisation
#######################################################################


# écriture de l'encodage du fichier log si pas encore existant
#-------------------------------------------------------
if [ ! -e "$LOG_FILE" ] || [ "$varReset" = true ] ; then
	printMessage "Création et paramétrage du fichier log" "$LOG_FILE"
	echo -e "# coding:UTF-8 \n\n" > $LOG_FILE || printError "$?"
	[ "$varDebug" ] && echo "Log File created" >> $DEBUG_FILE
fi

#######################################################################
# installation et configuration du système
#######################################################################

# Téléchargement des sources et mise à jour de l'installateur
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
		apt-get -q -o=Dpkg::Use-Pty=0 --assume-yes install git >> "$LOG_FILE" 2>&1 || printError "$?"
	fi

	[ "$varDebug" ] && echo "Install GIT done" >> $DEBUG_FILE

	# téléchargement des fichiers eBirds dans le répertoire courant
	printMessage "téléchargement des fichiers sources depuis GitHub" "https://github.com/SuperCreate73/eBirds.git"
	git clone --quiet "https://github.com/SuperCreate73/eBirds.git" >> $LOG_FILE 2>&1 || printError "$?"

	[ "$varDebug" ] && echo "Download files from GitHub done" >> $DEBUG_FILE

	# Extraction de la version de l'installateur de GitHub
	lvTempVersion=`grep "verInstal" "eBirds/instal/.config/versions.sh" | cut -d '=' -f 2`

	if [ ! "$lvTempVersion" = "$verInstal" ] || [ "$varLoadInstal" = true ] ; then

		source "$INSTALL_PATH/.instalModel/SCRIPTinstal.sh"
		updateParameter "$INSTALL_PATH/.config/versions.sh" "verInstal" "$lvTempVersion"

		[ "$varDebug" ] && echo "Modify instal script done -> reloading script" >> $DEBUG_FILE

		# recall nichoir with initial parameters for applying new install version
		sudo nichoir "--recall" "$varAllParams"

		exit 0

	fi
fi
#######################################################################
#######################################################################

# installation programmes - contrôles internes si déjà existant
lvTempVersion=`grep "verPrgInstal" "eBirds/instal/.config/versions.sh" | cut -d '=' -f 2`

if [ ! "$lvTempVersion" = "$verPrgInstal" ] || [ "$varCheckBib" = true ] ; then
	source "$INSTALL_PATH/.instalModel/PRGinstal.sh"
	updateParameter "$INSTALL_PATH/.config/versions.sh" "verPrgInstal" "$lvTempVersion"

	[ "$varDebug" ] && echo "Program installation done" >> $DEBUG_FILE

fi

#--------------------------------------------------------------------
# installation des capteurs / bibliothèques python - contrôle dans pip3 si déjà existant
lvTempVersion=`grep "verPythonLib" "eBirds/instal/.config/versions.sh" | cut -d '=' -f 2`

if [ ! "$lvTempVersion" = "$verPythonLib" ]  || [ "$varCheckBib" = true ] ; then
	source "$INSTALL_PATH/.instalModel/PYTHONinstal.sh"
	updateParameter "$INSTALL_PATH/.config/versions.sh" "verPythonLib" "$lvTempVersion"

	[ "$varDebug" ] && echo "Python Library installation done" >> $DEBUG_FILE

fi

#--------------------------------------------------------------------
# copie des fichiers eBirds - site local
lvTempVersion=`grep "verNichoirFiles" "eBirds/instal/.config/versions.sh" | cut -d '=' -f 2`

[ "$varDebug" ] && echo "\$varForceInstal = $varForceInstal" >> $DEBUG_FILE
if [ ! "$lvTempVersion" = "$verNichoirFiles" ]  || [ "$varForceInstal" = true ] ; then
	source "$INSTALL_PATH/.instalModel/FILESinstal.sh"
	updateParameter "$INSTALL_PATH/.config/versions.sh" "verNichoirFiles" "$lvTempVersion"

	[ "$varDebug" ] && echo "Local web site update done" >> $DEBUG_FILE

fi

#--------------------------------------------------------------------
# creation de la base de donnees et initialisation de la table 'user'
lvTempVersion=`grep "verDB" "eBirds/instal/.config/versions.sh" | cut -d '=' -f 2`

if [ ! "$lvTempVersion" = "$verDB" ]  || [ "$varForceInstal" = true ] ; then
	# création de la base de donnée
	source "$INSTALL_PATH/.instalModel/DBcreateTables.sh"

	[ "$varDebug" ] && echo "DB / table creation done" >> $DEBUG_FILE

	# insertion du user par défaut dans la DB
	source "$INSTALL_PATH/.instalModel/DBinsertAdmin.sh"

	[ "$varDebug" ] && echo "DB / admin user insert done" >> $DEBUG_FILE

	updateParameter "$INSTALL_PATH/.config/versions.sh" "verDB" "$lvTempVersion"
fi

#--------------------------------------------------------------------
# initialisation des tables et config initiale
if [ "$varFirstInstal" = "true" ] ; then
	# remplissage des tables
	# TODO existing record protection

	printMessage "remplissage des tables DB" "nichoir.db"
	doInsertRecord $(ls "$INSTALL_PATH"/.input/DBinsert_*)

	[ "$varDebug" ] && echo "DB / tables insert done" >> $DEBUG_FILE

	source "$INSTALL_PATH/.instalModel/CONFIGinit.sh"

	[ "$varDebug" ] && echo "Initial config done" >> $DEBUG_FILE

fi

#--------------------------------------------------------------------
# config de motion
# XXX Check after this line
source "$INSTALL_PATH/.instalModel/CONFIGmotion.sh"

[ "$varDebug" ] && echo "Motion config done" >> $DEBUG_FILE

# nettoyage des fichiers résiduels
printMessage "nettoyage des fichiers résiduels" "rm -r eBirds"
rm -r eBirds || printError "$?"

[ "$varDebug" ] && echo "File clean up done" >> $DEBUG_FILE

[ "$varDebug" ] && echo "\$varUpgrade = $varUpgrade" >> $DEBUG_FILE
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
