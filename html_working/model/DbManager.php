<?php

abstract class DbManager {


	protected $_table;


	public function setTable($tableName) {
		$this->_table = $tableName ;
	}


	protected function dbConnect() {
		// On utilise l'objet PHP PDO qui doit permettre une portabilité facile du code pour changer de DB.
		// Pour une connexion à la db SQLite du nichoir...

		$db = new PDO('sqlite:/var/www/nichoir.db');
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $db;
  }


	protected function cleanString($str) {
		// clean input string from special characters that could cause problem with DB
		//
  	$search  = array('&'    , '"'     , "'"    , '<'   , '>'    );
  	$replace = array('&amp;', '&quot;', '&#39;', '&lt;', '&gt;' );
 		$str = str_replace($search, $replace, $str);
  	return $str;
	}


	protected function md5Hash($str) {
		// MD5 hash of input String
		return md5(htmlspecialchars($str));
	}


	public function getAll($columns = NULL) {
		// Get all records for $columns from $_table
		//
		if (is_null($columns)) {
			$sqlColumns = "*";
		}

		elseif (is_array($columns)) {
			$sqlColumns = "";
			foreach ($columns as $values) {
				$sqlColumns = $sqlColumns.$values.", ";
			}
			// remove last ', ' from $sqlColumns
			$sqlColumns = substr($sqlColumns, 0, -2);
		}

		else {
			$sqlColumns = $columns;
		}

		// SQL query
		$db = $this->dbConnect();
		$stmt = $db->prepare("SELECT ". $sqlColumns ." FROM ". $this->_table );
		$stmt->execute();
		$list = $stmt->fetchall();
		$stmt->closeCursor();
		return $list;
	}

	protected function getKey($key, $value, $columns = NULL) {
		// Get all records from $_table where $key = $value
		//
		// $column formating
		if (is_null($columns)) {
			$sqlColumns = "*";
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

		// sql request
		$db = $this->dbConnect();
		$stmt = $db->prepare("SELECT ".$sqlColumns." FROM ".$this->_table." WHERE (".$key." = :value) ");
		$stmt->bindParam('value', $value);
		$stmt->execute();
		$list = $stmt->fetchall(PDO::FETCH_BOTH);
		$stmt->closeCursor();

		if (count($list) == 1) {
			$list = array_shift($list);
		}

		return $list ;
	}

	protected function keyExist ($whereClause) {
		// Test exitence of key
		// return (boolean)
		//
		$db = $this->dbConnect();
		$stmt = $db->prepare("SELECT EXISTS (SELECT 1 FROM ".$this->_table." WHERE (".$whereClause.")) AS returnVal ");
		$stmt->execute();
		$result = $stmt->fetch();
		$stmt->closeCursor();

		return ($result['returnVal'] == 1) ? True : False ;

		// if ($result['returnVal'] == 1) {
		// 	return True;
		// }
		// else {
		// 	return False;
		// }
	}

	protected function countRecords ($whereClause = NULL) {
		// Get number of rows
		// return (integer)
		//
		$where = is_null($whereClause) ? "" : " WHERE (".$whereClause.")";

		$db = $this->dbConnect();
		$stmt = $db->prepare("SELECT count(*) FROM ".$this->_table.$where );
		$stmt->execute();
		$result = $stmt->fetchColumn();
		$stmt->closeCursor();

		return $result;
		// $stmt = $db->query($sql);
		// $result = $stmt->fetch();
    //
		// return $result['nbres'];
	}
}
