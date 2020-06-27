#!/bin/bash
# coding:UTF-8
# crée le répertoire d'installation si n'existe pas (cfr constantes de config)
[ -d "$INSTALL_PATH" ] || mkdir "$INSTALL_PATH"

# copie des fichiers du programme d'installation
printMessage "copie de l'installateur" "$SCRIPT_FILE"
cp --force eBirds/instal/$SCRIPT_FILE "$INSTALL_PATH"/ || printError "$?"

printMessage "copie des dépendances de l'installateur" ".instalModel/"
cp -r --force eBirds/instal/.instalModel "$INSTALL_PATH" || printError "$?"

printMessage "copie des fichiers input" ".input/"
cp -r --force eBirds/instal/.input "$INSTALL_PATH" || printError "$?"

printMessage "copie des fichiers motion" "motion/"
cp -r --force eBirds/instal/motion "$INSTALL_PATH" || printError "$?"

if [ "$varCopyConfig" = true ] ; then
	printMessage "copie des fichiers de config" ".config/"
	# crée le répertoire si n'existe pas
	[ -d "$INSTALL_PATH/.config" ] || mkdir "$INSTALL_PATH/.config"
	cp --force eBirds/instal/.config/versions_init.sh "$INSTALL_PATH/.config/versions.sh" || printError "$?"
fi

# create link to $SCRIPT_FILE -> new bash command
printMessage "création du lien symbolique" "/usr/local/bin/nichoir"
rm /usr/local/bin/nichoir > /dev/null 2>&1
ln --symbolic "$INSTALL_PATH/$SCRIPT_FILE" /usr/local/bin/nichoir || printError "$?"

# permission to execute to all .sh files
# donne la permission en exécution aux fichiers .sh
# 	-> recherche des fichiers *.sh dans le répertoire d'install
printMessage "gestion des permissions des fichiers d'instal" "chmod 755"
find "$INSTALL_PATH" -name "*.sh" -exec chmod 755 {} \; || printError "$?"
