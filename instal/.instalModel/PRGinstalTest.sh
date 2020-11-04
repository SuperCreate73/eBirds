#!/bin/bash
# coding:UTF-8

	oldIFS=$IFS
	IFS=:
	read program description <<< $*
#	program=$1
#	shift
#	description=$@

	echo "Program = $program -- Description = $description" >> testfile.txt
	IFS=$oldIFS
