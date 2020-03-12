#!/usr/bin/python3
# -*- coding: utf-8  -*-

import DBManager
import DHTSensor


import adafruit_si7021
from busio import I2C
from board import SCL, SDA

# TODO créer les objets correpondant aux capteurs installés
#         --> lecture dans la DB
# TODO modifier la DB en faisant une table avec les données des capteurs
#         --> rajouter un ID pour chaque paramètre
# TODO faire une fonction 'read' dynamique avec les données de la table de correspondance
#
###################################################################
# Modifie pour Python3 - sudo apt-get install python-pip3
# Adafruit DHT installé aussi avec pip3
# bibliothèque - sudo pip3 install adafruit-circuitpython-si7021
# Erreur régulière de lecture avec si7021 -> faire une boucle TRY et réessayer si erreur


if __name__ == "__main__":
# Capteurs possibles : DHT11, DHT22, HX711, IR, SI7021

    DB = "/var/www/nichoir.db"
    TABLE = "meteo"
    DHT = 11
    readOut = dict()
    i2c = I2C(SCL, SDA)
    sensor = adafruit_si7021.SI7021(i2c)

    dbMeteo = DBManager.DBManager(DB, 'meteo', "sqlite3").setAPI()
    dbSensors = DBManager.DBManager(DB, 'sensors', "sqlite3").setAPI()
    readOut['humExt'], readOut['tempExt'] = DHTSensor.DHTSensor(DHT, 17).read()
    readOut['humInt'], readOut['tempInt'] = round(sensor.relative_humidity,1), round(sensor.temperature, 1)


    if (dbMeteo.dbInsert(readOut)):
        print ('données sauvegardées')
    else:
        print ('données perdues')
