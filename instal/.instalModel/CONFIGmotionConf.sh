#!/bin/bash
# coding:UTF-8

# configuration de motion
#------------------------
printMessage "paramétrage" "motion"
# mode démon par défaut quand motion est lancé dans la console


oldIFS="$IFS"

for varFile in $(ls "$varInstalPath"/.input/MOTIONparam*) ; do
	while IFS=: read flag parameter value ; do

    case "$flag" in
      "N")
        sed "$motionPath" -i -e "s/^\(#\|;\)\? \?$parameter.*$/$parameter $value/g"
        ;;
      "P")
        sed "$motionPath" -i -e "s:^\(#\|;\)\? \?$parameter.*$:$parameter $value:g"
        ;;
      "C")
        sed "$motionPath" -i -e "s:^\(#\|;\)\? \?$parameter.*$:; $parameter $value:g"
        ;;
    esac

    printError "$?"

	done < "$varFile"
done

IFS="$oldIFS"
