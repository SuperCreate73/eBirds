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
import AdafruitDHT  # import du capteur temp/humidite DHT11
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
logging.basicConfig(filename='/home/pi/ebirds/ebirds.log',filemode='a',
                    format='%(levelname)s:%(asctime)s-%(message)s')
logger=logging.getLogger('LoggingEbirds')

## Analyse des options
## Par defaut, le mode de logging est INFO et pas de simulation des capteurs
logger.setLevel('INFO')
gv_simu='N'
## Il est mis a DEBUG si l'option verbose est specifiee.
#TODO ameliorer le message si l(es) argument(s) ne sont pas corrects
if (len(sys.argv) > 1):
    if (sys.argv[1] == "-v" or sys.argv[1] == "-verbose"):
        logger.setLevel('DEBUG')
        logger.info('Verbose mode (debug) activated')
    else:
        logger.warning('Unknow parameter : %s' + sys.argv[1])
    if (len(sys.argv) > 2):
        if (sys.argv[2] == "-s" or sys.argv[2] == "-simu"):
            gv_simu = 'Y'
            logger.info('Simulation mode activated')
        else:
            logger.warning('Unknow parameter : %s' + sys.argv[2])

#Travailler dans le repertoire 'eBirds'
#TODO rendre le chemin relatif (fonction du user qui ne sera pas specialement 'pi')
# 2 DB necessaires car sqlite ne gere pas les acces concurrents (DB = fichier)
# hors l'utilisation de Threads implique des acces DB concurrents
global GV_DBNAME
global GV_DBNAME2
#TODO: changer le nom en ebirds.db ? --> attention impact du code Front
GV_DBNAME = '/home/pi/ebirds/nichoir.db'    #DB pour les donnees accedees depuis le Front
GV_DBNAME2 = '/home/pi/ebirds/captir.db'    #DB pour les donnees accedess uniqument depuis le Back

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
        #logger.debug("creation Thread # : {0}".format(threading.get_ident()))
        #logger.debug("creation Thread # : {0}".format(t.currentThread()))

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
    logger.debug("Clean and Exit")
    GPIO.cleanup()
    logger.debug("Thank you, Goodbye!")
    sys.exit()


### Creation des bases de donnees si non existantes
def create_main_DB():
    lv_conn = sqlite3.connect(GV_DBNAME)
    c = lv_conn.cursor()

    # Table InOut_IR - analyse des entrees/sorties IR
    c.execute('''CREATE TABLE IF NOT EXISTS InOut_IR \
        (FDatim DATETIME DEFAULT CURRENT_TIMESTAMP, FStatus TEXT,FTime LONG)''')

    #TODO: change types to numbers instead of text (doesn't change anything for the front)
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
    ### TODO -> a revoir pour recuperer l'ID max en DB (utile en cas de redemarrage du pgm)
    ### pour l'instant a chaque redemarrage on repars à zero..

    #The3 options give the same result (i.e. today in format YYYYMMDD)
    #print time.strftime('%Y%m%d')
    #today = datetime.today()
    #print today.strftime('%Y%m%d')
    #todaynow = datetime.now()   
    #print todaynow.strftime('%Y%m%d')

    #Definition des variables globales utilisees par le thread concurrent
    global gv_seq_num 
    global gv_date
    lv_id = 0
    lv_datime = time.strftime('%Y%m%d')    # initialisation date et heure
    #lv_datime = datetime.now()    # initialisation date et heure
    #print("gv_date IN = {0}".format(gv_date))
    #print("lv_datime = {0}".format(lv_datime))
    #TODO a priori, plus besoin de prendre les 10 caractères ici..
    print(gv_date)
    print(str(lv_datime))
    if gv_date != str(lv_datime)[0:10]:
        logger.info("Changement de date, nous sommes le {0}".format(lv_datime))
        gv_seq_num = 0
        #print ("{0} - {1} - {2}".format(str(lv_datime)[0:4],str(lv_datime)[5:7],str(lv_datime)[8:10]))
        gv_date = lv_datime
        #print("gv_date OUT = {0}".format(gv_date))
    else:
        #print ("Meme date, seq_num++")
        gv_seq_num += 1
    lv_id = int(lv_datime) * 1000000
    lv_id = lv_id + int(gv_seq_num)
    logger.debug("nombre sequentiel pour capteurs IR I/O = {0}".format(lv_id))
    return lv_id


