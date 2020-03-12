#!/usr/bin/python3
# -*- coding: utf-8  -*-

import import adafruit_dht as dhtSensor
from board import <pin>

from time import sleep

class DHTSensor:
    """ DHT sensor class
        Take as args :
            - sensor type as number (11 or 22)
    """


    def __init__(self, sensorType, pin):
        dht_device = adafruit_dht.DHT11(<pin>)
        if (sensorType == 11 or sensorType == 22):
            self._type = sensorType
        else:
            raise ValueError('Type de capteur inconnu')
        self._pin = pin


    def __listAverage(self, inputList):
        """ Trie les valeurs numériques de la liste en entrée et calcule la
            moyenne pondérée (élimine les deux extrèmes)
        """
        inputList.sort()

        inputList = [i for i in inputList if isinstance(i, int) or isinstance(i, float)]

        if len(inputList) > 2:
            output = round(sum(inputList[1:-1])/(len(inputList)-2), 1)
        elif len(inputList) > 0:
            output = round(sum(inputList)/len(inputList), 1)
        else:
            output = None
        return output


    def read(self):
        """ lit la température 5 fois à 5 secondes d'intervalle et
            renvoie un tuple (humidité, température) avec la moyenne des trois
            valeurs médianes
        """
        count = 0
        listHumidity = []
        listTemperature = []

        # listHumidity = listTemperature = []
        humidity, temperature = dhtSensor.read(self._type, self._pin)
        while (len(listTemperature)< 6 or len(listHumidity) < 6) and count < 10:
            humidity, temperature = dhtSensor.read(self._type, self._pin)

            if humidity:
                listHumidity.append(humidity)

            if temperature:
                listTemperature.append(temperature)

            sleep(5)
            count += 1

        return (self.__listAverage(listHumidity), self.__listAverage(listTemperature))


if __name__ == "__main__":

    DHT = 22
    readOut = dict()

    readOut['humExt'], readOut['tempExt'] = DHTSensor(DHT, 17).read()
    readOut['humInt'], readOut['tempInt'] = DHTSensor(DHT, 27).read()

    print ('Hum Ext= {} - Hum In= {} - T Ext= {} - T In= {}'.format(readOut['humExt'], readOut['humInt'], readOut['tempExt'], readOut['tempInt']))
