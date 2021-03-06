#!/bin/bash
# coding:UTF-8

# source ".instalModel/Functions.sh"
# source ".instalModel/SCRIPTinstal.sh"

function initVariables()
{
  BAD_OPTION=65			# unknow option used
  DEBUG_FILE="test/debug.txt"
  INSTALL_ROOTPATH="test"
  WEBAPP_ROOTPATH="/var/www"
  DB_FILE="$WEBAPP_ROOTPATH/nichoir.db"
  WEB_PATH="$WEBAPP_ROOTPATH/html"

  SCRIPT_FILE="installNichoir-3.sh"
  LOG_FILE="$INSTALL_ROOTPATH/logInstal.log"
  VERSION="1.1 - 01-06-2020"

  # error constant
  BAD_OPTION=65			# unknow option used
  BAD_USER=66				# No root user
  GIT_ERROR=67			# unable to install git
  SOURCES_ERROR=68	# source files not found
  WRONG_PARAMETER=69 # wrong parameter sent to function
  BAD_INPUT_FILE=70 # bad input file sent to function
  PROCESSING_LINE_ERROR=71 # program installation process error
  INSTALLATION_ERROR=72 # error processing in pgrInstalation function
  CREATE_DIR_ERROR=73 # unable to create dir
  CREATE_SYMLINK_ERROR=74 # unable to create symLink
  CREATE_TABLE_ERROR=75	# error creating DB table
  INSERT_DB_ERROR=76 # Error when inserting in DB
  SUBSTITUTE_ERROR=77	# Error during substitution

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

initVariables
source ".instalModel/Functions.sh"
source ".instalModel/FunctionsHelpers.sh"
#source ".instalModel/SCRIPTinstal.sh"

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

# utestSingleOption "--first" "varCheckBib" "varScriptInstal"
# utestSingleOption "--recall" "varRecall"
# utestSingleOption "--debug" "varDebug"
# utestSingleOption "-d" "varDebug"
# utestSingleOption "-e" "varError"
# utestSingleOption "-m" "varMotion"
# utestSingleOption "-u" "varUpdate"
# utestSingleOption "-l" "varResetLog"
# utestSingleOption "-U" "varUpgrade"
# utestSingleOption "-v" "varVerbose" "varError"
# utestSingleOption "-f" "varCheckBib" "varCheckDB" "varMotion" "varWebAppInstal"
# utestSingleOption "-s" "varCheckBib"
# utestSingleOption "-i" "varScriptInstal"
# utestSingleOption "-w" "varWebAppInstal" "varMotion"
# utestSingleOptionBad "d"
# utestSingleOptionBad "-z"
# utestSingleOptionBad "-1"
# utestSingleOptionBad "1"
# utestSingleOptionBad "d"

#####################################################################

function utestFileUtility()
{
  initVariables

  # create file in existing dir with input string
  createFile tstFile.txt "test simple de string à intégrer"
  if [ ! "$?" -eq 0 ] ; then
    echo "UnitTestError - unable to create file - input = tstFile.txt"
  else
    echo "UnitTestPass - createFile - input = tstFile.txt"
  fi

  # create file in existing dir with input string
  createFile tstFile18.txt "# coding:UTF-8 \
  \
  $(date) \
  \
  "
  if [ ! "$?" -eq 0 ] ; then
    echo "UnitTestError - unable to create file - input = tstFile.txt"
  else
    echo "UnitTestPass - createFile - input = tstFile.txt"
  fi

  # create file in existing dir no input string
  createFile tstFile2.txt
  if [ ! "$?" -eq 0 ] ; then
    echo "UnitTestError - unable to create file - input = tstFile2.txt"
  else
    echo "UnitTestPass - createFile - input = tstFile2.txt"
  fi

  # create file same name
  createFile tstFile2.txt
  if [ ! "$?" -eq 0 ] ; then
    echo "UnitTestError - unable to create - file exist - input = tstFile2.txt"
  else
    echo "UnitTestPass - createFile - file exist - input = tstFile2.txt"
  fi

  # create file non existing dir -> error
  createFile corona/tstFile2.txt
  if [ "$?" -eq 0 ] ; then
    echo "UnitTestError - unable to create - unknow dir - input = tstFile2.txt"
  else
    echo "UnitTestPass - createFile - error on unknow dir - input = tstFile2.txt"
  fi

  # remove existing file
  removeFile tstFile.txt
  if [ ! "$?" -eq 0 ] ; then
    echo "UnitTestError - unable to delete file - input = tstFile.txt"
  else
    echo "UnitTestPass - deleteFile - input = tstFile.txt"
  fi

  # remove non existing file -> no error, nothing happens
  removeFile tstFile3.txt
  if [ ! "$?" -eq 0 ] ; then
    echo "UnitTestError - deleteFile - false input = tstFile3.txt"
  else
    echo "UnitTestPass - deleteFile - false input = tstFile3.txt"
  fi

  # create dir
  createDir dirTest
  if [ ! "$?" -eq 0 ] ; then
    echo "UnitTestError - unable to create dir - input = dirTest"
  else
    echo "UnitTestPass - createDir - input = dirTest"
  fi

  # create dir with parent
  createDir "dirTest2/dirtest2"
  if [ ! "$?" -eq 0 ] ; then
    echo "UnitTestError - unable to create parent dir - input = dirTest2/dirtest2"
  else
    echo "UnitTestPass - create parent Dir - input = dirTest2/dirtest2"
  fi

  # remove dir
  removeDir dirTest
  if [ ! "$?" -eq 0 ] ; then
    echo "UnitTestError - unable to remove dir - input = dirTest"
  else
    echo "UnitTestPass - remove dir - input = dirTest"
  fi

  # remove dir with subfiles
  removeDir dirTest2
  if [ ! "$?" -eq 0 ] ; then
    echo "UnitTestError - unable to remove dir with sub - input = dirTest2"
  else
    echo "UnitTestPass - remove dirwith sub - input = dirTest2"
  fi

  # remove non existing dir -> no error, nothing happens
  removeDir cacophonie
  if [ ! "$?" -eq 0 ] ; then
    echo "UnitTestError - error on remove non existing dir - input = dirTest"
  else
    echo "UnitTestPass - remove non existing dir -> error - input = dirTest"
  fi

  # copy file to existing dir
  copyFiles tstFile2.txt "test/"
  if [ ! "$?" -eq 0 ] ; then
    echo "UnitTestError - unable to copy file in existing dir - input = tstFile2.txt test/"
  else
    echo "UnitTestPass - copy file to existing dir - input = tstFile2.txt test/"
  fi

  # copy file to non existing dir
  copyFiles tstFile2.txt "test2/"
  if [ ! "$?" -eq 0 ] ; then
    echo "UnitTestError - unable to copy file to non existing dir - input = tstFile2.txt test2/"
  else
    echo "UnitTestPass - copy file to non existing dir - input = tstFile2.txt test2/"
  fi

  # copy file to existing file
  copyFiles tstFile2.txt "test/tstFileCopy.svg"
  if [ ! "$?" -eq 0 ] ; then
    echo "UnitTestError - unable to copy file to existing file - input = tstFile2.txt test/tstFileCopy.svg"
  else
    echo "UnitTestPass - copy file to existing file - input = tstFile2.txt test/tstFileCopy.svg"
  fi

  # copy file to non existing file
  copyFiles tstFile2.txt "test/newTestCopy.txt"
  if [ ! "$?" -eq 0 ] ; then
    echo "UnitTestError - unable to copy file to non existing file - input = tstFile2.txt test/newTestCopy.txt"
  else
    echo "UnitTestPass - copy file to non existing file - input = tstFile2.txt test/newTestCopy.txt"
  fi

  # copy dir to existing dir
  copyFiles motion "test/testDir1"
  if [ ! "$?" -eq 0 ] ; then
    echo "UnitTestError - unable to copy dir to existing dir - input = motion test/testDir1"
  else
    echo "UnitTestPass - copy dir to existing dir - input = motion test/testDir1"
  fi

  # copy dir to non existing dir
  copyFiles motion "test/testDir2"
  if [ ! "$?" -eq 0 ] ; then
    echo "UnitTestError - unable to copy dir to non existing dir - input = motion test/testDir2"
  else
    echo "UnitTestPass - copy dir to non existing dir - input = motion test/testDir2"
  fi

  # copy empty dir to existing dir
  copyFiles "test/testDir7" "test/testDir5"
  if [ ! "$?" -eq 0 ] ; then
    echo "UnitTestError - unable to copy empty dir to existing dir - input = motion test/testDir7"
  else
    echo "UnitTestPass - copy empty dir to existing dir - input = motion test/testDir7"
  fi

  # Bad parameter
  copyFiles 1 2
  if [ ! "$?" -eq 70 ] ; then
    echo "UnitTestError - undetected bad parameter $? - input = 1 2"
  else
    echo "UnitTestPass - detected bad parameter $? - input = 1 2"
  fi

  # missing parameter
  copyFiles motion
  if [ ! "$?" -eq "$WRONG_PARAMETER" ] ; then
    echo "UnitTestError - undetected missing parameter $? - input = 1"
  else
    echo "UnitTestPass - detected missing parameter $? - input = 1"
  fi


  # copy dir to existing dir
  mkdir "test/testDir3" > /dev/null 2>&1
  copyDir motion "test/testDir3"
  tmpResponse="$?"
  if [ ! "$tmpResponse" -eq 0 ] ; then
    echo "UnitTestError - unable to copy dir to existing dir - $tmpResponse input = motion test/testDir3"
  else
    echo "UnitTestPass - copy dir to existing dir - input = motion test/testDir3"
  fi

  # copy dir to non existing dir
  copyDir motion "test/testDir4"
  tmpResponse="$?"
  if [ ! "$tmpResponse" -eq 0 ] ; then
    echo "UnitTestError - unable to copy dir to non existing dir - $tmpResponse input = motion test/testDir2"
  else
    echo "UnitTestPass - copy dir to non existing dir - input = motion test/testDir2"
  fi

  # Bad parameter
  copyDir 1 2
  if [ ! "$?" -eq 70 ] ; then
    echo "UnitTestError - undetected bad parameter $? - input = 1 2"
  else
    echo "UnitTestPass - detected bad parameter $? - input = 1 2"
  fi

  # missing parameter
  copyDir motion
  if [ ! "$?" -eq "$WRONG_PARAMETER" ] ; then
    echo "UnitTestError - undetected missing parameter $? - input = 1"
  else
    echo "UnitTestPass - detected missing parameter $? - input = 1"
  fi


  # clear empty dir
  clearDir "test/testDir5"
  tmpResponse="$?"
  if [ ! "$tmpResponse" -eq 0 ] ; then
    echo "UnitTestError - unable to clear empty dir - $tmpResponse - input = test/testDir5"
  else
    echo "UnitTestPass - clear empty dir - input = test/testDir5"
  fi

  # clear dir containing other dirs
  clearDir "test/testDir4"
  tmpResponse="$?"
  if [ ! "$tmpResponse" -eq 0 ] ; then
    echo "UnitTestError - unable to copy clear non empty dir - $tmpResponse - input = test/testDir4"
  else
    echo "UnitTestPass - clear non empty dir - input = test/testDir4"
  fi

  # missing parameter
  clearDir "test/testDir6"
  tmpResponse="$?"
  if [ ! "$tmpResponse" -eq "$WRONG_PARAMETER" ] ; then
    echo "UnitTestError - undetected wrong parameter $tmpResponse - input = test/testDir6"
  else
    echo "UnitTestPass - detected missing parameter $tmpResponse - input = test/testDir6"
  fi

  # symlink creation with input file
  createSymLink "/home/olivier/Documents/nichoir/web/eBirdsGit/instal/test/test/tstFile2.txt" "test/symLink1"
  tmpResponse="$?"
  if [ ! "$tmpResponse" -eq 0 ] ; then
    echo "UnitTestError - Unable to create file symLink1 ($tmpResponse) - input = test/tstFile2.txt test/symLink1"
  else
    echo "UnitTestPass - file symLink1 successfull created - input = test/tstFile2.txt test/symLink1"
  fi

  # symlink creation with input dir
  createSymLink "/home/olivier/Documents/nichoir/web/eBirdsGit/instal/test/test/testDir1" "test/symLink2"
  tmpResponse="$?"
  if [ ! "$tmpResponse" -eq 0 ] ; then
    echo "UnitTestError - Unable to create dir symLink1 ($tmpResponse) - input = test/testDir1 test/symLink2"
  else
    echo "UnitTestPass - dir symLink2 successfull created - input = test/testDir1 test/symLink2"
  fi

  # symlink creation with non existing input file
  createSymLink "/home/olivier/Documents/nichoir/web/eBirdsGit/instal/test/test/tstFile18.txt" "test/symLink3"
  tmpResponse="$?"
  if [ ! "$tmpResponse" -eq "$BAD_INPUT_FILE" ] ; then
    echo "UnitTestError - bad input not detected when creating symLink3 ($tmpResponse) - input = test/tstFile18.txt test/symLink3"
  else
    echo "UnitTestPass - bad input detected when creating symlink - input = test/tstFile18.txt test/symLink3"
  fi

  # symlink creation with existing target file
  createSymLink "/home/olivier/Documents/nichoir/web/eBirdsGit/instal/test/test/testDir1" "test/testSymLink"
  tmpResponse="$?"
  if [ ! "$tmpResponse" -eq 0 ] ; then
    echo "UnitTestError - Unable to create symLink on existing target file ($tmpResponse) - input = test/testDir1 test/testSymLink"
  else
    echo "UnitTestPass - symLink on existing target file successfully created - input = test/testDir1 test/testSymLink"
  fi

  # symlink creation with non existing output Dir
  createSymLink "/home/olivier/Documents/nichoir/web/eBirdsGit/instal/test/test/tstFile2.txt" "test/testDir52/symlink4"
  tmpResponse="$?"
  if [ "$tmpResponse" -eq 0 ] ; then
    echo "UnitTestError - bad output dir not detected when creating symLink4 ($tmpResponse) - input = test/tstFile18.txt test/testDir52/symlink4"
  else
    echo "UnitTestPass - bad output dir detected when creating symlink4 ($tmpResponse) - input = test/tstFile18.txt test/testDir52/symlink4"
  fi

  return 0
}

#utestFileUtility

######################################################################################################

function uTestApplyConfig()
{
  initVariables

  # normal behaviour
  readInputFile "$INSTALL_ROOTPATH/.input/PRGlist" "prgInstallation"
  local TMP_OUTPUT="$?"
  if [ ! "$TMP_OUTPUT" -eq 0 ] ; then
    echo "UnitTestError - Error on applying config - $TMP_OUTPUT"
  else
    echo "UnitTestPass - uTestApplyConfig - $TMP_OUTPUT"
  fi

  TMP_OUTPUT=0
  # wrong parameter error
  readInputFile "$INSTALL_ROOTPATH/.input/PRplist" "prgInstallation"
  TMP_OUTPUT="$?"
  if [ "$TMP_OUTPUT" -eq 0 ] ; then
    echo "UnitTestError - wrong file not detected - $TMP_OUTPUT"
  else
    echo "UnitTestPass - wrong file detected - $TMP_OUTPUT"
  fi

  TMP_OUTPUT=0
  # wrong function call error
  readInputFile "$INSTALL_ROOTPATH/.input/PRGlist" "prgInstallation23"
  TMP_OUTPUT="$?"
  if [ "$TMP_OUTPUT" -eq 0 ] ; then
    echo "UnitTestError - wrong function name not detected - $TMP_OUTPUT"
  else
    echo "UnitTestPass - wrong function name detected - $TMP_OUTPUT"
  fi
}

# uTestApplyConfig

#################################################################################################
function uTestApplyPythonConfig()
{
  initVariables

  # normal behaviour
  readInputFile "$INSTALL_ROOTPATH/.input/PYTHONlist" "pythonInstallation"
  local TMP_OUTPUT="$?"
  if [ ! "$TMP_OUTPUT" -eq 0 ] ; then
    echo "UnitTestError - Error on applying config - $TMP_OUTPUT"
  else
    echo "UnitTestPass - uTestApplyConfig - $TMP_OUTPUT"
  fi

  TMP_OUTPUT=0
  # wrong parameter error
  readInputFile "$INSTALL_ROOTPATH/.input/PYTHOlist" "pythonInstallation"
  TMP_OUTPUT="$?"
  if [ "$TMP_OUTPUT" -eq 0 ] ; then
    echo "UnitTestError - wrong file not detected - $TMP_OUTPUT"
  else
    echo "UnitTestPass - wrong file detected - $TMP_OUTPUT"
  fi

  TMP_OUTPUT=0

}

# uTestApplyPythonConfig

function uTestSubstitute()
{
  local TMP_OUTPUT

  # simple test
  # substitute "tstFile2.txt" "Test:Essai"
  # TMP_OUTPUT="$?"
  # if [ ! "$TMP_OUTPUT" -eq 0 ] ; then
  #   echo "UnitTestError - Error on substitution - $TMP_OUTPUT"
  # else
  #   echo "UnitTestPass - substitution is OK - $TMP_OUTPUT"
  # fi

  # wrong file
  substitute "Test:Essai" "testFile385.txt"
  TMP_OUTPUT="$?"
  if [ "$TMP_OUTPUT" -eq 0 ] ; then
    echo "UnitTestError - substitute - wrong file not detected - $TMP_OUTPUT"
  else
    echo "UnitTestPass - substitution - wrong file detected - $TMP_OUTPUT"
  fi

  # first string empty
  substitute ":Essai" "tstFile2.txt"
  TMP_OUTPUT="$?"
  if [ "$TMP_OUTPUT" -eq 0 ] ; then
    echo "UnitTestError - substitute - search string empty not detected - $TMP_OUTPUT"
  else
    echo "UnitTestPass - substitution - search string empty detected - $TMP_OUTPUT"
  fi

  # normal behaviour
  readInputFile "testSubstitute.txt" "substitute" "tstFile2.txt"
  local TMP_OUTPUT="$?"
  if [ ! "$TMP_OUTPUT" -eq 0 ] ; then
    echo "UnitTestError - Error on applying config - $TMP_OUTPUT"
  else
    echo "UnitTestPass - uTestApplyConfig - $TMP_OUTPUT"
  fi

}

# uTestSubstitute

# readInputFile "MOTIONcompare.txt" "substitute" "$(ls DBinsertMotion_*.txt)" || echo "$?"

readInputFile "$INSTALL_ROOTPATH/.input/MOTIONparam" "motionConfig" "motion.conf" || printError "$?"
##################################################################################################
# updateParameter "tstFile19.txt" "test1" "test1Modifié" || printError "$?"
#
# updateParameter "tstFile19.txt" "test2" "test1Modifié" ":" || printError "$?"
#
# updateParameter "tstFile19.txt" "test3" "test3Modifié" " " || printError "$?"
#
# updateParameter "tstFile20.txt" "test3" "test3Modifié" " " || printError "$?"