def IR_interrupt(channel):
    #TODO: move in proper library and set pin as param/config
    #TODO: should be in a try/exception
    #Get number of milliseconds since Epoch (1/1/1970 00:00:00 GMT)
    IRmillis=int(time.time()*1000)    
    logger.debug("Lecture capteurs IR entrees/sorties")
    if (int(channel)==16):
	IRCapt = "2"
	IRStat = GPIO.input(16)
        logger.debug("Capteur {0} (IN) - Status {1}".format(IRCapt,IRStat))
    else:
	IRCapt = "1"
	IRStat = GPIO.input(20)
        logger.debug("Capteur {0} (OUT) - Status {1}".format(IRCapt,IRStat))
    IR_write(IRCapt,IRStat,IRmillis)
    IR_eval(str(IRCapt)+str(IRStat),IRmillis)


def IR_write(wCapt,wStat,wMillis):
    #TODO: should be in a try/exception

    # re-ouverture de la DBIR et acquisition du curseur
    # (le commit se fait en fin d'iteration)
    lv_connIR = sqlite3.connect(GV_DBNAME2)
    cIR = lv_connIR.cursor()

    #Definition des variables globales utilisees par le thread concurrent
    global gv_id
    gv_id = get_next_id(gv_id)
    # Ecriture des infos des capteurs IR
    dataTble = [(wCapt,wStat,wMillis,gv_id)]
    logger.debug('Write in Capt_IR DB - %s', dataTble)
    cIR.executemany('''INSERT INTO Capt_IR (FConnector,FStatus,FTime,FID_Pair)
                     VALUES (?,?,?,?)''', dataTble)

    lv_connIR.commit()
    lv_connIR.close()


def IR_eval(lv_IRcur,lv_IRmillis):
    # Test des series d'interruptions

## Repérer les séquences
##
## 1Off-2Off-1ON-2ON    10, 20, 11, 21    -> Serie 1    Entréee
## 2Off-1Off-2ON-1ON    20, 10, 21, 11    -> Serie 2    Sortie
## 1Off-1On-2Off-2ON    10, 11, 20, 21    -> Serie 3    Entrée bis
## 2Off-2On-1Off-1ON    20, 21, 10, 11    -> Serie 4    Sortie bis


