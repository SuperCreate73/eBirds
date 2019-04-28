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
# TODO modification dynamisque du contenu du message en POST
# curl --data @nomDuFichier https://ebirds.be/...
# Doit être créé dynamiquement car contient l'adresse mail paramétrable via
# l'interface web
#varMail=

curl --data "EMAIL=$varMail&CONTENT="@motionMailContent.txt https://ebirds.be/
