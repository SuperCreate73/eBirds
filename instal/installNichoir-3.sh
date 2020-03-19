#!/bin/bash
# coding:UTF-8

# date : 27/05/2019
# créateur : bibi
# license : dwyw (do what you want)

#
# Le script doit être copié dans le répertoire /home/pi (ou votre nom de user si
# vous l'avez modifié).
# S'assurer que le script est bien exécutable (sudo chmod 766 installnichoir.sh)
# Lancer le script en sudo : $ sudo ./installNichoir.sh avec les options
# éventuelles.
# Pour plus d'informations, lancer ./installNichoir.sh avec l'option -h ou -?
#
#######################################################################
# vérifie que l'utilisateur est superUser
#######################################################################
if [[ $EUID -ne 0 ]] ; then
	echo -e "Ce script doit être exécuté avec les droits de SuperUser." 1>&2
	exit 1
fi
#######################################################################
# initialisation des variables
#######################################################################
# booléen options
varUpgrade=false
varVerbose=false
varReset=false
varError=false
varLocal=false
varGit=true
varServer=false
varUpdate=false

if [ -e /var/www/nichoir.db ] ; then
	varFirstInstal=false
else
	varFirstInstal=true
fi

# messages à afficher ou imprimer dans le log
varMessage=""

#compteur d'erreur
(( varErrorCount = 0 ))

# noms par défauts
varInstalPath='/usr/local/etc/instal'
varLogFile="$varInstalPath/logInstal.log"
#varSourceWeb="web.tar.xz"

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

# TODO inclusion de code : soit '.' , soit 'source'

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
if [ $# -gt 0 ] ; then

	# boucle parcourant les paramètres fournis
	while [ 1 -le $# ] ; do

		# test de la valeur de la variable - regex testant une chaine commançant par - et contenant
		# zero ou une occurance de chacune des lettres autorisées
		if [[ "$1" =~ ^[-]([eglmruv]+)$ ]]  ; then

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
				esac
			done
		elif [ $1 == "-h" ] || [ $1 == "--help" ] || [ $1 == "?" ] ; then
			usage
			exit 0
	#	motif de paramètres non reconnu
		elif [ ${1:0:1} == "-" ] ; then
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
if [ "$varReset" = true ] && [ -e $varLogFile ]; then
	printMessage "Réinitialisation du fichier log" "$varLogFile"
	rm $varLogFile > /dev/null 2>&1
	printError "$?"
fi

# écriture de l'encodage du fichier log si pas encore existant
#-------------------------------------------------------
if [ ! -e "$varLogFile" ] ; then
	printMessage "Création et paramétrage du fichier log" "$varLogFile"
	echo -e "# coding:UTF-8 \n\n" >> $varLogFile
	printError "$?"
fi

#######################################################################
# installation et configuration du système
#######################################################################
# Mise à jour du système
#-------------------------------------
printMessage "Mise à jour du système linux" "update"
apt-get --quiet --assume-yes update >> $varLogFile 2>&1
printError "$?"

#######################################################################
#######################################################################

# installation programmes - contrôles internes si déjà existant
source "$varInstalPath/.instalModel/PRGinstal.sh"

# installation des capteurs / bibliothèques python - contrôle dans pip3 si déjà existant
source "$varInstalPath/.instalModel/PYTHONinstal.sh"

# téléchargement et copie des fichiers eBirds -
source "$varInstalPath/.instalModel/FILESinstal.sh"

# création de la base de donnée vide
source "$varInstalPath/.instalModel/DBcreateTables.sh"

# insertion du user par défaut dans la DB
source "$varInstalPath/.instalModel/DBinsertAdmin.sh"

if [ "$varFirstInstal" = "true" ] ; then
	# remplissage des tables
	source "$varInstalPath/.instalModel/DBinsertRecord.sh"

	source "$varInstalPath/.instalModel/CONFIGinit.sh"

	# TODO test pour voir si c'est nécessaire
	source "$varInstalPath/.instalModel/CONFIGmotion.sh"
fi

#######################################################################
# upgrade du système linux
#######################################################################
if [ "$varUpgrade" == true ] ; then
	printMessage "Mise à jour du système linux" "upgrade"
	apt-get --quiet --assume-yes upgrade  >> $varLogFile 2>&1
	printError "$?"

	printMessage "Mise à jour du système linux" "dist-upgrade"
	apt-get --quiet --assume-yes dist-upgrade >> $varLogFile 2>&1
	printError "$?"
fi

#######################################################################
# sortie du script
#######################################################################
if [ $varErrorCount -gt 0 ] ; then
	echo -e "\n\nNombre d'erreurs rencontrées : $varErrorCount - Consultez le fichier $logFile pour plus d'informations"
	echo -e "\n\nNombre d'erreurs rencontrées : $varErrorCount" >> $varLogFile
	exit 1
elif [ "$varFirstInstal" = "true" ] ; then
	echo -e "\n\nL'installation du nichoir est maintenant terminée - aucune erreur rencontrée"
	echo -e "\n\nLe redémarrage du nichoir est vivement conseillé !"
	echo -e "\n\nL'installation du nichoir est maintenant terminée - aucune erreur rencontrée" >> $varLogFile
	exit 0
else
	echo -e "\n\nLa mise à jour du nichoir est terminée - aucune erreur rencontrée"
	echo -e "\n\n"
	echo -e "\n\nLa mise à jour du nichoir est terminée - aucune erreur rencontrée" >> $varLogFile
	exit 0
fi
