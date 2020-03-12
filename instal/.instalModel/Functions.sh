#!/bin/bash
# coding:UTF-8

function usage()
{
# affichage de l'aide
#--------------------
#
	echo -e "\nUsage: $0 [OPTION] "
	echo -e "\nInstalle et configure la version standart du nichoir dans le dossier courant (version 2.0)"
	echo -e "Les fichiers nécessaires sont automatiquement téléchargés du serveur ebirds (sauf si l'option 'local' est activée)"
	echo -e "\nLe script doit être copié dans le répertoire /home/pi (ou votre nom de user si vous l'avez modifié)."
	echo -e "S'assurer que le script est bien exécutable."
	echo -e "Lancer le script en sudo : $ sudo ./instalNichoir.sh avec les options éventuelles."
	echo -e "\n\nOptions:"
	echo -e "\n  -e    error - affichage des erreurs dans la console (aussi affichées en mode 'verbose')"
	echo -e "\n  -g    GitHub - charge le nichoir depuis le repository sur GitHub - comportement par défaut"
	echo -e "\n  -h    help - affichage de l'aide"
	echo -e "\n  -l    local - installe et configure le nichoir sur base des fichiers prsents dans le répertoire .input"
	echo -e "\n  -m    mise à jour - télécharge et installe les dernières mises à jours"
	echo -e "        Comportement par défaut si le nichoir est déjà installé localement (version actuelle disponible)"
	echo -e "\n  -r    reset - réinitialisation du fichier log"
	echo -e "\n  -u    upgrade - upgrade du sytème Linux après installation du nichoir"
	echo -e "\n  -v    verbose - affichage des opérations effectuées"
	echo -e "\n\nExemple d'utilisation :"
	echo -e "	$0 -uv    installation du nichoir en mode verbeux avec upgrade du système\n"
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
	if [ "$varVerbose" = true ] ; then  echo "$varMessage" ; fi

	# préparation de la chaine de séparation '-----------'
	str=""
	(( count = 0 ))

	while [ $count -lt ${#varMessage} ] ; do
		str="$str-"
		(( count += 1 ))
	done

	# écriture dans le fichier log
	echo -e "\n$varMessage \n$str " >> $varLogFile
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
		echo "    Error on execution - $varMessage - Error Code $1" >> $varLogFile
		# affichage en console si option activée
		if [ "$varError" = true ] ; then  echo "    Error on execution - $varMessage - Error Code $1" ; fi
	fi
}
