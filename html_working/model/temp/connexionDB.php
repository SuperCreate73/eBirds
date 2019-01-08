<?php
// Pour une connexion à une db mySQL, pour tester en local...
// On attribue à des variables les différentes informations permettant de se connecter à la DB :
/*   $dbhote = "localhost";
    $dbuser = "root";
    $dbpass = "root";
    $dbbase = "nichoir";
*/
// On utilise l'objet PHP PDO qui doit permettre une portabilité facile du code pour changer de DB.
/*      $dsn = "mysql:dbname=$dbbase; host=$dbhote";
      $fichier_db = new PDO($dsn, $dbuser, $dbpass);
*/
// Pour une connexion à la db SQLite du nichoir...

    $fichier_db = new PDO('sqlite:/var/www/nichoir.db');
 ?>
