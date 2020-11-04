#!/bin/bash
# coding:UTF-8

function printMessage()
{
# print messages on terminal and/or log file :
# $1 description of current operation
# $2 item

	varMessage="$1 -- $2 --" 		# message format

	[ "$varVerbose" = true ] &&  echo "$varMessage"  	# write in console if applicable

	# prepare separation string '-----------'
	str=""
	(( count = 0 ))

	while [ $count -lt ${#varMessage} ] ; do
		str="$str-"
		(( count += 1 ))
	done

	echo -e "\n$varMessage \n$str " >> $LOG_FILE		# write in logfile

	return 0
}

function printError()
{
# print errors on console and log file :
# $1 error number
#

	if [ $1 -gt 0 ] ; then 	# if error number > 0
		(( varErrorCount += 1 ))	# error count increment
		echo "#### ERROR #### $varMessage - Error Code $1" >> $LOG_FILE			# write in logfile
		[ "$varError" ] && echo "    Error on execution - $varMessage - Error Code $1"	# write in console if applicable
	fi

	return 0
}

function updateParameter()
{
	# parameter update in config file :
	# $1 - config file
	# $2 - parameter
	# $3 - value
	# $4 - separator - defaut '='

	[ -f "$1" ] || return "$BAD_INPUT_FILE"

	local separator='='

	[ ! -z "$4" ] && separator="$4"
	# if parameter already exist
	if grep -q "$2" "$1" ; then
		sed "$1" -i -e 's/^'"$2$separator"'.*$/'"$2$separator$3"'/g' > /dev/null || return "$?"
	else
		echo "$2$separator$3" >> $1 || return "$?"
	fi
	return 0
}

function doInsertRecord()
{
	# insert record from input files given as parameters :
	# $x : input file(s)

	oldIFS="$IFS"

	for varFile in $* ; do
		while read varLine ; do
	    IFS="+"
			read tmpMain tmpRef <<< "$varLine"

			if [ "${tmpMain::1}" != '#' ] ; then
				if [ -n "$tmpRef" ] ; then
					IFS=":" read tmpTable tmpFields tmpValues <<< "$tmpRef"
					ref1=$(sqlite3 "$DB_FILE" "SELECT $tmpFields FROM $tmpTable WHERE $tmpValues ;")
					tmpMain=$(echo "$tmpMain" | sed 's/_REF1_/'"$ref1"'/' )
				fi

				IFS=":"
				read table fields values <<< "$tmpMain"
				printMessage "insertion des paramètres - table: $table - record: F$fields V$values" "nichoir.db"
				if [ -n "$table" ] ; then
					sqlite3 "$DB_FILE" "INSERT INTO $table $fields VALUES $values ;"
					printError "$?"
				fi
			fi

		done < "$varFile"
	done

	IFS="$oldIFS"

	return 0
}

################################################################################
# file / dir utilities
################################################################################
function createFile()
{
  # create file if does'nt exist
  # $1 name of file to create
  # $2 optional string to insert in new file

  [ -f $1 ] && return 0  # exit if already exist

  local inputString="$2"

  touch "$1" > /dev/null 2>&1 || return "$WRONG_PARAMETER"

  [ "$inputString" ] || inputString="Test string"
  echo "$inputString" >  "$1"
  return $?
}

function removeFile()
{
  # $1 name of file to remove
  # $2 option -f -> force--> not used

  [ -e "$1" ] && rm -r -d "$1"
  return $?
}

function removeDir()
{
  # $1 name of directory to remove
  # $2 option -f -> force --> not used

  removeFile "$1"
  return $?
}

function createDir()
{
  # create dir (and parent dir if needed)
  # $1 name of dir to create

  [ -e "$1" ] && return 0
  [ -d "$1" ] && return 0

  mkdir -p "$1"
  return $?
}

function copyFiles()
{
	# copy files or directories and create target dir if not exist

	local inputFiles=$1
	local outputDir=$2

  # basic tests of function parameter
  [ -f "$inputFiles" -o -d "$inputFiles" ] || return "$BAD_INPUT_FILE"
  [ ! -z "$outputDir" ] || return "$WRONG_PARAMETER"

	# create output dir if not exist
	printMessage "Create dir :" "$outputDir"
  if ! createDir "$outputDir" ; then
    printError "$?"
    return 1
  fi

	printMessage "Files copy :" "$inputFiles"
	if [ -d "$inputFiles" ] && [ -d "$outputDir" ] ; then  # if folder to dir
		cp -r --force "$inputFiles" "$outputDir" || printError "$?"
	elif [ -f "$inputFiles" ] && [ -d "$outputDir" ] ; then # if file to dir
		cp --force "$inputFiles" "$outputDir" || printError "$?"
	elif [ -f "$inputFiles" ] && [ ! -d "$outputDir" ]  ; then # if file to file
		cp --force "$inputFiles" "$outputDir" || printError "$?"
	else
		return "$WRONG_PARAMETER"
	fi
	return 0
}
