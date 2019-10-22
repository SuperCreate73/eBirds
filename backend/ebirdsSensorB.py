#!/usr/bin/python2
# -*- coding: utf-8  -*-

import Adafruit_DHT.common as dhtSensor
import sqlite3
import time

class APISqlite3:
    """ SQLIte3 Interface -
        Init parameters:
            - File (str): db file to manage
            - Table (str): table to manage
    """

    def __init__(self, dbFile, dbTable):
        self._dbFile = dbFile
        self._dbTable = dbTable

    def dbInsert(self, key, value):
        """ Insert one record in table.
            Take as args key and value to insert
        """
        lv_conn = sqlite3.connect(self._dbFile)
        cursor  = lv_conn.cursor()

        try:
           cursor.execute('''INSERT INTO meteo (?) VALUES (?)''',( key, value))

        except Exception as e:
            lv_conn.rollback()
            lv_conn.close()
            return e

        lv_conn.commit()
        lv_conn.close()

        return False

    def dbRead(self, whereClause = None):
        # TODO if needed
        return whereClause

    def dbDelete(self, whereClause = None):
        # TODO if needed
        return whereClause

    def dbExist(self, whereClause = None):
        # TODO if needed
        return whereClause

    def dbCount(self, whereClause = None):
        # TODO if needed
        return whereClause


class DBManager:

    def __init__(self, dbFile, dbTable, apiLink):
        self._APIlink = "API{}".format(apiLink.capitalize())
        self._dbFile = dbFile
        self._dbTable = dbTable

    def setAPI(self):
        return globals()[self._APIlink](self._dbFile, self._dbTable)


class DHTSensor:
    """ DHT sensor class
        Take as args :
            - sensor type as number (11 or 22)
	    - pin
    """


    def __init__(self, sensorType, pin):
        if (sensorType == 11 or sensorType == 22):
            self._type = sensorType
        else:
            raise ValueError('Type de capteur inconnu')
        self._pin = pin

    def read(self):
        """ lit la température 5 fois à 5 secondes d'intervalle et
            renvoie un tuple (température, humidité) avec la moyenne des trois
            valeurs médianes
        """

        humidity, temperature = dhtSensor.read(self._type, self._pin)

        return (humidity, temperature)
#        return (round(float(sum(listTemperature[1:4]))/len(listTemperature),1), round(float(sum(listHumidity[1:4]))/len(listHumidity),1))


# TODO créer les objets correpondant aux capteurs installés
#         --> lecture dans la DB
# TODO modifier limport Adafruit_DHT.common as dhtSensora DB en faisant une table avec les données des capteurs
#         --> rajouter un ID pour chaque paramètre
# TODO faire une fonction 'read' dynamique avec les données de la table de correspondance

if __name__ == "__main__":

    DB = "/var/www/nichoir.db"
    TABLE = "meteo"
    DHT = 11

    dbCapteur = DBManager(DB, TABLE, "sqlite3").setAPI()
    readOut = DHTSensor(DHT, 17).read()
    readIn = DHTSensor(DHT, 27).read()

    if (dbCapteur.dbInsert('tempExt' , readOut [0]) and
        dbCapteur.dbInsert('humExt'    , readOut [1]) and
        dbCapteur.dbInsert('tempInt'  , readIn [0] ) and
        dbCapteur.dbInsert('humInt'    , readIn [1] ) ):
        print ('données sauvegardées')
    else:
        print (dbCapteur.dbInsert('tempExt' , readOut [0]))

