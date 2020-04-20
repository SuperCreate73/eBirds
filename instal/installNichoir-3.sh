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
# Pour plus d'informations, lancer nichoir avec l'option -h, --help ou ?
#

# initialisation
#######################################################################
# répertoires par défauts
varInstalPath='/usr/local/etc/instal'
varLogFile="$varInstalPath/logInstal.log"

# déclaration des fonctions de base du script
source "$varInstalPath/.instalModel/Functions.sh"

# vérifie si le paramètre fourni est l'affichage de l'aide
if [ $1 == "-h" ] || [ $1 == "--help" ] || [ $1 == "?" ] || [ $1 == "-?" ] ; then
	usage
	exit 0
fi

# vérifie si l'utilisateur est superUser
if [[ $EUID -ne 0 ]] ; then
	echo -e "Ce script doit être exécuté avec les droits de SuperUser." 1>&2
	exit 1
fi

# lecture du fichier local des versions installées
source "$varInstalPath/.config/versions.sh"

# initialisation des variables
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

# test si première installation
if [ -e /var/www/nichoir.db ] ; then
	varFirstInstal=false
else
	varFirstInstal=true
fi

# messages à afficher ou imprimer dans le log
varMessage=""

#compteur d'erreur
(( varErrorCount = 0 ))

# répertoires par défauts
varInstalPath='/usr/local/etc/instal'
varLogFile="$varInstalPath/logInstal.log"

#######################################################################
# TODO prévoir un mode update :
#			IF [fichier .info existe ] ; THEN
#				lecture du fichier
#				IF [version courante > version installée] ; THEN
#					update
#				else
#					instal
#				fi
#			else
#				instal
#			fi

# TODO Force install -> efface et remplace les fichiers actuels
# TODO Repair -> réinstalle sans toucher aux fichiers data
# TODO Update -> voir ci-dessus - mode par défaut si fichier .info existe
# TODO New install - mode par défaut si fichier .info n'existe pas (et DB ?)

# TODO créer le fichier .info

# TODO lire un fichier avec la configuration d'installation :
# 			Quels capteurs ?
# 			Quels types ?
# 			Fonction 'read' à utiliser (propre à chaque capteur)
# 			Paramètres et format à donner à la fonction 'read' (pin, ?)

# TODO modifier la BD pour faire une seule table capteur
# 			table de correspondance ID - label, unité (degré, %, ?), fonction read et paramètres

#######################################################################
# déclaration des fonctions
#######################################################################
source "$varInstalPath/.instalModel/Functions.sh"

#######################################################################
# Initialisation de l'installation
#######################################################################

# analyse des paramètres
#-----------------------
# test de la présence de paramètres (nombre de paramètres supérieur à 0)
if [ "$#" -gt 0 ] ; then

	varAllParams="$@"
	# boucle parcourant les paramètres fournis
	while [ 1 -le "$#" ] ; do

		if [ "${1:0:2}" = "--" ] ; then
			case "${1:2}" in
				"first")
					varFirstInstal=true
					;;
				"recall")
					varRecall=true
					;;
			esac

		# test de la valeur de la variable - regex testant une chaine commançant par - et contenant
		# zero ou une occurance de chacune des lettres autorisées
	elif [[ "$1" =~ ^[-]([eglmruvcsfi]+)$ ]]  ; then

			#	affecte le string des paramètres à 'variables' en enlevant le premier '-'
			variables=${1:1}

			#	parcours de la chaine de caractère des paramètres et affectation des
			#   variables d'état des paramètres + tests des combinaisons de paramètres
			for var_count in `seq 0 ${#variables}` ; do
				case ${variables:$var_count:1} in
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
			echo "Paramètre(s) non reconnu"
			echo "------------------------"
			usage
			exit 1
		fi

	#	passage au paramètre suivant
		shift
	done
fi

# efface le fichier de log si option r - reset - activée
#-------------------------------------------------------
# if [ "$varReset" = true ] && [ -e $varLogFile ] ; then
# 	printMessage "Réinitialisation du fichier log" "$varLogFile"
# 	rm $varLogFile > /dev/null 2>&1
# 	printError "$?"
# fi

# écriture de l'encodage du fichier log si pas encore existant
#-------------------------------------------------------
if [ ! -e "$varLogFile" ] || [ "$varReset" = true ] ; then
	printMessage "Création et paramétrage du fichier log" "$varLogFile"
	echo -e "# coding:UTF-8 \n\n" > $varLogFile || printError "$?"
fi

