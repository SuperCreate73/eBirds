 #import RPi.GPIO as GPIO
import sys
from hx711 import HX711
import time
import random

#TODO pin a mettre en fichier de conf
pinData = 5
pinClock = 6
hx = HX711(pinData, pinClock)

# I've found out that, for some reason, the order of the bytes is not always the same between versions of python, numpy and the hx711 itself.
# Still need to figure out why does it change.
# If you're experiencing super random values, change these values to MSB or LSB until to get more stable values.
# There is some code below to debug and log the order of the bits and the bytes.
# The first parameter is the order in which the bytes are used to build the "long" value.
# The second paramter is the order of the bits inside each byte.
# According to the HX711 Datasheet, the second parameter is MSB so you shouldn't need to modify it.
hx.set_reading_format("LSB", "MSB")

# HOW TO CALCULATE THE REFFERENCE UNIT
# To set the reference unit to 1. Put 1kg on your sensor or anything you have and know exactly how much it weights.
# In this case, 92 is 1 gram because, with 1 as a reference unit I got numbers near 0 without any weight
# and I got numbers around 184000 when I added 2kg. So, according to the rule of thirds:
# If 2000 grams is 184000 then 1000 grams is 184000 / 2000 = 92.
#hx.set_reference_unit(113)
hx.set_reference_unit(92)

hx.reset()
hx.tare()

def getPoids(simu,logger):
    if (simu == 'Y'):
        logger.debug("Simulation capteur de poids")
        poids = random.randint(0,10000)
    else:
        logger.debug("Lecture capteur de poids")
        # These three lines are usefull to debug wether to use MSB or LSB in the reading formats
        # for the first parameter of "hx.set_reading_format("LSB", "MSB")".
        # Comment the line "poids = hx.get_weight(5)" and uncomment the three lines to see what it prints.
        #np_arr8_string = hx.get_np_arr8_string()
        #binary_string = hx.get_binary_string()
        #print binary_string + " " + np_arr8_string
      
        # Prints the weight. Comment if you're debbuging the MSB and LSB issue.
        # Param is the number of consecutive read. The value returned is the average
        poids = hx.get_weight(5)

        hx.power_down()
        hx.power_up()

    return poids
