#!/bin/bash
# coding:UTF-8

# date : 21/11/2018
# créateur : bibi
# license : dwyw (do what you want)

#
# Le script doit être copié dans le répertoire /home/pi (ou votre nom de user si vous l'avez modifié).
# S'assurer que le script est bien exécutable
# Lancer le script en sudo : $ sudo ./instalNichoir.sh avec les options éventuelles.
# Pour plus d'informations, lancer ./instalNichoir.sh avec l'option -h ou -?
#

#######################################################################
# initialisation des variables
#######################################################################
# booléen options
varUpgrade=false
varVerbose=false
varReset=false
varError=false
varLocal=false
varGit=false

# messages à afficher ou imprimer dans le log
varMessage=""

#compteur d'erreur
(( varErrorCount = 0 ))

# noms par défauts
varLogFile="logInstalNichoir.log"
varSourceWeb="web.tar.xz"
#varSourcePrg="prg.tar.gz"


#######################################################################
# déclaration des fonctions
#######################################################################

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
	echo -e "\n  -g    GitHub - charge le nichoir depuis le repository sur GitHub"
	echo -e "\n  -h    help - affichage de l'aide"
	echo -e "\n  -l    local - installe et configure le nichoir sur base du fichier $varSourceWeb dans le répertoire courant"
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

function installProgram()
{
# installation de programme avec l'utilitaire apt-get
#----------------------------------------------------
# $1 texte à afficher
# $2 programme à installer

	# fonction gérant la sortie verbeuse
	printMessage "$1" "$2"

	# installation avec option 'assume-yes' (oui à toutes les questions)
	# -q -o=Dpkg::Use-Pty=0 - réduit le nombre d'affichages (mode quiet)
	# et écriture de la sortie dans le fichier log (mode ajout)
	sudo apt-get -q -o=Dpkg::Use-Pty=0 --assume-yes install $2 >> $varLogFile 2>&1

	# gestion des erreurs éventuelles
	printError "$?"
}

#######################################################################
# corps du programme
#######################################################################

