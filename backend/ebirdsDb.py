#!/usr/bin/python2
# -*- coding: utf-8  -*-

############################################################
#  TO REPLACE BY DBMANAGER.PY
############################################################

import sqlite3

class APISqlite3:
    """ SQLIte3 Interface -
        Init parameters:
            - File (str): db file to manage
            - Table (str): table to manage
    """

    def __init__(self, dbFile, dbTable):
        self._dbFile = dbFile
        self._dbTable = dbTable

    def dbInsert(self, inputDict):
        """ Insert one row in table.
            Take as arg a dictionary with pairs 'column'=value to insert in DB
        """
        lv_conn = sqlite3.connect(self._dbFile)
        cursor  = lv_conn.cursor()

        sql =   'INSERT INTO {} '.format(self._dbTable) + \
                '(' + ', '.join(inputDict.keys()) + \
                ') VALUES '+ \
                repr(tuple(str(value) for value in inputDict.values()))
        try:
            cursor.execute(sql)

        except Exception as err:
            lv_conn.rollback()
            lv_conn.close()
            raise err

        lv_conn.commit()
        lv_conn.close()

        return ('Données sauvegardées')

    def dbRead(self, whereClause = None):
        """ Select * from table and return a dictionary with first column as key.
            optional arg= string -> where clause
        """
        lv_conn = sqlite3.connect(self._dbFile)
        cursor  = lv_conn.cursor()
        output = []

        if (whereClause):
            sql = 'SELECT * FROM {} WHERE ({})'.format(self._dbTable, whereClause)
        else:
            sql = 'SELECT * FROM {}'.format(self._dbTable)

        try:
            cursor.execute(sql)

        except Exception as err:
            lv_conn.close()
            raise err

        for rowList in cursor:
            output.append(rowList)

        return output

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




# TODO créer les objets correpondant aux capteurs installés
#         --> lecture dans la DB
# TODO modifier la DB en faisant une table avec les données des capteurs
#         --> rajouter un ID pour chaque paramètre
# TODO faire une fonction 'read' dynamique avec les données de la table de correspondance

if __name__ == "__main__":

    DB = "/var/www/nichoir.db"
    TABLE = "sensors"

    dbCapteur = DBManager(DB, TABLE, "sqlite3").setAPI()
    print (dbCapteur.dbRead('sensor = "DHT11"'))

    # dbTest = {'sensor':'DHT11', 'pin':'13', 'location':'IN'}
    # print (dbCapteur.dbInsert(dbTest))
    # dbTest = {'sensor':'DHT11', 'pin':'17', 'location':'OUT'}
    # print (dbCapteur.dbInsert(dbTest))
    # dbTest = {'sensor':'HX711', 'pin':'15', 'location':'IN'}
    # print (dbCapteur.dbInsert(dbTest))
    # dbTest = {'sensor':'IR', 'pin':'8', 'location':'IN'}
    # print (dbCapteur.dbInsert(dbTest))
    # dbTest = {'sensor':'IR', 'pin':'9', 'location':'OUT'}
    # print (dbCapteur.dbInsert(dbTest))
