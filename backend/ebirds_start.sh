#!/bin/bash

#One param not mandatory, either 'DEBUG' or 'INFO'
param=$1

#manual command : sudo python ebirdsv2.py -v

if [ $# -gt 0 ] ; then
#TODO to check if the ampersand is mandatory
  if [ $param == "--delay" ] ; then
    sleep 120
    ebirdsv2.py &
  else
    ebirdsv2.py $param &
  fi
else
  ebirdsv2.py &
fi