##TODO: a deplacer ? --> completement en dehors ? (trigger = front ?) ou autre ?
##TODO: A DISCUTER ! ici on se base sur une lecture sequentielle des evenements
##      hors avec les interrupts ce n'est sans doute pas 100% certain
##      De nouveau, il vaut sans doute mieux separer ce process dans un pgm a part
##      et se baser sur une lecture en DB en fonction des millisecondes depuis Epoch
## TODO: log de toutes les 'erreur' (non matching) en DB pour post-analyse (à discuter..)
## D'abord tester avec une variable globale de sequence et puis sinon lecture DB ??
## Ajouter champs test pour identifier le traitemnt d'une ligne
##     -> 0: non traité - (-1): événement parasite - 2: événement reconnu
## TODO
## Ajouter l'ID du traitement dans l'autre table
##
##TODO !ajouter le test temporel
## Chercher 10-20 (entree) ou 20-10 (sortie) sur un intervalle de temps max (1s)
## chercher le binome suivant 11-21 ou 21-11 qui peut suivre ou être plus loin afin de valider l'entree ou sortie
## Verifier l'intervalle de temps entre les 2 binomes et intrabinome
##
## lv_IRprev : Variable contenant la précédante valeur des capteurs IR
## lv_IRpair : numéro de la paire précédante pour tester la suite de 4
#  TODO commentaires suivants obsoletes mais utile pour une revision
## lv_IRnum : Flag True/false pour savoir si il faut rechercher un couple
##             et si possible l'ID précédant dans la table dans la table'
##     -> initialisé à zéro au début et à chaque couple qui match
##
## Si lv_IRnum[1] est true:
##     comparaison des 2 derniers événement pour trouver une paire
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


        #TODO enlever les prints
        print(lv_IRcur)
        logger.debug("lv_IRcur = {0}".format(lv_IRcur))
        logger.debug("gv_IRprec = {0}".format(gv_IRprec))
        print str(gv_IRprec) + lv_IRcur

        #TODO pourquoi global ? Pcq sinon on a l'erreur 'used before assignement --> comment eviter ?
        #TODO si on utilise une var sur plusieurs iteration d'une def on doit d'office utiliser une global ? 
        #TODO a noter qu'on a pas ce probleme si on utilise un tableau initialisé au debut du corps du pgm..
        global gv_foundPaire
        global gv_IRprec
        global gv_pairePrec
        lv_tmpPaire = ""
        # Concatenation de la lecture capteur precedente avec donnees courantes 
        # pour rechercher l'existance de la paire dans le dictionnaire
        # gv_IRprec contient le capteur déclenché précédent et la lecture de son statut (10,11,20,21)
        lv_tmpPaire = (str(gv_IRprec) + (str(lv_IRcur)))
        logger.debug('Testing IR Paire - %s', lv_tmpPaire)
        # Pas de test de la paire si l'interrupt precedent fait deja partie d'une matching paire
        #TODO --> pas suffisament generique car si finalament pas de serie trouvée avec la matching paire suivante, on risque de louper un cas
        if ((not gv_foundPaire) and (lv_tmpPaire in lv_paireDico)):
            logger.debug('Matching Paire - %s', lv_tmpPaire)
            gv_foundPaire = True
            # Concatenation de la matching paire precedente avec la matching paire courante 
            # pour rechercher l'existance de la serie dans le dictionnaire
            # gv_pairePrec contient l'entree dans le dico des Paires de la matching paire trouvee precedemment
            lv_tmpSerie = (str(gv_pairePrec) + str(lv_paireDico.get(lv_tmpPaire)))
            logger.debug('Testing IR Serie - %s', lv_tmpSerie)
            if (lv_tmpSerie in lv_serieDico):
                # Serie correspondante a un mouvement, stockage dans la table
                # des mouvements et reinitialisation des valeurs
                logger.debug('Matching Serie (mvt found) - %s', lv_tmpSerie)
                lv_connMvt = sqlite3.connect(GV_DBNAME)
                cMvt = lv_connMvt.cursor()

                # Ecriture du resultat d'analyse des capteurs IR
                dataTble = [(lv_serieDico.get(lv_tmpSerie) , lv_IRmillis)]
                logger.debug('Write in InOut_IR DB - %s', dataTble)
                cMvt.executemany('''INSERT INTO InOut_IR (FStatus,FTime)
                                VALUES (?,?)''', dataTble)
                lv_connMvt.commit()
                lv_connMvt.close()

                # Re-initialisation de toutes les valeurs
                gv_IRprec = IR_cur = ''
                gv_pairePrec = ''
                gv_foundPaire = False
            else:
                # Serie non correspondante a un mouvement/evenement, 
                # on ecrase et on stocke les valeurs courantes pour la prochaine iteration
                logger.debug('Non matching Serie (mvt not found) - %s', lv_tmpSerie)
                gv_pairePrec = str(lv_paireDico.get(lv_tmpPaire))
                gv_IRprec = str(lv_IRcur)
        else:
            logger.debug('Non matching Paires (serie not found or not checked) - %s',lv_tmpPaire)
            # Si deux paires successives ne sont pas reconnues, reinitialisation de la table des series
            # On ne le fait pas des la premiere non matching paire car entre 2 matching series il y a (presque) toujours une non-matching paire
            # (et il faut 2 matching Series pour avoir un Mouvement)
            if not gv_foundPaire:
                logger.debug('Reinitialisation de la table des series')
                gv_pairePrec = ''
            #gv_IRprec = str(lv_splitSTR[1]) + str(lv_splitSTR[2])
            gv_IRprec = str(lv_IRcur)
            gv_foundPaire = False


###############################################################################
###############################################################################
###                       Initialisation du programme                       ###
###############################################################################
###############################################################################

logger.warning('begin program eBirds...')

