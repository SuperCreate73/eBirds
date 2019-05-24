<?php

abstract class DbManager {

	protected $_sql=false;
	protected $_table;

	protected function dbConnect() {

		// On utilise l'objet PHP PDO qui doit permettre une portabilité facile du code pour changer de DB.
		// Pour une connexion à la db SQLite du nichoir...
		$db = new PDO('sqlite:/var/www/nichoir.db');
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $db;
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

	protected function getAll($columns = NULL) {
		// Get all records from $_table
		//
		if (is_null($columns)) {
			$sqlColumns="*";
		}
		elseif (is_array($columns)) {
			foreach ($columns as $values) {
				$sqlColumns=$sqlColumns.$values.", ";
			}
			$sqlColumns = substr($sqlColumns, 0, -2);
		}
		else {
			$sqlColumns = $columns;
		}
		$db = $this->dbConnect();
		$sql = "SELECT ".$sqlColumns." FROM ".$this->_table.";";
		$stmt = $db->query($sql);
		$list = $stmt->fetchall();
		return($list);
	}

	protected function getKey($key, $value, $columns = NULL) {
		// Get all records from $_table where $key = $value
		//
		if (is_null($columns)) {
			$sqlColumns="*";
		}
		elseif (is_array($columns)) {
			foreach ($columns as $values) {
				$sqlColumns=$sqlColumns.$values.", ";
			}
			$sqlColumns = substr($sqlColumns, 0, -2);
		}
		else {
			$sqlColumns = $columns;
		}
		$db = $this->dbConnect();
		$sql = "SELECT ".$sqlColumns." FROM ".$this->_table." WHERE ".$key." = '".$value."' ;";
		$stmt = $db->query($sql);
		$list = $stmt->fetchall(PDO::FETCH_BOTH);
		debug_to_console('texte : '.json_encode(array_shift($list)));
		return(array_shift($list));
	}

	protected function keyExist ($whereClause) {
		// Test exitence of key
		// return (boolean)
		//
		$db = $this->dbConnect();
		$sql = "SELECT EXISTS(SELECT 1 FROM ".$this->_table." WHERE (".$whereClause.")) AS returnVal ;";
		$stmt = $db->query($sql);
		$result = $stmt->fetch();
		if ($result['returnVal'] == 1) {
			return True;
		}
		else {
			return False;
		}
	}

	protected function countRecords ($whereClause = NULL) {
		// Get number of rows
		// return (integer)
		//
		$where = (is_null($whereClause)) ? "" : " WHERE (".$whereClause.")";
		$db = $this->dbConnect();
		$sql = "SELECT count(*) as nbres FROM ".$this->_table.$where." ;";
		$stmt = $db->query($sql);
		$result = $stmt->fetch();
		return $result['nbres'];
	}
}
