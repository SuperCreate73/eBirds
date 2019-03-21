import RPi.GPIO as GPIO
import time
import sys

GPIO.setwarnings(False)
def cleanAndExit():
    print "Cleaning..."
    GPIO.cleanup()
    print "Bye!"
    sys.exit()

GPIO.setmode(GPIO.BCM)

GPIO.setup(16,GPIO.IN)
GPIO.setup(20,GPIO.IN)

# Detection function callback with default parameter being the input channel
def detection(channel):
    if (int(channel)==16):
	result = GPIO.input(16)
        print ("IN - {0} - salut {1}".format(result,channel))
    else:
	result = GPIO.input(20)
        print ("OUT - {0} - saluthannel {1}".format(result,channel))

# Interrupt function from the GPIO library
GPIO.add_event_detect(16, GPIO.BOTH, callback=detection, bouncetime=300)
GPIO.add_event_detect(20, GPIO.BOTH, callback=detection, bouncetime=300)

# This loop's purpose is only to test the interrupt of the event triggered here above
while True:
    try:
       result = GPIO.input(16)
       print (str(GPIO.input(16))+"--"+str(GPIO.input(20)))
       time.sleep(5)

    except (KeyboardInterrupt, SystemExit):
       cleanAndExit()
