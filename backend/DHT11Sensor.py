#!/usr/bin/python2
# -*- coding: utf-8  -*-

import DHTSensor

class DHT11Sensor(DHTSensor):
    """ DHT11 sensor class
        Take as args :
            - Sensor Pin
    """

    def __init__(self, pin):
        DHTSensor.__init__(self, 11, pin)
