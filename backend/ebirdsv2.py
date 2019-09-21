#!/usr/bin/python2
# -*- coding: utf-8  -*-

# TODO: check if program doesn't run already..

## Declaration des bibliotheques
import sqlite3  # lecture et ecriture de la DB
import sys      # systeme standart de python
sys.path.append('./capteurs') # inclus le repertoire 'capteurs' dans le system path
import logging  # ecriture de messages d'etat dans un fichier log
import time     # outils de gestion du temps (pause/sleep)
from datetime import datetime   # date et heure
import random   # generation aleatoire de nombre

## Import des fonctions capteurs
#TODO import generique (pas relatif a une marque de capteur en particulier)
#import meteo       # import du capteur temp/humidite
import Adafruit_DHT  # import du capteur temp/humidite DHT11
import balance     # import du capteur de poids
#import hx711        # import du capteur de poids HX711
#import IR_InOut    # import des capteurs IR d'entree/sortie

import RPi.GPIO as GPIO
GPIO.setwarnings(False)
GPIO.setmode(GPIO.BCM)
GPIO.setup(16,GPIO.IN)
GPIO.setup(20,GPIO.IN)

import threading

## Initialisation du logging
#TODO rendre le chemin relatif (fonction du user qui ne sera pas specialement 'pi')
logging.basicConfig(filename='/var/www/backend/ebirds.log',filemode='a',
                    format='%(levelname)s:%(asctime)s-%(message)s')
logger=logging.getLogger('LoggingIR')

## Analyse des options
## Par defaut, le mode de logging est INFO et pas de simulation des capteurs
logger.setLevel('INFO')
lv_simu='N'
## Il est mis a DEBUG si l'option verbose est specifiee.
#TODO ameliorer le message si l(es) argument(s) ne sont pas corrects
if (len(sys.argv) > 1):
    if (sys.argv[1] == "-v" or sys.argv[1] == "-verbose"):
        logger.setLevel('DEBUG')
    else:
        logger.warning('Unknow parameter : %s' + sys.argv[1])
    if (len(sys.argv) > 2):
        if (sys.argv[2] == "-s" or sys.argv[2] == "-simu"):
            lv_simu = 'Y'
        else:
            logger.warning('Unknow parameter : %s' + sys.argv[2])

#Travailler dans le repertoire 'eBirds'
#TODO rendre le chemin relatif (fonction du user qui ne sera pas specialement 'pi')
# 2 DB necessaires car sqlite ne gere pas les acces concurrents (DB = fichier)
# hors l'utilisation de Threads implique des acces DB concurrents
global GV_DBNAME
global GV_DBNAME2
#TODO: changer le nom en ebirds.db ? --> attention impact du code Front
GV_DBNAME = '/var/www/nichoir.db'    #DB pour les donnees accedees depuis le Front
GV_DBNAME2 = '/var/www/captir.db'    #DB pour les donnees accedess uniqument depuis le Back

gv_seq_num = 0
gv_date = '1970-01-01'
gv_id = 0

###############################################################################
# Definitions des classes
###############################################################################

# Classe utilisee pour les capteurs d'entrees/sorties IR
# L'utilisation de Threads permet une meilleur gestion de l'interrupt,
# sans pause de celui-ci pdt le bouncetime lors de detection
class handler(threading.Thread):
    def __init__(self, pin, func, edge='both', bouncetime=200):
        super(handler, self).__init__()
        super(handler, self).setDaemon(True)

        self.edge = edge
        self.func = func
        self.pin = pin
        self.bouncetime = float(bouncetime)/1000

        self.lastpinval = GPIO.input(self.pin)
        self.lock = threading.Lock()

    def __call__(self, *args):
        if not self.lock.acquire():
            return

        t = threading.Timer(self.bouncetime, self.read, args=args)
        t.start()
        ##print("creation Thread # : ".format(threading.get_ident()))
        #print("creation Thread # : ".format(t.currentThread()))

    def read(self, *args):
        pinval = GPIO.input(self.pin)

        if (
                ((pinval == 0 and self.lastpinval == 1) and
                 (self.edge in ['falling', 'both'])) or
                ((pinval == 1 and self.lastpinval == 0) and
                 (self.edge in ['rising', 'both']))
        ):
            self.func(*args)

        self.lastpinval = pinval
        self.lock.release()