### Declaration des variables locales 
#TODO a reviewer et rationaliser (noms var, var utilisees ou pas..)
#TODO quelle est la portee des lv_* par rapport aux defs? Apparemment OK si pas redéclaré dans le def
# --> où déclarer les lv utilisees uniquement dans les defs? Vraiment ici ?? ou plutôt dans le def IR_eval ?
gv_IRprec = str()
lv_IRcur = str()
# var pour capteurs I/O
lv_tmpPaire = str()
lv_tmpSerie = str()
lv_count = int()
lv_countIter = int()
gv_foundPaire = False
# Voir aussi les commentaires en bas de page
# Chaque paire donne l'etat d'une séquence des capteurs IR
# ex: "1020" signifie capteur 1 coupé suivi de capteur 2 coupé
# ex: "1121" signifie capteur 1 allumé suivi de capteur 2 allumé
# (capteur 1 -> exterieur)
lv_paireDico = {"1020": 11, "1121": 12, "2010": 21, "2111": 22, "1011": 31,
                "2021": 32}    
# Ensuite chaque couple/doublet est associé (concaténé) pour donner une serie (quadruplet)
# La serie est ensuite analysée pour déterminer le type d'événement
# ex: "1112" correpond au doublon "capteur 1 éteint suivi de capteur 2 éteint" lui même suivi de "capteur 1 allumé suivi de capteur 2 allumé"
#     ceci correspond donc à l'événement "E1" qui indique une entrée de type 1
# "E" pour entrée, "S" pour sortie, "V" pour visite (coupure des capteurs sans pour autant rentrer)
lv_serieDico = {"1112": 'E1', "2122": 'S1', "3132": 'E2', "3231": 'S2',
                "1122" : 'V1', "1221" : 'V2'}
gv_IRprec = '00'
gv_pairePrec = '00'
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
   logger.debug("Demarrage des Thread pour interrupts sur capteurs IR d'entrees/sorties")
   #Thread pour capteur 1
   capt1 = handler(16, IR_interrupt, bouncetime=1)
   capt1.start()
   GPIO.add_event_detect(16, GPIO.BOTH, callback=capt1)
   
   #Thread pour capteur 2
   capt2 = handler(20, IR_interrupt, bouncetime=1)
   capt2.start()
   GPIO.add_event_detect(20, GPIO.BOTH, callback=capt2)

   
   #GPIO.add_event_detect(16, GPIO.BOTH, callback=IR_interrupt, bouncetime=300)
   #GPIO.add_event_detect(20, GPIO.BOTH, callback=IR_interrupt, bouncetime=300)

except (KeyboardInterrupt, SystemExit):
   #logging.error('Erreur lors de la gestion des interrupts pour capteurs I/O')
   logging.exception('Erreur lors de la gestion des interrupts pour capteurs I/O')
   #logging.critical('Erreur lors de la gestion des interrupts pour capteurs I/O')
   cleanAndExit()

###############################################################################
###############################################################################
### Boucle infinie (mesure des autres capteurs et infinité du programme)
### Note: l'iteration du programme est interrompue (pause) a tout moment par les
### interrupts definis ci-dessus pour la gestion des capteurs IR In/Out

### Boucle de test des capteur I/O IR (à distance) en mode bouchon

logger.debug("Demarrage boucle infinie pour lecture des differents capteurs")
while True:

# initialisation du temps de reference
# TODO -> a revoir -> toujours utilisé ? Et dans le cas de tous les capteurs ?
#                     et a faire a chaque iteration ??
    if not (lv_originTime):
        lv_originTime = lv_datime
        lv_originMicroSec = int(lv_splitSTR[3])*1000