# analyse des paramètres
#-----------------------
# test de la présence de paramètres (nombre de paramètres supérieur à 0)
if [ $# -gt 0 ] ; then

	# boucle parcourant les paramètres fournis
	while [ 1 -le $# ] ; do

		# test de la valeur de la variable - regex testant une chaine commançant par - et contenant
		# zero ou une occurance de chacune des lettres autorisées
		if [[ "$1" =~ ^[-]([eglruv]+)$ ]]  ; then

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
			exit 1
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
if [ "$varReset" = true ] ; then
	printMessage "Réinitialisation du fichier log" "$varLogFile"
	sudo rm $varLogFile > /dev/null 2>&1
	printError "$?"
fi

# vérification de la présence des fichiers sources en local ou sur le serveur
#----------------------------------------------------------------------------
if [ ! "$varGit" == "true" ] ; then
	if [ ! "$varLocal" == "true" ] ; then
		printMessage "téléchargement des fichiers sources depuis le serveur" "$varSourceWeb"
		wget --no-verbose --content-disposition -N "https://www.ebirds.be/data/getNichoir" >> $varLogFile 2>&1
		printError "$?"
	fi
	if [ ! -e "$varSourceWeb" ] ; then
		varMessage="Fichier source manquant ou erreur lors du téléchargement. L'installation du nichoir est interrompue"
		printError "$?"
		exit 2
	fi
fi


#######################################################################
# installation et configuration du système
#######################################################################
# Mise à jour du système
#-------------------------------------
printMessage "Mise à jour du système linux" "update"
sudo apt-get --quiet --assume-yes update >> $varLogFile 2>&1
printError "$?"

# Installation des programmes
#-------------------------------------
installProgram "installation du serveur web" "lighttpd"
installProgram "installation du gestionnaire de base de données" "sqlite3"
installProgram "installation" "git"
#installProgram "installation" "build-essential" -- déjà inclus dans l'installation de base
installProgram "installation de bibliothèque python" "python-pip"
installProgram "installation de bibliothèque python" "python-numpy"
installProgram "installation de PHP" "php-cgi"
#installProgram "installation de PHP" "php-common" -- déjà inclus dans php-cgi
installProgram "installation de PHP" "php-sqlite3"
installProgram "installation de PHP" "php-json"
installProgram "installation de PHP" "php7.0"
installProgram "installation du gestionnaire de flux video" "ffmpeg"
installProgram "installation de dnsutils pour transmission IP" "dnsutils"
installProgram "installation de dépendances de motion" "libmariadbclient18"
installProgram "installation de dépendances de motion" "libpq5"
#installProgram "installation de dépendances de motion" "mysql-common" -- déjà inclus dans libmariadbclient18
installProgram "installation de l'utilitaire de décompression" "xz-utils"

# installation de motion
#-----------------------
printMessage "téléchargement du serveur video" "motion"
wget --no-verbose -N "github.com/Motion-Project/motion/releases/download/release-4.0.1/pi_stretch_motion_4.0.1-1_armhf.deb" >> $varLogFile 2>&1
printError "$?"

printMessage "installation du serveur video" "motion"
sudo dpkg -i "pi_stretch_motion_4.0.1-1_armhf.deb" >> $varLogFile 2>&1
printError "$?"

# vérification des inter-dépendances
#-----------------------------------
printMessage "vérification des dépendances" "tous paquets"
sudo apt-get install --fix-missing >> $varLogFile 2>&1
printError "$?"


#######################################################################
# installation et configuration des capteurs
#######################################################################

# installation des bibliothèques de capteurs : HX711
#---------------------------------------------------
printMessage "import de bibliothèque de capteur" "HX711"
git clone --quiet https://github.com/tatobari/hx711py >> $varLogFile 2>&1
printError "$?"


# installation des bibliothèques de capteurs : DHT11
#---------------------------------------------------
printMessage "import de bibliothèques de capteur" "DHT11"
git clone --quiet https://github.com/adafruit/Adafruit_Python_DHT >> $varLogFile 2>&1
printError "$?"

# configuration de PYTHON
#------------------------
# Bibliothèque DHT11
printMessage "modification du répertoire actif" "DHT11"
cd Adafruit_Python_DHT
(( locError=$? ))
printError "$locError"

printMessage "configuration des bibliothèques python" "DHT11"
if [ ! $locError -gt 0 ] ; then
	sudo python setup.py install >> $varLogFile 2>&1
	printError "$?"
	cd ..
fi

# Bibliothèque HX711
printMessage "modification du répertoire actif" "HX711"
cd hx711py
(( locError=$? ))
printError "$locError"

printMessage "configuration des bibliothèques python" "HXT11"
if [ ! $locError -gt 0 ] ; then
	sudo python setup.py install >> $varLogFile 2>&1
	printError "$?"
	cd ..
fi

#######################################################################
# installation et configuration des programmes
#######################################################################

# Activation du service fastcgi
#-----------------------------
printMessage "activation du service fast-cgi" "PHP"
sudo lighty-enable-mod fastcgi-php >> $varLogFile 2>&1
(( locError=$? ))
printError "$locError"
if [ ! $locError -gt 0 ] ; then
	sudo service lighttpd force-reload
	printError "$?"
fi

# Paramétrage de PHP - cgi.fix_pathinfo
#-------------------------------------
printMessage "paramétrage" "php-cgi"
sudo sed "/etc/php/7.0/cli/php.ini" -i -e "s/^;cgi\.fix_pathinfo=1/cgi\.fix_pathinfo=1/g"

# activation de la caméra
#------------------------
# teste si les lignes de configuration existent déjà
printMessage "activation de la caméra" "/boot/config.txt"
if sudo grep -e "^start_x=1$" /boot/config.txt ; then
	printMessage "caméra déjà activée" "/boot/config.txt"
else
	sudo echo -e "\n# activation de la caméra" >> /boot/config.txt
	sudo echo "start_x=1" >> /boot/config.txt
	# allocation de mémoire
	sudo echo "gpu_mem=128" >> /boot/config.txt
	# désactiver la led de la caméra
	sudo echo "disable_camera_led=1" >> /boot/config.txt
fi

# configuration de lighttpd
#--------------------------
# teste si la ligne de configuration existe déjà
printMessage "paramétrage" "lighttpd"
if sudo grep -e "^	\"mod_fastcgi\",$" /etc/lighttpd/lighttpd.conf ; then
	printMessage "mod_fastcgi déjà autorisé" "lighttpd"
else
	# renomme le fichier de configuration sous lighttpd.conf.bak
	sudo mv /etc/lighttpd/lighttpd.conf /etc/lighttpd/lighttpd.conf.bak
	# crée le nouveau fichier de configuration en ajoutant la ligne requise
	sudo awk '/server.modules/ { print; print "	\"mod_fastcgi\","; next }1' /etc/lighttpd/lighttpd.conf.bak > /etc/lighttpd/lighttpd.conf
fi

# configuration de motion
#------------------------
printMessage "paramétrage" "motion"
# mode démon par défaut quand motion est lancé dans la console
sudo sed "/etc/motion/motion.conf" -i -e "s/^\(#\|;\)\? \?daemon \(on\|off\)/daemon on/g"

# fichier PID déplacé pour permission d'écriture
sudo sed "/etc/motion/motion.conf" -i -e "s:^\(#\|;\)\? \?process_id_file *\(on\|off\):process_id_file /home/pi/.motion/motion.pid:g"

# dimensions de l'image en pixel
sudo sed "/etc/motion/motion.conf" -i -e "s/^\(#\|;\)\? \?width [0-9]*/width 640/g" -e "s/^\(#\|;\)\? \?height [0-9]*/height 480/g"

# nom de la camera
sudo sed "/etc/motion/motion.conf" -i -e "s/^\(#\|;\)\? \?mmalcam_name .*$/mmalcam_name vc.ril.camera/g"

# durée maximale des films en secondes
sudo sed "/etc/motion/motion.conf" -i -e "s/^\(#\|;\)\? \?max_movie_time [0-9]*$/max_movie_time 100/g"

# enregistrements de films mis off
sudo sed "/etc/motion/motion.conf" -i -e "s/^\(#\|;\)\? \?ffmpeg_output_movies on$/ffmpeg_output_movies off/g"

# target directory
sudo sed "/etc/motion/motion.conf" -i -e "s:^\(#\|;\)\? \?target_dir .*$:target_dir /var/www/html/public/cameraShots:g"

# stream port
sudo sed "/etc/motion/motion.conf" -i -e "s/^\(#\|;\)\? \?stream_port [0-9]*$/stream_port 9081/g"

# stream only for local host
sudo sed "/etc/motion/motion.conf" -i -e "s/^\(#\|;\)\? \?stream_localhost on$/stream_localhost off/g"

# Output frames at 1 fps when no motion is detected and increase to stream_maxrate when motion is detected
sudo sed "/etc/motion/motion.conf" -i -e "s/^\(#\|;\)\? \?stream_motion off$/stream_motion on/g"

# Maximum framerate for stream streams (default: 1)
sudo sed "/etc/motion/motion.conf" -i -e "s/^\(#\|;\)\? \?stream_maxrate .*$/stream_maxrate 12/g"

# Script to launch on motion detection, commented by default
sudo sed "/etc/motion/motion.conf" -i -e "s:^\(#\|;\)\? \?on_motion_detected .*$:; on_motion_detected /var/www/html/public/bash/motionSendMail.sh:g";

# configuration du démon
sudo sed "/etc/default/motion" -i -e "s/^start_motion_daemon=no/start_motion_daemon=yes/g"

printMessage "activation de motion" "motion"
sudo systemctl enable motion
printError "$?"

#######################################################################
# gestion des fichiers sources et copie dans les bons répertoires
#######################################################################
printMessage "Nettoyage du répertoire html" "rm -r /var/www/html/*"
sudo rm -r /var/www/html/* >> $varLogFile 2>&1
printError "$?"

if [ "$varGit" == "true" ] ; then
	printMessage "téléchargement des fichiers sources depuis GitHub" "https://github.com/SuperCreate73/eBirds.git"
	git clone --quiet https://github.com/SuperCreate73/eBirds.git >> $varLogFile 2>&1
	printError "$?"

	printMessage "déplacement des fichiers web" "/var/www/html"
	sudo mv --force eBirds/html_working/* /var/www/html/ >> $varLogFile 2>&1
	printError "$?"

	printMessage "déplacement des scripts python" "/var/www/backend"
	sudo mv --force eBirds/backend /var/www/ >> $varLogFile 2>&1
	printError "$?"

	printMessage "mise en place du daemon - python" "/usr/bin/ebirdsDaemon"
	sudo cp /var/www/backend/ebirdsv2.py /usr/bin/ebirdsDaemon >> $varLogFile 2>&1
	printError "$?"

	printMessage "mise en place du daemon - bash" "/etc/init.d/ebirdsDaemon"
	sudo cp /var/www/backend/ebirdsDaemon /etc/init.d/ebirdsDaemon >> $varLogFile 2>&1
	printError "$?"

	printMessage "mise en place du daemon - permissions" "/etc/init.d/ebirdsDaemon"
	sudo chmod +x /etc/init.d/ebirdsDaemon >> $varLogFile 2>&1
	printError "$?" >> $varLogFile 2>&1

	printMessage "mise en place du daemon - permissions" "/usr/bin/ebirdsDaemon"
	sudo chmod +x /usr/bin/ebirdsDaemon >> $varLogFile 2>&1
	printError "$?" >> $varLogFile 2>&1

	printMessage "mise en place du daemon - activation" "update-rc.d"
	update-rc.d ebirdsDaemon defaults
	printError "$?" >> $varLogFile 2>&1

	printMessage "nettoyage des fichiers résiduels" "rm -r eBirds"
	sudo rm -r eBirds
	printError "$?"
else
	printMessage "décompression des fichiers sources" "nichoir"
	tar -xJf $varSourceWeb >> $varLogFile 2>&1
	printError "$?"

	printMessage "déplacement des fichiers web" "/var/www/html"
	sudo mv --force web/html/ /var/www/ >> $varLogFile 2>&1
	printError "$?"

	printMessage "nettoyage des fichiers résiduels" "rm -r eBirds"
	sudo rm -r web
	printError "$?"
fi

#######################################################################
# création de la basede donnée vide
#######################################################################
printMessage "creation de la base de données" "nichoir.db"
sqlite3 /var/www/nichoir.db << EOS
	CREATE TABLE IF NOT EXISTS users (login TINY TEXT PRIMARY KEY, password TEXT);
	CREATE TABLE IF NOT EXISTS Capt_IR (FDatim DATETIME DEFAULT CURRENT_TIMESTAMP, FConnector TEXT, FStatus TEXT, FTime LONG, FTreated INTEGER DEFAULT 0, FID_Pair LONG);
	CREATE TABLE IF NOT EXISTS meteo (dateHeure DATETIME DEFAULT CURRENT_TIMESTAMP, tempExt TEXT, humExt TEXT, tempInt TEXT, humInt TEXT);
	CREATE TABLE IF NOT EXISTS InOut_IR (FDatim DATETIME DEFAULT CURRENT_TIMESTAMP, FStatus TEXT, FTime LONG);
	CREATE TABLE IF NOT EXISTS Capt_cap (dateHeure DATETIME DEFAULT CURRENT_TIMESTAMP, connecteur TEXT, valeur LONG);
	CREATE TABLE IF NOT EXISTS config (setting TINY TEXT PRIMARY KEY, value TINY TEXT, defautValue TINY TEXT, valueType);
	CREATE TABLE IF NOT EXISTS configRange (setting TINY TEXT, rangeValue TINY TEXT);
	CREATE TABLE IF NOT EXISTS configAlias (alias TINY TEXT, aliasValue TINY TEXT, setting TINY TEXT, settingValue TINY TEXT);
EOS
printError "$?"
#######################################################################
# crée un utilisateur par défaut - admin - dans la base de donnée
#######################################################################
printMessage "insertion de l'utilisateur admin (password = admin)" "nichoir.db"
adminPwd=$(printf '%s' "admin" | md5sum | cut -d ' ' -f 1)
sqlite3 /var/www/nichoir.db << EOS
	INSERT INTO users ('login', 'password') VALUES ('admin', '$adminPwd');
EOS
printError "$?"

#######################################################################
# crée les settings dans la base de données
#######################################################################
printMessage "insertion des paramètres" "nichoir.db"
sqlite3 /var/www/nichoir.db << EOS
	INSERT INTO config ('setting', 'value', 'defautValue', 'valueType') VALUES ('on_motion_detected', 'email', 'comment', 'email');
	INSERT INTO config ('setting', 'value', 'defautValue', 'valueType') VALUES ('width', '640', '640', 'discreet');
	INSERT INTO config ('setting', 'value', 'defautValue', 'valueType') VALUES ('height', '480', '480', 'discreet');
	INSERT INTO config ('setting', 'value', 'defautValue', 'valueType') VALUES ('threshold', '10', '10', 'range');
	INSERT INTO config ('setting', 'value', 'defautValue', 'valueType') VALUES ('quality', '75', '75', 'range');
	INSERT INTO config ('setting', 'value', 'defautValue', 'valueType') VALUES ('ffmpeg_timelapse', '0', '0', 'range');
	INSERT INTO config ('setting', 'value', 'defautValue', 'valueType') VALUES ('ffmpeg_timelapse_mode', 'daily', 'daily', 'discreet');
	INSERT INTO configRange ('setting', 'rangeValue') VALUES ('width', '480');
	INSERT INTO configRange ('setting', 'rangeValue') VALUES ('width', '640');
	INSERT INTO configRange ('setting', 'rangeValue') VALUES ('width', '1280');
	INSERT INTO configRange ('setting', 'rangeValue') VALUES ('height', '360');
	INSERT INTO configRange ('setting', 'rangeValue') VALUES ('height', '480');
	INSERT INTO configRange ('setting', 'rangeValue') VALUES ('height', '960');
	INSERT INTO configRange ('setting', 'rangeValue') VALUES ('threshold', '5');
	INSERT INTO configRange ('setting', 'rangeValue') VALUES ('threshold', '50');
	INSERT INTO configRange ('setting', 'rangeValue') VALUES ('quality', '0');
	INSERT INTO configRange ('setting', 'rangeValue') VALUES ('quality', '100');
	INSERT INTO configRange ('setting', 'rangeValue') VALUES ('ffmpeg_timelapse', '0');
	INSERT INTO configRange ('setting', 'rangeValue') VALUES ('ffmpeg_timelapse', '3200');
	INSERT INTO configRange ('setting', 'rangeValue') VALUES ('ffmpeg_timelapse_mode', 'hourly');
	INSERT INTO configRange ('setting', 'rangeValue') VALUES ('ffmpeg_timelapse_mode', 'daily');
	INSERT INTO configRange ('setting', 'rangeValue') VALUES ('ffmpeg_timelapse_mode', 'weekly-sunday');
	INSERT INTO configRange ('setting', 'rangeValue') VALUES ('ffmpeg_timelapse_mode', 'weekly-monday');
	INSERT INTO configRange ('setting', 'rangeValue') VALUES ('ffmpeg_timelapse_mode', 'monthly');
	INSERT INTO configAlias ('alias', 'aliasValue', 'setting', 'settingValue') VALUES ('imageSize', 'low', 'width', '480') ;
	INSERT INTO configAlias ('alias', 'aliasValue', 'setting', 'settingValue') VALUES ('imageSize', 'low', 'height', '360') ;
	INSERT INTO configAlias ('alias', 'aliasValue', 'setting', 'settingValue') VALUES ('imageSize', 'medium', 'width', '640') ;
	INSERT INTO configAlias ('alias', 'aliasValue', 'setting', 'settingValue') VALUES ('imageSize', 'medium', 'height', '480') ;
	INSERT INTO configAlias ('alias', 'aliasValue', 'setting', 'settingValue') VALUES ('imageSize', 'high', 'width', '1280') ;
	INSERT INTO configAlias ('alias', 'aliasValue', 'setting', 'settingValue') VALUES ('imageSize', 'high', 'height', '960') ;
EOS
printError "$?"

########################################################################
printMessage "gestion des permissions des répertoires" ".motion & /var/www"
sudo addgroup w3
printError "$?"

sudo adduser pi w3
printError "$?"

sudo adduser motion w3
printError "$?"

sudo adduser www-data w3
printError "$?"

# TODO adduser to SudoUser : adduser www-data sudo
# alternative : www-data ALL=(ALL) NOPASSWD:ALL
# à ajouter dans /etc/sudoers à la dernière ligne
sudo echo "www-data ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

sudo chgrp -R w3 /var/www/
printError "$?"

sudo chmod -R 774 /var/www/
printError "$?"

sudo mkdir /home/pi/.motion
printError "$?"

sudo chgrp -R w3 /home/pi/.motion/
printError "$?"

sudo chmod -R 770 /home/pi/.motion/
printError "$?"

#######################################################################
# configuration de l'envoi de l'adresse IP au serveur
#######################################################################
printMessage "envoi de l'adresse IP au serveur central" "curlIP"
MACaddress=$(sudo ifconfig | grep -i -m 1  ether | cut -f 10 -d ' ')
IPlocale=$(sudo ifconfig | grep -i -m 1  "netmask 255.255.255.0" | cut -f 10 -d ' ')
IPexterne=$(dig TXT +short -4 o-o.myaddr.1.google @ns1.google.com | cut -f 2 -d '"')
Name=$(hostname)

curl --data "ID=$MACaddress&IPEXT=$IPexterne&IPINT=$IPlocale&NAME=$Name" https://ebirds.be/donnees/identify
printError "$?"

printMessage "config envoi de l'IP au serveur central" "crontab"
# crontab -u pi - <<FIN
# 00 */06 * * * /var/www/html/public/bash/sendIP.sh
# @reboot /var/www/html/public/bash/sendIP.sh
# FIN
#
# crontab < <(crontab -l ; echo "0 */6 * * * /var/www/html/public/bash/sendIP.sh > /dev/null 2>&1")

sudo touch /etc/cron.d/sendIP
sudo chmod 777 /etc/cron.d/sendIP
sudo echo "00 */06 * * * pi /var/www/html/public/bash/sendIP.sh" >> /etc/cron.d/sendIP
sudo echo "@reboot pi /var/www/html/public/bash/sendIP.sh" >> /etc/cron.d/sendIP

printError "$?"

#######################################################################
# upgrade du système linux
#######################################################################
if [ "$varUpgrade" == true ] ; then
	printMessage "Mise à jour du système linux" "upgrade"
	sudo apt-get --quiet --assume-yes upgrade  >> $varLogFile 2>&1
	printError "$?"

	printMessage "Mise à jour du système linux" "dist-upgrade"
	sudo apt-get --quiet --assume-yes dist-upgrade >> $varLogFile 2>&1
	printError "$?"
fi

# TODO - configurer CRONTAB pour envoyer l'adresse IP
#		 possible avec la commande curl en bash (IP en post ou en get)
# 		 log de la réponse ??
#	   - script à créer sur le serveur pour stocker l'adresse IP
#	   - ID unique à créer pour le nichoir
#
# TODO - validation finale

#

#######################################################################
# sortie du script
#######################################################################
if [ $varErrorCount -gt 0 ] ; then
	echo -e "\n\nNombre d'erreurs rencontrées : $varErrorCount - Consultez le fichier $logFile pour plus d'informations"
	echo -e "\n\nNombre d'erreurs rencontrées : $varErrorCount" >> $varLogFile
	exit 1
else
	echo -e "\n\nL'installation du nichoir est maintenant terminée - aucune erreur rencontrée"
	echo -e "\n\nLe redémarrage du nichoir est vivement conseillé !"
	echo -e "\n\nL'installation du nichoir est maintenant terminée - aucune erreur rencontrée" >> $varLogFile
	exit 0
fi