###############################################################################
###############################################################################
###                             Fonctions (def)                             ###
###############################################################################
###############################################################################

def cleanAndExit():
    print ("Cleaning...")
    GPIO.cleanup()
    print ("Bye!")
    sys.exit()

### Creation des bases de donnees si non existantes
def create_main_DB():
    lv_conn = sqlite3.connect(GV_DBNAME)
    c = lv_conn.cursor()

    # Table InOut_IR - analyse des entrees/sorties IR
    c.execute('''CREATE TABLE IF NOT EXISTS InOut_IR \
        (FDatim DATETIME DEFAULT CURRENT_TIMESTAMP, FStatus TEXT,FTime LONG)''')

    #TODO: change types to numbers instead of text (doesn't chzange anything for the front)
    # Table meteo - Temperature et humidite interieur et exterieur
    c.execute('''CREATE TABLE IF NOT EXISTS meteo \
        (dateHeure DATETIME DEFAULT CURRENT_TIMESTAMP, \
        tempExt TEXT,humExt TEXT,tempInt TEXT,humInt TEXT)''')

    # Table poids - Mesure du poids du nichoir
    c.execute('''CREATE TABLE IF NOT EXISTS balance \
        (dateHeure DATETIME DEFAULT CURRENT_TIMESTAMP, \
        poidsNich TEXT)''')

    # Fermeture et sauvegarde
    lv_conn.commit()
    lv_conn.close()

### Creation des bases de donnees si non existantes
def create_back_DB():
    lv_conn2 = sqlite3.connect(GV_DBNAME2)
    c2 = lv_conn2.cursor()

    # Table Capt_IR - donnees brutes des capteurs IR
    c2.execute('''CREATE TABLE IF NOT EXISTS Capt_IR \
        (FDatim DATETIME DEFAULT CURRENT_TIMESTAMP, FConnector TEXT, \
         FStatus TEXT, FTime LONG, FTreated INTEGER DEFAULT 0, \
         FID_Pair LONG)''')

    # Fermeture et sauvegarde
    lv_conn2.commit()
    lv_conn2.close()


### Creation d'un ID unique incremental a partir de la date ('2017-04-27 14:05:02.628289')
#TODO id uniquement pour capteurs IR ? --> A renommer
def get_next_id(lv_id):
    ### TODO certainement moyen de recuperer juste la date dans le bon format directement
    ### TODO -> a revoir pour recuperer l'ID max en DB (utile en cas de redemarrage du pgm)
    ### pour l'instant a chaque redemarrage on repars à zero..
    ### TODO essayer aussi de mettre des 0 (longueur identique qq soit l'incrementation)
    #Definition des variables globales utilisees par le thread concurrent
    global gv_seq_num
    global gv_date
    lv_datime = datetime.now()    # initialisation date et heure
    #print("gv_date IN = {0}".format(gv_date))
    #print("lv_datime = {0}".format(lv_datime))
    if gv_date != str(lv_datime)[0:10]:
        #print ("Changement de date, reset du numero de sequence")
        gv_seq_num = 0
        #print ("{0} - {1} - {2}".format(str(lv_datime)[0:4],str(lv_datime)[5:7],str(lv_datime)[8:10]))
        lv_id = int(str(lv_datime)[0:4] + str(lv_datime)[5:7] + \
            str(lv_datime)[8:10] + str(gv_seq_num))
        gv_date = str(lv_datime)[0:10]
        #print("gv_date OUT = {0}".format(gv_date))
    else:
        #print ("Meme date, seq_num++")
        gv_seq_num += 1
        lv_id = int(str(lv_id)[0:8] + str(gv_seq_num))
        #        lv_date = str(lv_datime)[0:9]        # Date format yyyy-mm-dd
        #        lv_time = str(lv_datime)[11:22]      # temps coupe au 1/1000 s
        #lv_count += 1
    print ("lv_id = {0}".format(lv_id))
    return lv_id

