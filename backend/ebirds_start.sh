#!/bin/bash

#One param not mandatory, either 'DEBUG' or 'INFO'
param=$1

#manual command : sudo python ebirdsv2.py -v

if [ $# -gt 0 ] ; then
#TODO to check if the ampersand is mandatory
  if [ $param == "--delay" ] ; then
    sleep 120
    python ebirdsIO.py &
  else
    python ebirdsIO.py $param &
  fi
else
  python ebirdsIO.py &
fi
