#!/bin/bash
# coding:UTF-8

# date : 28/04/2019
# créateur : bibi
# license : dwyw (do what you want)

#
# Script utilisé pour l'envoi de mail lors de la détection de mouvements
#
# Envoi d'une requête https au serveur central eBirds.be avec adresse mail du
# destinataire en GET
#
# TODO tester la dernière date d'envoi d'un message et annulerl'envoi si < 1 jour

varMail=""
varInterval=3600

if [ $varMail = "" ] ; then
  exit 0
fi

if [ -f .lastrun ] ; then
    last=$(cat .lastrun)
else
    last=0
fi

curr=$(date '+%s')
diff=$(($curr - $last))
if [ $diff -lt $varInterval ]; then
    exit 0
fi

echo "$curr" >.lastrun


# la variable varMail est complétée automatiquement lorsqu'une nouvelle adresse
# mail est encodée dans l'interface web

content=$(cat motionMailContent.txt)

curl --data "EMAIL=$varMail" --data "CONTENT=$content" https://ebirds.be/data/motionSendMail