def IR_detection(channel):
    #TODO: move in proper library and set pin as param/config
    if (int(channel)==16):
        IRCapt = "2"
        IRStat = GPIO.input(16)
        print ("Capteur {0} (IN) - Status {1}".format(IRCapt,IRStat))
    else:
        IRCapt = "1"
        IRStat = GPIO.input(20)
        print ("Capteur {0} (OUT) - Status {1}".format(IRCapt,IRStat))

    #Get number of milliseconds since Epoch (1/1/1970 00:00:00 GMT)
    lv_millis=int(time.time()*1000)

    # re-ouverture de la DBIR et acquisition du curseur
    # (le commit se fait en fin d'iteration)
    lv_conn2 = sqlite3.connect(GV_DBNAME2)
    c2 = lv_conn2.cursor()

    #Get number of milliseconds since Epoch (1/1/1970 00:00:00 GMT)
    lv_millis=int(time.time()*1000)
    #lv_date = "2018-10-05"
    #Definition des variables globales utilisees par le thread concurrent
    global gv_id
    gv_id = get_next_id(gv_id)
    # Ecriture des infos des capteurs IR
    dataTble = [(IRCapt,IRStat,lv_millis,gv_id)]
    logger.debug('Write in Capt_IR DB - %s', dataTble)
    print ("capteur IR I/O => DB")
    c2.executemany('''INSERT INTO Capt_IR (FConnector,FStatus,FTime,FID_Pair)
                     VALUES (?,?,?,?)''', dataTble)

    lv_conn2.commit()
    lv_conn2.close()
#    IR_eval(lv_dataF + str(lv_dataLen))



def IR_eval(lv_recSTR):

    # Test des series d'interruptions
    #TODO !!! ajouter le test temporel

        # Concatenation de la paire precedente avec donnees courantes
        # pour rechercher l'existance du couple dans le dictionnaire
#TODO: a deplacer --> completement en dehors ? (trigger = front ?) ou autre ?
#TODO: utiliser des noms de variables plus parlalantes..
#TODO: A DISCUTER ! ici on se base sur une lecture sequentielle des evenements
#      hors avec les interrupts ce n'est sans doute pas 100% certain
#      De nouveau, il vaut sans doute mieux separer ce process dans un pgm a part
#      et se baser sur une lecture en DB en fonction des millisecondes depuis Epoch
#    TODO: log de toutes les 'erreur' (non matching) en DB pour post-analyse (à discuter..)
#    D'abord tester avec une variable globale de sequence et puis sinon lecture DB ??
        global lv_foundPair
        lv_tmpPaire = ""
        lv_recSTR = lv_precSTR = \
        lv_recSTR[lv_recSTR.find('<') + 1:lv_recSTR.find('>')]
        lv_splitSTR = lv_recSTR.split(',')
        # Concatenation de la paire precedente avec donnees courantes
        # pour rechercher l'existance du couple dans le dictionnaire
        lv_tmpPaire = (str(lv_paire[0]) + \
                    (str(lv_splitSTR[1]) + str(lv_splitSTR[2])))
        logger.debug('Testing IR Paires - %s', lv_tmpPaire)
        if (lv_tmpPaire in lv_paireDico):
            #logger.debug('Matching paires, testing IR series - %s', lv_tmpDico)
            logger.debug('Matching paires, testing IR series ')
            lv_foundPair = True
            lv_tmpDico = (str(lv_serie[0]) + str(lv_paireDico.get(lv_tmpPaire)))
            if (lv_tmpDico in lv_serieDico):
                # Serie correspondante a un mouvement, stockage dans la table
                # des mouvements et reinitialisation des valeurs
                logger.debug('Matching Serie - %s', lv_tmpDico)
                lv_conn = sqlite3.connect(GV_DBNAME)
                c = lv_conn.cursor()

                dataTble = [(lv_serieDico.get(lv_tmpDico) , lv_splitSTR[3])]
                c.executemany('''INSERT INTO InOut_IR (FStatus,FTime)
                                VALUES (?,?)''', dataTble)
                lv_conn.commit()
                lv_conn.close()
                lv_recStr = ''

                lv_serie[0] = lv_serie[1] = ''
                lv_paire[0] = lv_paire[1] = ''
            else:
                # Serie non correspondante a un mouvement, decallage des tables
                # de valeurs pour la prochaine iteration
                logger.debug('Non matching Serie - %s', lv_tmpDico)
                lv_serie[0] = str(lv_paireDico.get(lv_tmpPaire))
                lv_paire[0] = str(lv_splitSTR[1]) + str(lv_splitSTR[2])
                lv_serie[1] = lv_paire[1] = str(lv_splitSTR[3])
        else:
            logger.debug('Non matching Paires - %s',lv_tmpPaire)
            # Si deux paires successives ne sont pas reconnues, reinitialisation
            # des de la table des series
            #NFA: pourquoi pas directement à la première non matching pair ?
            if not lv_foundPair:
                lv_serie[0] = lv_serie[1] = ''
            lv_paire[0] = str(lv_splitSTR[1]) + str(lv_splitSTR[2])
            lv_foundPair = False


