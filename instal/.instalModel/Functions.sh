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
		echo "    ERROR - $varMessage - Error Code $1" >> $varLogFile
		# affichage en console si option activée
		if [ "$varError" = true ] ; then  echo "    Error on execution - $varMessage - Error Code $1" ; fi
	fi
}

function updateParameter() {
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
}
