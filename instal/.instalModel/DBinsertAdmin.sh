#!/bin/bash
# coding:UTF-8

# Insertion des champs dans la DB
#######################################################################
# crée un utilisateur par défaut - admin - dans la base de donnée
#######################################################################
# check that the table is empty
if [ ! `sqlite3 "$DB_FILE" "SELECT count() FROM users"` -gt 0 ] ; then
  # insert defaut User
  printMessage "insertion de l'utilisateur admin (password = admin)" "$DB_FILE"
  # calculate MD5 password
  adminPwd=$(printf '%s' "admin" | md5sum | cut -d ' ' -f 1)
  # insert in table
  sqlite3 "$DB_FILE" "INSERT INTO users ('login', 'password') VALUES ('admin', '$adminPwd');" || printError "$?"
fi
