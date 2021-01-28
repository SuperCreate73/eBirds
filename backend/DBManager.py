#!/usr/bin/python3
# -*- coding: utf-8  -*-

# TODO check output of dbRead, seems to be lst of list, but description said disctionary ???

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

        # connexion à la DB et ouverture du curseur
        lv_conn = sqlite3.connect(self._dbFile)
        cursor  = lv_conn.cursor()

        # construction de la requête SQL à partir du dictionnaire en paramètre
        sql =   'INSERT INTO {} '.format(self._dbTable) + \
                '(' + ', '.join(inputDict.keys()) + \
                ') VALUES '+ \
                repr(tuple(str(value) for value in inputDict.values()))

        # écriture dans la db dans un bloc Try-Execpt pour gérer les erreurs
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

        # connexion à la DB et ouverture du curseur
        lv_conn = sqlite3.connect(self._dbFile)
        cursor  = lv_conn.cursor()
        output = []

        # construction de la requête SQL en fonction du paramètre 'whereClause'
        if (whereClause):
            sql = 'SELECT * FROM {} WHERE ({})'.format(self._dbTable, whereClause)
        else:
            sql = 'SELECT * FROM {}'.format(self._dbTable)

        # écriture dans la db dans un bloc Try-Execpt pour gérer les erreurs
        try:
            cursor.execute(sql)

        except Exception as err:
            lv_conn.close()
            raise err

        for rowList in cursor:
            output.append(rowList)

        return output

    def dbDelete(self, whereClause = None):
        """
        Not yet coded
        """
        # TODO if needed
        return whereClause

    def dbExist(self, whereClause = None):
        """
        Not yet coded
        """
        # TODO if needed
        return whereClause

    def dbCount(self, whereClause = None):
        """
        Not yet coded
        """
        # TODO if needed
        return whereClause


class DBManager:
    """
    Master class to initiate appropriate db manager.
    Interface curently available :
        SQLITE3
    """

    def __init__(self, dbFile, dbTable, apiLink):
        self._APIlink = "API{}".format(apiLink.capitalize())
        self._dbFile = dbFile
        self._dbTable = dbTable

    def setAPI(self):
        """
        Link db API sent as parameter 'apilink'
        """
        return globals()[self._APIlink](self._dbFile, self._dbTable)


if __name__ == "__main__":

    DB = "/var/www/nichoir.db"
    TABLE = "sensors"

    dbCapteur = DBManager(DB, TABLE, "sqlite3").setAPI()
    print (dbCapteur.dbRead('sensor = "DHT11"'))
