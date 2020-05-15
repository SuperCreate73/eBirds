#!/bin/bash
# coding:UTF-8

# Insertion des champs dans la DB
#######################################################################
# insertion des paramètres dans la base de données
#######################################################################
printMessage "insertion des paramètres - table" "nichoir.db"

doInsertRecord $(ls "$INSTALL_PATH"/.input/DBinsert*)