###############################################################################
###############################################################################
###                       Initialisation du programme                       ###
###############################################################################
###############################################################################

logger.warning('begin program eBirds...')

### Declaration des variables locales
#TODO a reviewer et rationaliser (noms var)
#NFA: quelle est la portee des lv_* par rapport aux defs?
# --> où déclarer les lv utilisees uniquement dans les defs?
lv_precSTR = str()
lv_recSTR = str()
# var pour capteurs I/O
lv_tmpPaire = str()
lv_tmpDico = str()
lv_count = int()
lv_countIter = int()
lv_foundPair = False
# Voir aussi les commentaires en bas de page
# Chaque paire donne l'etat d'une séquence des capteurs IR
# ex: "1020" signifie capteur 1 coupé suivi de capteur 2 coupé
# ex: "1121" signifie capteur 1 allumé suivi de capteur 2 allumé
# (capteur 1 -> exterieur)
lv_paireDico = {"1020": 11, "1121": 12, "2010": 21, "2111": 22, "1011": 31,
                "2021": 32}
# Ensuite chaque couple/doublet est associé (concaténé) pour donner une serie (quadruplet)
# La serie est ensuite annalysée pour déterminer le type d'événement
# ex: "1112" correpond au doublon "capteur 1 éteint suivi de capteur 2 éteint" lui même suivi de "capteur 1 allumé suivi de capteur 2 allumé"
#     ceci correspond donc à l'événement "E1" qui indique une entrée de type 1
# "E" pour entrée, "S" pour sortie, "V" pour visite (coupure des capteurs sans pour autant rentrer)
lv_serieDico = {"1112": 'E1', "2122": 'S1', "3132": 'E2', "3231": 'S2',
                "1122" : 'V1', "1221" : 'V2'}
lv_paire = [0,0,0]    # stockage de FStatus, Datime, ID
lv_serie = ["s",0,0,0]
lv_originTime = datetime.now()
lv_originMicroSec = int()
lv_countIter = 0

### Verification de la BD et de la connection serie
create_main_DB()    # appel de la procedure pour creer la BD principale
create_back_DB()    # appel de la procedure pour creer la BD utilisee uniquement par le Back



###############################################################################
###############################################################################
###                             Corps du programme                          ###
###############################################################################
###############################################################################


#TODO a virer ?
### Initialisations
#lv_date = "2018-10-04"
#lv_count = 0


###############################################################################
### Creation de Thread pour gestion Interrupt pour les capteurs IR d'entrees/sorties

