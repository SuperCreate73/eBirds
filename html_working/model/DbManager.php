<?php

abstract class DbManager {

	protected $_sql=false;

	protected function dbConnect() {

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
		try {
			$db = new PDO('sqlite:/var/www/nichoir.db');
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $db;
		}
		catch (PDOException $e) {
	  		$dbhote = "localhost";
    		$dbuser = "nichoir";
    		$dbpass = "";
    		$dbbase = "nichoir";
			$db = new PDO('mysql:host=localhost;dbname=nichoir', 'nichoir');
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->_sql=true;
			return $db;
		}
    }

	protected function clean($str) {
  		$search  = array('&'    , '"'     , "'"    , '<'   , '>'    );
  		$replace = array('&amp;', '&quot;', '&#39;', '&lt;', '&gt;' );
 		$str = str_replace($search, $replace, $str);
  		return $str;
	}

	protected function md5Hash($str) {
			// MD5 hash of input String
			return md5(htmlspecialchars($str));
	}
}
