#!/bin/bash
# coding:UTF-8



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

function utestOptionAnalyse()
{
  source ".instalModel/Functions.sh"

  #-----------------------------------
  initVariables
  inputVar="d"
  optionAnalyse "$inputVar"

  if [ ! "$?" -eq $BAD_OPTION ] ; then
    echo "UnitTestError - bad option - input = $inputVar"
  else
    echo "UnitTestPass - bad option - input = $inputVar"
  fi

  #-----------------------------------
  initVariables
  inputVar="z"
  optionAnalyse "$inputVar"

  if [ ! "$?" -eq $BAD_OPTION ] ; then
    echo "UnitTestError - bad option - input = $inputVar"
  else
    echo "UnitTestPass - bad option - input = $inputVar"
  fi

  #-----------------------------------
  initVariables
  inputVar="-z"
  optionAnalyse "$inputVar"

  if [ ! "$?" -eq $BAD_OPTION ] ; then
    echo "UnitTestError - bad option - input = $inputVar"
  else
    echo "UnitTestPass - bad option - input = $inputVar"
  fi

  #-----------------------------------
  initVariables
  inputVar="-1"
  optionAnalyse "$inputVar"

  if [ ! "$?" -eq $BAD_OPTION ] ; then
    echo "UnitTestError - bad option - input = $inputVar"
  else
    echo "UnitTestPass - bad option - input = $inputVar"
  fi

  #-----------------------------------
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

}

utestOptionAnalyse