#######################################################################
# installation et configuration du système
#######################################################################
# Mise à jour de l'installateur
#-------------------------------------
if [ ! "$varRecall" = true ] ; then
	printMessage "Mise à jour du système linux" "update"
	apt-get --quiet --assume-yes update >> $varLogFile 2>&1 || printError "$?"

	if  ! dpkg -V "$program" > /dev/null 2>&1 ; then
		printMessage "Installation de GIT" "git"
		# installation avec option 'assume-yes' (oui à toutes les questions)
		# -q -o=Dpkg::Use-Pty=0 - réduit le nombre d'affichages (mode quiet)
		# et écriture de la sortie dans le fichier log (mode ajout)
		apt-get -q -o=Dpkg::Use-Pty=0 --assume-yes install git >> "$varLogFile" 2>&1 || printError "$?"
	fi

	# téléchargement et copie des fichiers eBirds -
	printMessage "téléchargement des fichiers sources depuis GitHub" "https://github.com/SuperCreate73/eBirds.git"
	git clone --quiet "https://github.com/SuperCreate73/eBirds.git" >> $varLogFile 2>&1 || printError "$?"

	# check de la version de l'installateur
	lvTempVersion=`grep "verInstal" "eBirds/instal/.config/versions.sh" | cut -d '=' -f 2`

	if [ ! "$lvTempVersion" = "$verInstal" ] || [ "$varLoadInstal" = true ] ; then

		[ -d /usr/local/etc/instal ] || mkdir /usr/local/etc/instal

		# copie des nouveaux fichiers et relance de l'installateur
		printMessage "copie de l'installateur" "installNichoir-3.sh"
		cp --force eBirds/instal/installNichoir-3.sh /usr/local/etc/instal/ || printError "$?"

		printMessage "copie des dépendances de l'installateur" ".instalModel/"
		cp -r --force eBirds/instal/.instalModel /usr/local/etc/instal/ || printError "$?"

		printMessage "copie des fichiers input" ".input/"
		cp -r --force eBirds/instal/.input /usr/local/etc/instal/ || printError "$?"

		printMessage "copie des fichiers motion" "motion/"
		cp -r --force eBirds/instal/motion /usr/local/etc/instal/ || printError "$?"

		if [ "$varFirstInstal" = true ] ; then
			printMessage "copie des fichiers de config" ".config/"
			[ -d /usr/local/etc/instal/.config ] || mkdir /usr/local/etc/instal/.config
			cp --force eBirds/instal/.config/versions_init.sh /usr/local/etc/instal/.config/versions.sh || printError "$?"
		fi
		# create link to installNichoir-3.sh -> new bash command
		printMessage "création du lien symbolique" "/usr/local/bin/nichoir"
		rm /usr/local/bin/nichoir > /dev/null 2>&1
		ln -s -f /usr/local/etc/instal/installNichoir-3.sh /usr/local/bin/nichoir || printError "$?"

		# permission to execute to all .sh files
		printMessage "gestion des permissions des fichiers d'instal" "chmod 755"
		cd /usr/local/etc/instal/
		chmod 755 `find -mindepth 0 -name "*.sh"` || printError "$?"
		cd -

		# update config file with instal version
		updateParameter "$varInstalPath/.config/versions.sh" "verInstal" "$lvTempVersion"

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
	source "$varInstalPath/.instalModel/PRGinstal.sh"
	updateParameter "$varInstalPath/.config/versions.sh" "verPrgInstal" "$lvTempVersion"
fi

# installation des capteurs / bibliothèques python - contrôle dans pip3 si déjà existant
lvTempVersion=`grep "verPythonLib" "eBirds/instal/.config/versions.sh" | cut -d '=' -f 2`

if [ ! "$lvTempVersion" = "$verPythonLib" ]  || [ "$varCheckBib" = true ] ; then
	source "$varInstalPath/.instalModel/PYTHONinstal.sh"
	updateParameter "$varInstalPath/.config/versions.sh" "verPythonLib" "$lvTempVersion"
fi

# téléchargement et copie des fichiers eBirds -
lvTempVersion=`grep "verNichoirFiles" "eBirds/instal/.config/versions.sh" | cut -d '=' -f 2`

if [ ! "$lvTempVersion" = "$verNichoirFiles" ]  || [ "$varForceInstal" = true ] ; then
	source "$varInstalPath/.instalModel/FILESinstal.sh"
	updateParameter "$varInstalPath/.config/versions.sh" "verNichoirFiles" "$lvTempVersion"
fi

lvTempVersion=`grep "verDB" "eBirds/instal/.config/versions.sh" | cut -d '=' -f 2`
if [ ! "$lvTempVersion" = "$verDB" ]  || [ "$varForceInstal" = true ] ; then
	# création de la base de donnée
	source "$varInstalPath/.instalModel/DBcreateTables.sh"

	# insertion du user par défaut dans la DB
	source "$varInstalPath/.instalModel/DBinsertAdmin.sh"

	updateParameter "$varInstalPath/.config/versions.sh" "verDB" "$lvTempVersion"
fi

# config de motion
source "$varInstalPath/.instalModel/CONFIGmotion.sh"

if [ "$varFirstInstal" = "true" ] ; then
	# remplissage des tables
	source "$varInstalPath/.instalModel/DBinsertRecord.sh"

	source "$varInstalPath/.instalModel/CONFIGinit.sh"
fi

# nettoyage des fichiers résiduels
printMessage "nettoyage des fichiers résiduels" "rm -r eBirds"
rm -r eBirds || printError "$?"

#######################################################################
# upgrade du système linux
#######################################################################
if [ "$varUpgrade" == true ] ; then
	printMessage "Mise à jour du système linux" "upgrade"
	apt-get --quiet --assume-yes upgrade  >> $varLogFile 2>&1 || printError "$?"

	printMessage "Mise à jour du système linux" "dist-upgrade"
	apt-get --quiet --assume-yes dist-upgrade >> $varLogFile 2>&1 || printError "$?"
fi

#######################################################################
# sortie du script
#######################################################################
if [ "$varErrorCount" -gt 0 ] ; then
	echo -e "\n\nNombre d'erreurs : $varErrorCount - Consultez le fichier $logFile pour plus d'informations"
	echo -e "\n\nNombre d'erreurs : $varErrorCount" >> $varLogFile
	exit 1
elif [ "$varFirstInstal" = "true" ] ; then
	echo -e "\n\nL'installation du nichoir est maintenant terminée (aucune erreur)"
	echo -e "\n\nLe redémarrage du nichoir est vivement conseillé !"
	echo -e "\n\nL'installation du nichoir est maintenant terminée - aucune erreur" >> $varLogFile
	exit 0
else
	echo -e "\n\nLa mise à jour du nichoir est terminée - aucune erreur"
	echo -e "\n\n"
	echo -e "\n\nLa mise à jour du nichoir est terminée - aucune erreur" >> $varLogFile
	exit 0
fi
