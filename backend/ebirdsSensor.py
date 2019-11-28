#!/usr/bin/python2
# -*- coding: utf-8  -*-

import DBManager
import DHT11Sensor
import DHT22Sensor

class Sensors:
    """ Classe pour gérer les capteurs

    """
    pass

    # Connection à la table des capteurs
    # Lecture de la table
    # Retours d'un tableau
    # Identification des capteurs différents
    # Import des bibliothèques des différents capteurs
    # Recherche des classes nommées 'NomDuCapteur'Sensor.
    # Si elles existent import
    #   Sinon, message d'erreur
    # appel de la fonction 'readSensor' et lecture des valeurs
    #
    #
    #
    # TODO Adapter la DB pour indiquer le paramètre mesuré
    #   Ce paramètre sera ensuite utilisé pour la lecture
    #



# TODO créer les objets correspondant aux capteurs installés
#         --> lecture dans la DB
# TODO modifier la DB en faisant une table avec les données des capteurs
#         --> rajouter un ID pour chaque paramètre
# TODO faire une fonction 'read' dynamique avec les données de la table de correspondance
#


if __name__ == "__main__":
# Capteurs possibles : DHT11, DHT22, HX711, IR, SI7021

    DB = "/var/www/nichoir.db"
    TABLE = "meteo"
    DHT = 22
    readOut = dict()

    dbMeteo = DBManager.DBManager(DB, 'meteo', "sqlite3").setAPI()
    dbSensors = DBManager.DBManager(DB, 'sensors', "sqlite3").setAPI()

    sensorList = dbSensors.dbRead()

    readOut['humExt'], readOut['tempExt'] = DHTSensor.DHTSensor(DHT, 17).read()
    readOut['humInt'], readOut['tempInt'] = DHTSensor.DHTSensor(DHT, 27).read()

    if (dbMeteo.dbInsert(readOut)):
        print ('données sauvegardées')
    else:
        print ('données perdues')