###############################################################################
### evaluation des capteurs d'entree/sortie

    try:
        #TODO jusqu'ou peut aller lv_counter? (int) -> eviter le plantage a cause de unbound
        lv_countIter = lv_countIter +1
        print (lv_countIter)
        logger.debug("Iteration boucle courante={0}".format(lv_countIter))
        if (gv_simu == "Y"):
            logger.debug("Simulation capteurs IR entrees/sorties")
            IRCapt = random.randint(1,2) # 1 ou 2
            IRStat = random.randint(0,1) # 0 ou 1
            IR_write(IRCapt,IRStat,int(time.time()*1000))

        # Evalation des paires IR I/O toutes les x iterations
        #TODO a remettre uniquement si on fait une eval basee sur une lecture DB de Capt_IR et non interrupts sequentiels 
        #if (lv_countIter % 97 == 0):
        #    IR_eval(lv_dataF)
        IR_eval("10",int(time.time()*1000))
        IR_eval("20",int(time.time()*1000))
        IR_eval("11",int(time.time()*1000))
        IR_eval("21",int(time.time()*1000))

        IR_eval("20",int(time.time()*1000))
        IR_eval("10",int(time.time()*1000))
        IR_eval("21",int(time.time()*1000))
        IR_eval("11",int(time.time()*1000))

        IR_eval("10",int(time.time()*1000))
        IR_eval("11",int(time.time()*1000))
        IR_eval("20",int(time.time()*1000))
        IR_eval("21",int(time.time()*1000))

        IR_eval("20",int(time.time()*1000))
        IR_eval("21",int(time.time()*1000))
        IR_eval("10",int(time.time()*1000))
        IR_eval("11",int(time.time()*1000))

        IR_eval("10",int(time.time()*1000))
        IR_eval("20",int(time.time()*1000))
        IR_eval("21",int(time.time()*1000))
        IR_eval("11",int(time.time()*1000))

        IR_eval("11",int(time.time()*1000))
        IR_eval("21",int(time.time()*1000))
        IR_eval("20",int(time.time()*1000))
        IR_eval("10",int(time.time()*1000))

        cleanAndExit()

    except (KeyboardInterrupt, SystemExit):
        cleanAndExit()


# re-ouverture de la DB et acquisition du curseur
# (le commit se fait en fin d'iteration)
    lv_conn = sqlite3.connect(GV_DBNAME)
    c = lv_conn.cursor()

###############################################################################
### Capteur(s) Meteo (Temp/Hum)

    try:
       logger.debug("Lecture capteur Meteo")
       # result est retourné dans le format (tempExt,humExt,tempInt,humInt)
       result = AdafruitDHT.getTempHum(gv_simu,logger) #retour fonction capteur meteo
       if result is None:
          logger.warning("Capteur meteo disfonctionnel (pas de données)")
       else:
          # Ecriture des infos des capteurs Meteo
          #TODO: changer la methode ? --> insert DB uniquement si different ? Cmt faire niveau front pour l'affichage des donnees alors ? (graphiques)
          logger.debug('Write in meteo DB - %s', [result])
          #dataTble = [(tempExt,humExt,tempInt,humInt)]
          c.executemany('''INSERT INTO meteo (tempExt,humExt,tempInt,humInt)
                           VALUES (?,?,?,?)''',[result])

    except (KeyboardInterrupt, SystemExit):
       logger.critical("Erreur d'ecriture dans la table METEO - %s", [result])
       cleanAndExit()


###############################################################################
### Capteur Poids

    try:
       logger.debug("Lecture capteur Poids")
       # result est retourné dans le format (poidsNich)
       result = balance.getPoids(gv_simu,logger) #retour fonction capteur poids 
       if result is None:
          logger.warning("Capteur meteo disfonctionnel (pas de données)")
       else:
          # Ecriture des infos des capteurs Meteo
          #TODO: changer la methode ? --> insert DB uniquement si different ? Cmt faire niveau front pour l'affichage des donnees alors ? (graphiques)
          logger.debug('Write in Balance DB - %s', [result])
          #VALUES (?) only takes string as parameter
          #and it must be an array, hence giving the string in a tuple 
          #otherwise it will consider each letter of the string as an array entry..
          c.execute('INSERT INTO balance (poidsNich) VALUES (?)', [str(result),])

    except (KeyboardInterrupt, SystemExit):
       logger.critical("Erreur d'ecriture dans la table BALANCE - %s", [result])
       cleanAndExit()


###############################################################################
# Fin de traitement des infos des capteurs

    # commit et sauvegarde DB
    lv_conn.commit()
    lv_conn.close()

    #TODO utiliser une variable de conf pour le sleep time
    logger.debug("sleep 3 secondes")
    print("sleep 3 secondes")
    time.sleep(3)




