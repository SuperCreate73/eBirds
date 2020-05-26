#!/bin/bash
# coding:UTF-8

# configuration de motion
#------------------------
printMessage "paramétrage" "motion"
# mode démon par défaut quand motion est lancé dans la console


oldIFS="$IFS"

for varFile in $(ls "$INSTALL_PATH"/.input/MOTIONparam*) ; do
	while IFS=: read flag parameter value ; do

    case "$flag" in
      "N"|"P")
        sed "$motionPath" -i -e "s:^\(#\|;\)\? \?$parameter.*$:$parameter $value:g"
        ;;
      "C")
        sed "$motionPath" -i -e "s:^\(#\|;\)\? \?$parameter.*$:; $parameter $value:g"
        ;;
			"*")
				continue
				;;
    esac

    printError "$?"

	done < "$varFile"
done

IFS="$oldIFS"
