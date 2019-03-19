#!/usr/bin/python
# Copyright (c) 2014 Adafruit Industries
# Author: Tony DiCola

# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:

# The above copyright notice and this permission notice shall be included in all
# copies or substantial portions of the Software.

# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
# SOFTWARE.
import sys
import Adafruit_DHT
import time
import random

# Sensor should be set to Adafruit_DHT.DHT11,
# Adafruit_DHT.DHT22, or Adafruit_DHT.AM2302.
# Parse command line parameters.
#sensor_args = { '11': Adafruit_DHT.DHT11,
#                '22': Adafruit_DHT.DHT22,
#                '2302': Adafruit_DHT.AM2302 }
#if len(sys.argv) == 3 and sys.argv[1] in sensor_args:
#    sensor = sensor_args[sys.argv[1]]
#    pin = sys.argv[2]
#else:
#    print('usage: sudo ./Adafruit_DHT.py [11|22|2302] GPIOpin#')
#    print('example: sudo ./Adafruit_DHT.py 2302 4 - Read from an AM2302 connected to GPIO #4')
#    sys.exit(1)

# Simply force the sensor to DHT11 for the moment..
sensor = Adafruit_DHT.DHT11

# Raspberry Pi with DHT sensor connected to GPIO17 and GPIO27
# TODO replace by param from config file
pinIn = 17
pinOut = 27

# TODO test return in case of failure reading
def getTempHum(simu,logger):
    if (simu == "Y"):
        logger.debug("Simulation capteur meteo DHT")
        humidityOut, temperatureOut = random.randint(0,100), random.randint(0,50)
        humidityIn, temperatureIn = random.randint(0,100), random.randint(0,50)
    else:
        logger.debug("Lecture capteur meteo DHT")
        # Try to grab a sensor reading.  Use the read_retry method which will retry up
        # to 15 times to get a sensor reading (waiting 2 seconds between each retry).
        humidityOut, temperatureOut = Adafruit_DHT.read_retry(sensor, pinOut)
        logger.debug('TempOut={0}C HumidityOut={1}%'.format(temperatureOut, humidityOut))
        humidityIn, temperatureIn = Adafruit_DHT.read_retry(sensor, pinIn)
        logger.debug('TempIn={0}C  HumidityIn={1}%'.format(temperatureIn, humidityIn))

    # Note that sometimes you won't get a reading and
    # the results will be null (because Linux can't
    # guarantee the timing of calls to read the sensor).
    # If this happens try again!
    if humidityIn is not None and temperatureIn is not None:
        # Formula to convert the temperature to Fahrenheit:
        # temperature = temperature * 9/5.0 + 32
        #print('Temp={0:0.1f}*  Humidity={1:0.1f}%'.format(temperature, humidity))
        #print('TempIn={0}*C  HumidityIn={1}%'.format(temperatureIn, humidityIn))
        logger.debug('TempIn={0}C  HumidityIn={1}% -- TempOut={2}C HumidityOut={3}%'.format(temperatureIn, humidityIn, temperatureOut, humidityOut))
        return (temperatureOut, humidityOut, temperatureIn, humidityIn)
    else:
        logger.warning('Failed to get reading from Adafruit DHT. Bypassed')
        #sys.exit(1)