try:
   print ("Lecture capteur IR")
   #Thread pour capteur 1
   capt1 = handler(16, IR_detection, bouncetime=1)
   capt1.start()
   GPIO.add_event_detect(16, GPIO.BOTH, callback=capt1)

   #Thread pour capteur 2
   capt2 = handler(20, IR_detection, bouncetime=1)
   capt2.start()
   GPIO.add_event_detect(20, GPIO.BOTH, callback=capt2)


   #GPIO.add_event_detect(16, GPIO.BOTH, callback=detection, bouncetime=300)
   #GPIO.add_event_detect(20, GPIO.BOTH, callback=detection, bouncetime=300)

except (KeyboardInterrupt, SystemExit):
   cleanAndExit()

###############################################################################
###############################################################################
### Boucle infinie (mesure des autres capteurs et infinité du programme)
### Note: l'iteration du programme est interrompue (pause) a tout moment par les
### interrupts definis ci-dessus pour la gestion des capteurs IR In/Out

### Boucle de test des capteur I/O IR (à distance) en mode bouchon
### TODO enlever le mode bouchon


while True:

# initialisation du temps de reference
# TODO -> a revoir -> toujours utilisé ? Et dans le cas de tous les capteurs ?
#                     et a faire a chaque iteration ??
    if not (lv_originTime):
        lv_originTime = lv_datime
        lv_originMicroSec = int(lv_splitSTR[3])*1000


###############################################################################
### lecture des capteurs d'entree/sortie

## String de valeurs pour Entree/Sortie :
##    "S"; 1C - capteur ; 1C - Status Ouvert/ferme ;  xC - Millisecondes
##

    try:
        lv_countIter = lv_countIter +1
        print (lv_countIter)
        #TODO virer les print
        print ("")
        print ("Lecture capteurs IR entrees/sorties")
        if (lv_simu == "Y"):
            IRCapt = random.randint(1,2) # 1 ou 2
            IRStat = random.randint(0,1) # 0 ou 1
        else:
            #TODO to remove --> mettre write DB en def ?  ou utiliser IR_detection avec simu en param plutot
            # fait par la fonction callback IR_detection
            print ("Capteurs I/O TODO")
            IRCapt = 0
            IRStat = 0
        print ("Capteur {0} - Status {1}".format(IRCapt,IRStat))

        # re-ouverture de la DBIR et acquisition du curseur
        # (le commit se fait en fin d'iteration)
        lv_conn2 = sqlite3.connect(GV_DBNAME2)
        c2 = lv_conn2.cursor()

        #Get number of milliseconds since Epoch (1/1/1970 00:00:00 GMT)
        lv_millis=int(time.time()*1000)
        #lv_date = "2018-10-05"
        #Definition des variables globales utilisees par le thread concurrent
        global gv_id
        gv_id = get_next_id(gv_id)
        # Ecriture des infos des capteurs IR
        dataTble = [(IRCapt,IRStat,lv_millis,gv_id)]
        logger.debug('Write in Capt_IR DB - %s', dataTble)
        print ("capteur IR I/O => DB")
        c2.executemany('''INSERT INTO Capt_IR (FConnector,FStatus,FTime,FID_Pair)
                         VALUES (?,?,?,?)''', dataTble)

        lv_conn2.commit()
        lv_conn2.close()

        # Evalation des paires IR I/O toutes les x iterations
        #if (lv_countIter % 1 == 0):
        #    IR_eval(lv_dataF)
#        IR_eval(lv_dataF)

    except (KeyboardInterrupt, SystemExit):
        cleanAndExit()


# re-ouverture de la DB et acquisition du curseur
# (le commit se fait en fin d'iteration)
    lv_conn = sqlite3.connect(GV_DBNAME)
    c = lv_conn.cursor()

