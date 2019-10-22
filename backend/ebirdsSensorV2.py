#!/usr/bin/python2
# -*- coding: utf-8  -*-

import time
import ebirdsDb
import random


class DHTSensor:
    """ DHT sensor class
        Take as args :
            - sensor type as number (11 or 22)
    """

    def __init__(self, sensorType, pin):
        if (sensorType == 11 or sensorType == 22):
            self._type = sensorType
        else:
            raise ValueError('Type de capteur inconnu')
        self._pin = pin

    def tmpRead(self, loctype, locpin):
        # return (random.random()*20, random.random()*40)
        return (None, None)


    def read(self):
        """ lit la température 5 fois à 5 secondes d'intervalle et
            renvoie un tuple (température, humidité) avec la moyenne des trois
            valeurs médianes
        """
        count = 0
        listHumidity = listTemperature = []
        # humidity, temperature = dhtSensor.read(self._type, self._pin)

        while (len(listTemperature)< 6 or len(listHumidity) < 6) and count < 10:
            humidity, temperature = self.tmpRead(self._type, self._pin)

            if humidity:
                listHumidity.append(humidity)
            if temperature:
                listTemperature.append(temperature)

            time.sleep(5)
            count += 1
        print (count)
        listHumidity.sort()
        listTemperature.sort()
        listHumidity = [i for i in listHumidity if isinstance(i, int) or isinstance(i, float)]

        if len(listHumidity) > 2:
            humidity = round(sum(listHumidity[1:-1])/(len(listHumidity)-2),1)
        elif len(listHumidity) > 0:
            humidity = round(sum(listHumidity)/len(listHumidity),1)
        else:
            humidity = None

        if len(listTemperature) > 2:
            temperature = round(sum(listTemperature[1:-1])/(len(listTemperature)-2))
        elif len(listTemperature) > 0:
            temperature = round(sum(listTemperature)/len(listTemperature))
        else:
            temperature = None

        # return (round(float(sum(listTemperature[1:4]))/len(listTemperature),1),
                # round(float(sum(listHumidity[1:4]))/len(listHumidity),1))
        return (humidity, temperature)

# TODO créer les objets correpondant aux capteurs installés
#         --> lecture dans la DB
# TODO modifier la DB en faisant une table avec les données des capteurs
#         --> rajouter un ID pour chaque paramètre
# TODO faire une fonction 'read' dynamique avec les données de la table de correspondance

if __name__ == "__main__":
    DHT = 22
    readOut = dict()

    readOut['humExt'], readOut['tempExt'] = DHTSensor(DHT, 17).read()
    readOut['humInt'], readOut['tempInt'] = DHTSensor(DHT, 27).read()
    # readIn = DHTSensor(DHT, 21).read()

    print (readOut)
