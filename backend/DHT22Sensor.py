#!/usr/bin/python2
# -*- coding: utf-8  -*-

import DHTSensor

class DHT22Sensor(DHTSensor):
    """ DHT22 sensor class
        Take as args :
            - Sensor Pin
    """

    def __init__(self, pin):
        DHTSensor.__init__(self, 22, pin)
