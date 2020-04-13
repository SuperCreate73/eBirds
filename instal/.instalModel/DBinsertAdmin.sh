#!/bin/bash
# coding:UTF-8

# Insertion des champs dans la DB
#######################################################################
# crée un utilisateur par défaut - admin - dans la base de donnée
#######################################################################
if [ ! `sqlite3 /var/www/nichoir.db "SELECT count() FROM users"` -gt 0 ] ; then
  printMessage "insertion de l'utilisateur admin (password = admin)" "nichoir.db"
  adminPwd=$(printf '%s' "admin" | md5sum | cut -d ' ' -f 1)
  sqlite3 /var/www/nichoir.db "INSERT INTO users ('login', 'password') VALUES ('admin', '$adminPwd');" || printError "$?"
fi
