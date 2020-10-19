#!/bin/bash
# coding:UTF-8



function initVariables()
{
  BAD_OPTION=65			# unknow option used
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
      if [ "${!var}" ] then
        echo "UnitTestPass - input = $var"
      else
        echo "UnitTestError - options misconfigurated - input = $var"
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
  initVariables
  inputVar="--first"
  optionAnalyse "$inputVar"

  if [ "$?" -eq $BAD_OPTION ] ; then
    echo "UnitTestError - output bad option - input = $inputVar"
  elif [ "$varCheckBib" ] && [ "$varScriptInstal" ] ; then
    echo "UnitTestPass - input = $inputVar"
  else
    echo "UnitTestError - options misconfigurated - input = $inputVar"
  fi

  #-----------------------------------
  initVariables
  inputVar="--recall"
  optionAnalyse "$inputVar"

  if [ "$?" -eq $BAD_OPTION ] ; then
    echo "UnitTestError - output bad option - input = $inputVar"
  elif [ "$varRecall" ] ; then
    echo "UnitTestPass - input = $inputVar"
  else
    echo "UnitTestError - options misconfigurated - input = $inputVar"
  fi

  #-----------------------------------
  initVariables
  inputVar="--debug"
  optionAnalyse "$inputVar"

  if [ "$?" -eq $BAD_OPTION ] ; then
    echo "UnitTestError - output bad option - input = $inputVar"
  elif [ "$varDebug" ] ; then
    echo "UnitTestPass - input = $inputVar"
  else
    echo "UnitTestError - options misconfigurated - input = $inputVar"
  fi

  #-----------------------------------
  initVariables
  inputVar="-d"
  optionAnalyse "$inputVar"

  if [ "$?" -eq $BAD_OPTION ] ; then
    echo "UnitTestError - output bad option - input = $inputVar"
  elif [ "$varDebug" ] ; then
    echo "UnitTestPass - input = $inputVar"
  else
    echo "UnitTestError - options misconfigurated - input = $inputVar"
  fi

  #-----------------------------------
  initVariables
  inputVar="-e"
  optionAnalyse "$inputVar"

  if [ "$?" -eq $BAD_OPTION ] ; then
    echo "UnitTestError - output bad option - input = $inputVar"
  elif [ "$varError" ] ; then
    echo "UnitTestPass - input = $inputVar"
  else
    echo "UnitTestError - options misconfigurated - input = $inputVar"
  fi

  #-----------------------------------
  initVariables
  inputVar="-m"
  optionAnalyse "$inputVar"

  if [ "$?" -eq $BAD_OPTION ] ; then
    echo "UnitTestError - output bad option - input = $inputVar"
  elif [ "$varMotion" ] ; then
    echo "UnitTestPass - input = $inputVar"
  else
    echo "UnitTestError - options misconfigurated - input = $inputVar"
  fi


}