###############################################################################
### Capteur(s) Meteo (Temp/Hum)

    try:
       #TODO virer les print
       print ("")
       print ("Lecture capteur Meteo")
       # result est retourné dans le format (tempExt,humExt,tempInt,humInt)
       result = AdafruitDHT.getTempHum(lv_simu, logger) #retour fonction capteur meteo
       print (result)
       # Ecriture des infos des capteurs Meteo
       #TODO: changer la methode ? --> insert DB uniquement si different ? Cmt faire niveau front pour l'affichage des donnees alors ? (graphiques)
       logger.debug('Write in meteo DB - %s', [result])
       print ("capteur Meteo => DB")
       #dataTble = [(tempExt,humExt,tempInt,humInt)]
       c.executemany('''INSERT INTO meteo (tempExt,humExt,tempInt,humInt)
                       VALUES (?,?,?,?)''',[result])

    except (KeyboardInterrupt, SystemExit):
       cleanAndExit()


###############################################################################
### Capteur Poids

    try:
       #TODO virer les print
       print ("")
       print ("Lecture capteur Poids")
       # result est retourné dans le format (poidsNich)
       result = balance.getPoids(lv_simu) #retour fonction capteur poids
       print (result)
       # Ecriture des infos des capteurs Meteo
       #TODO: changer la methode ? --> insert DB uniquement si different ? Cmt faire niveau front pour l'affichage des donnees alors ? (graphiques)
       logger.debug('Write in Balance DB - %s', [result])
       print ("capteur Poids => DB")
       #VALUES (?) only takes string as parameter
       #and it must be an array, hence giving the string in a tuple
       #otherwise it will consider each letter of the string as an array entry..
       c.execute('INSERT INTO balance (poidsNich) VALUES (?)', [str(result),])

    except (KeyboardInterrupt, SystemExit):
       cleanAndExit()


###############################################################################
# Fin de traitement des infos des capteurs

    # commit et sauvegarde DB
    lv_conn.commit()
    lv_conn.close()

    #TODO utiliser une variable de conf pour le sleep time
    print("")
    print("")
    print ("sleep 5 secondes")
    time.sleep(5)
    print("")
    print("")


##
## Ajouter champs test pour identifier le traitemnt d'une ligne
##     -> 0: non traité - (-1): événement parasite - 2: événement reconnu
## Ajouter l'ID du traitement dans l'autre table
##
## Repérer les séquences
##
## 1Off-2Off-1ON-2ON    10, 20, 11, 21    -> Serie 1    Entréee
## 2Off-1Off-2ON-1ON    20, 10, 21, 11    -> Serie 2    Sortie
## 1Off-1On-2Off-2ON    10, 11, 20, 21    -> Serie 3    Entrée bis
## 2Off-2On-1Off-1ON    20, 21, 10, 11    -> Serie 4    Sortie bis
## Chercher 10-20 (entree) ou 20-10 (sortie) sur un intervalle de temps max (1s)
## chercher le binome suivant 11-21 ou 21-11 qui peut suivre ou être plus loin
## Verifier l'intervalle de temps entre les 2 binomes et intrabinome
##
## lv_IRprev : Variable contenant la précédante valeur des capteurs IR
## lv_IRnum : Flag True/false pour savoir si il faut rechercher un couple
##             et si possible l'ID précédant dans la table dans la table'
##     -> initialisé à zéro au début et à chaque couple qui match
## lv_IRpair : numéro de la paire précédante pour tester la suite de 4
##
## Si lv_IRnum[1] est true:
##     comparaison des 2 derniers événement pour trouver une paire
##        !!! ## délais entre les événements à tester
##     si match :
##        initialisation des deux champs avec le numéro correspondant à la
##            paire : de 1 à 4
##        si lv_IRpair[1] != 0
##            Comparaison avec la paire précédante
##            si match :
##                Initialisation de la table à 10 des deux couples
##                Nouvelle ligne dans la table des mouvements
##                Initialisation de lv_IRpair à 0
##            sinon
##                Initialisation de la table à -1 du couple précédant
##                Nouvelle ligne dans la table des mouvements
##                Initialisation de lv_IRpair avec le numéro de la paire et
##                    son ID
