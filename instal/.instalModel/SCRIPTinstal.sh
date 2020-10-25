#!/bin/bash
# coding:UTF-8

function createDir()
{
  # create dir (and parent dir if needed)
  # $1 name of dir to create

  [ ! -d $1 ] && mkdir -p "$1"
  return $?
}

function copyFiles()
{
	# copy files or directories and create target dir if not exist

	inputFiles=$1
	outputDir=$2

	# create output dir if not exist
	createDir "$outputDir" || printerror "$?"

	printMessage "Files copy :" "$inputFiles"
	if [ -d $inputFiles ] && [ -d $outputDir ] ; then  # if folder to dir
		cp -r --force "$inputFiles" "$outputDir" || printError "$?"
	elif [ -f $inputFiles ] && [ -d $outputDir ] ; then # if file to dir
		cp --force "$inputFiles" "$outputDir" || printError "$?"
	elif [ -f $inputFiles ] && [ ! -d $outputDir ]  ; then # if file to file
		cp --force $inputFiles "$outputDir" || printError "$?"
	else
		return "$WRONG_PARAMETER"
	fi
	return 0
}

# Copy installation files
copyFiles "eBirds/instal/$SCRIPT_FILE" "$INSTALL_PATH" || printerror "$?"
copyFiles "eBirds/instal/.instalModel" "$INSTALL_PATH" || printerror "$?"
copyFiles "eBirds/instal/.input" "$INSTALL_PATH" || printerror "$?"
copyFiles "eBirds/instal/motion" "$INSTALL_PATH" || printerror "$?"

if [ "$varCopyConfig" = true ] ; then
	copyFiles "eBirds/instal/.config/versions_init.sh" "$INSTALL_PATH/.config/versions.sh" || printerror "$?"
fi

# create link to $SCRIPT_FILE -> new bash command
printMessage "création du lien symbolique" "/usr/local/bin/nichoir"
rm /usr/local/bin/nichoir > /dev/null 2>&1  # remove if exist
ln --symbolic "$INSTALL_PATH/$SCRIPT_FILE" /usr/local/bin/nichoir || printError "$?"

# permission to execute to all .sh files
# donne la permission en exécution aux fichiers .sh
# 	-> recherche des fichiers *.sh dans le répertoire d'install
printMessage "gestion des permissions des fichiers d'instal" "chmod 755"
find "$INSTALL_PATH" -name "*.sh" -exec chmod 755 {} \; || printError "$?"
