#!/bin/bash
# coding:UTF-8

# Copy installation files
copyFiles "eBirds/instal/$SCRIPT_FILE" "$INSTALL_PATH" || printError "$?"
copyDir "eBirds/instal/.instalModel" "$INSTALL_PATH" || printError "$?"
copyDir "eBirds/instal/.input" "$INSTALL_PATH" || printError "$?"
copyDir "eBirds/instal/motion" "$INSTALL_PATH" || printError "$?"

if [ "$varCopyConfig" = true ] ; then
	copyFiles "eBirds/instal/.config/versions_init.sh" "$INSTALL_PATH/.config/versions.sh" || printerror "$?"
fi

# create link to $SCRIPT_FILE -> new bash command
printMessage "création du lien symbolique" "/usr/local/bin/nichoir"
createSymLink "$INSTALL_PATH/$SCRIPT_FILE" /usr/local/bin/nichoir || printError "$?"

# permission to execute to all .sh files
# donne la permission en exécution aux fichiers .sh
# 	-> recherche des fichiers *.sh dans le répertoire d'install
printMessage "gestion des permissions des fichiers d'instal" "chmod 755"
find "$INSTALL_PATH" -name "*.sh" -exec chmod 755 {} \; || printError "$?"
