#!/bin/bash
# coding:UTF-8

source ".instalModel/Functions.sh"
source ".instalModel/SCRIPTinstal.sh"

function initVariables()
{
  BAD_OPTION=65			# unknow option used
  DEBUG_FILE="/var/www/debug.txt"
  INSTALL_PATH="/var/www/unitTest"
  ROOT_PATH="/var/www"
  DB_FILE="$ROOT_PATH/nichoir.db"
  WEB_PATH="$ROOT_PATH/html"

  SCRIPT_FILE="installNichoir-3.sh"
  LOG_FILE="$INSTALL_PATH/logInstal.log"
  VERSION="1.1 - 01-06-2020"

  # error constant
  BAD_OPTION=65			# unknow option used
  BAD_USER=66				# No root user
  GIT_ERROR=67			# unable to install git
  SOURCES_ERROR=68	# source files not found
  WRONG_PARAMETER=69 # wrong parameter sent to function

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
  varFirstInstall=false
}

function utestSingleOption()
{
  initVariables
  inputVar="$1"
  shift
  optionAnalyse "$inputVar"

  if [ "$?" -eq $BAD_OPTION ] ; then
    echo "UnitTestError - output bad option - input = $inputVar"
  else
    for var
    do
      if [ "${!var}" ] ; then
        echo "UnitTestPass - input = $inputVar - variable = $var"
      else
        echo "UnitTestError - options misconfigurated - input = $inputVar -variable = $var"
      fi
    done
  fi
}

function utestSingleOptionBad()
{
  initVariables
  inputVar="$1"
  optionAnalyse "$inputVar"

  if [ "$?" -eq $BAD_OPTION ] ; then
    echo "UnitTestPass - bad option - input = $inputVar"
  else
    echo "UnitTestError - output bad option = $? - input = $inputVar"
  fi
}

utestSingleOption "--first" "varCheckBib" "varScriptInstal"
utestSingleOption "--recall" "varRecall"
utestSingleOption "--debug" "varDebug"
utestSingleOption "-d" "varDebug"
utestSingleOption "-e" "varError"
utestSingleOption "-m" "varMotion"
utestSingleOption "-u" "varUpdate"
utestSingleOption "-l" "varResetLog"
utestSingleOption "-U" "varUpgrade"
utestSingleOption "-v" "varVerbose" "varError"
utestSingleOption "-f" "varCheckBib" "varCheckDB" "varMotion" "varWebAppInstal"
utestSingleOption "-s" "varCheckBib"
utestSingleOption "-i" "varScriptInstal"
utestSingleOption "-w" "varWebAppInstal" "varMotion"
utestSingleOptionBad "d"
utestSingleOptionBad "-z"
utestSingleOptionBad "-1"
utestSingleOptionBad "1"
utestSingleOptionBad "d"

#####################################################################
function createFile()
{
  # create file if does'nt exist
  # $1 name of file to create
  # $2 optional string to insert in new file

  [ -f $1 ] && return 0  # exit if already exist

  local inputString="$2"

  [ $inputString ] || inputString="Test string"
  echo "$inputString" >  "$1"
  return $?
}


function removeFile()
{
  # $1 name of file to remove
  # $2 option -f -> force
  [ -e "$1" ] && rm -r -d "$1"
  return "$?"
}

function removeDir()
{
  # $1 name of directory to remove
  # $2 option -f -> force

  return 0
}

function utestCopyFiles()
{

  # test cases :
  #   pass -> copy file to existing dir
  #   pass -> copy file to non existing dir
  #   pass -> copy file to file
  #   pass -> copy dir to existing dir
  #   pass -> copy dir to non existing dir
  #   pass -> bad parameters
  return 0
}
