#!/bin/bash
# coding:UTF-8

# copy current version to installer version
cp -f ../instalNichoir.sh ../nichoir.sh

# extract archive -> to transform in create archive
# option -c: create; z:gzip; f:file
tar -zcf "../instal.tar.gz" "../instal"
# tail -n +$((numLine + 1)) $0 | tar zx 2> /dev/null

# use of 'cat' command to output archivefile
cat ../instal.tar.gz >> ../nichoir.sh

