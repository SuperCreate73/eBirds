#!/bin/bash

#One param not mandatory, either 'DEBUG' or 'INFO'
param=$1

#manual command : sudo python ebirdsv2.py -v

if [ $# -ne 1 ]; then
#TODO to check if the ampersand is mandatory
ebirdsv2.py &
exit
fi

ebirdsv2.py $param &
done

