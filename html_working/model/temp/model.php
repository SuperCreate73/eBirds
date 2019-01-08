<?php

function dbConnect()
{
    try
    {
	// Pour une connexion à une db mySQL, pour tester en local...
	// On attribue à des variables les différentes informations permettant de se connecter à la DB :
	/*  $dbhote = "localhost";
    	$dbuser = "root";
    	$dbpass = "root";
    	$dbbase = "nichoir";
	*/
	// On utilise l'objet PHP PDO qui doit permettre une portabilité facile du code pour changer de DB.
	/*	$dsn = "mysql:dbname=$dbbase; host=$dbhote";
      	$fichier_db = new PDO($dsn, $dbuser, $dbpass);
	*/
	// Pour une connexion à la db SQLite du nichoir...
		$db = new PDO('sqlite:/var/www/nichoir.db');
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $db;
    }
    catch(Exception $e)
    {
        die('Erreur : '.$e->getMessage());
    }
}

function checkLogin($login,$passe)
{
  	$fichier_db = dbConnect();
  	// On "hashe" en md5 le mot de passe avant de faire la requête.
  	// En effet, les mots de passe sont stockés encryptés dans la DB.
  	$passe= md5($passe);
  	// On utilise la fonction "quote" pour filtrer et éventuellement ajouter des caractères
  	// d'échappement dans les informations transmises par le formulaire
  	$nom_sql = $fichier_db->quote($login);
  	$pass_sql = $fichier_db->quote($passe);
  	// htmlspecialstring

  	// On formule la requête SQL. On ajoute à la variable nbres le nombre de lignes de la DB dans
  	// lesquelles le mot de passe et le login correspondent à ceux fournis.
  	$sql = "SELECT count(*) as nbres FROM Users WHERE login=$nom_sql AND password=$pass_sql";

  	//$resultat = $dbh->query($sql);
  	$resultat = $fichier_db->query($sql);

  	$row = $resultat->fetch();
  	$resultat = null;

  	// Si nbres contient "1" c'est qu'il y a bien une ligne avec mot de passe et identifiant associés
  	if($row['nbres'] == 1)
  	{
  		return TRUE; // La fonction de vérification renvoie "TRUE"
	}
	else
	{ // Autrement (à priori nbre == 0), il n'y a pas de ligne avec ce login et mot de passe
    	return FALSE; // la fonction renvoie "FALSE"
	}
}

