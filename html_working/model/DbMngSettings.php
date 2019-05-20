<?php

require_once("model/DbManager.php");

class DbMngSettings extends DbManager {
	// 'setting' table management : add, remove, modify, get records from table
	// table 'config': setting, value, default value (or comment), value type: (discreet, range, file)
	// linked table 'configRange' : setting, allowValue
	//			--> one row for each value or (min or max)
	// 			--> multiple row for each setting
//	public function __construct() {
		public $config = "config";
		public $configRange = "configRange";
//	}
// ##################################################################
	public function getSettingFromAlias ($alias, $value) {
		// Get all records from $configAlias
		// where alias = $alias and aliasValue = $value
		//
		$this->_table = 'configAlias';
		$db = $this->dbConnect();
		$sql = "SELECT setting, settingValue
						FROM configAlias
						WHERE alias = '".$alias."' AND aliasValue = '".$value."' ;";
		$stmt = $db->query($sql);
		$list = $stmt->fetchall(PDO::FETCH_KEY_PAIR);
		return($list);
	}

// ##################################################################
	private function getSettingRange ($setting, $sorted = False) {
		// get possible range for setting -> return list
		$this->_table = $this->configRange;
		if ($sorted) {
			return sort($this -> getKey('setting', $setting, 'rangeValue'));
		}
		else {
			return $this -> getKey('setting', $setting, 'rangeValue');
		}
	}

// ##################################################################
	public function getSettingValue ($setting) {
		// Return current value from setting
		$this->_table = $this->config ;
		$result= $this->getKey('setting', $setting, 'value');
		return ($result);
	}

// ##################################################################
	public function getAliasValue ($alias) {
		// Return current value from alias
		$db = $this->dbConnect();
		$sql = "SELECT aliasValue FROM configAlias
						INNER JOIN config
						ON (configAlias.setting = config.setting
							AND configAlias.settingValue = config.value)
						WHERE configAlias.alias = '".$alias."'
						LIMIT 1 ;";
		$stmt = $db->query($sql);
		$list = $stmt->fetchall();
		return($list);
	}

// ##################################################################
	public function validateValue ($setting, $value) {
		// get type of value to validate - discreet, range or file
		$this->_table = $this->config;
		$valueType = $this -> getKey('setting', $setting, 'valueType');

		// validate value
		$this->_table = $this->configRange;
		if ($valueType == 'discreet') {
			return (in_array($value, $this -> getSettingRange ($setting)) ? True : False) ;
		}
		elseif($valueType == 'range')  {
			$range = $this -> getSettingRange ($setting, True);
			return (($value >= $range[0] && $value <= $range[1]) ? True : False) ;
		}
		elseif ($valueType == 'email') {
			$atom   = '[-a-z0-9!#$%&\'*+\\/=?^_`{|}~]';   // caractères autorisés avant l'arobase
			$domain = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)'; // caractères autorisés après l'arobase (nom de domaine)

			$regex = '/^' . $atom . '+' .   // Une ou plusieurs fois les caractères autorisés avant l'arobase
			'(\.' . $atom . '+)*' .         // Suivis par zéro point ou plus
			                                // séparés par des caractères autorisés avant l'arobase
			'@' .                           // Suivis d'un arobase
			'(' . $domain . '{1,63}\.)+' .  // Suivis par 1 à 63 caractères autorisés pour le nom de domaine
			                                // séparés par des points
			$domain . '{2,63}$/i';          // Suivi de 2 à 63 caractères autorisés pour le nom de domaine

			// test de l'adresse e-mail
			if (preg_match($regex, $value)) {
			    return True;
			} else {
			    return False;
			}
			return True;
		}
		else {
			return False;
		}
	}

// ##################################################################
	public function modifySetting ($setting,$value) {
		// modify value
		$this->_table = $this->config;
		$db = $this->dbConnect();
		$stmt=$db->prepare("UPDATE config SET value = :Value WHERE (setting = :Setting)");
		$resultat=$stmt->execute(array(
			'Setting'	=>	$setting,
			'Value' => $value));
		return $resultat;
	}

		// public function getSettingFromAlias ($alias, $value) {
			// get corresponding list of settings from alias
		// 	$this->_table = $configAlias;
		// 	return array_unique($this -> getKey('alias', $alias, 'setting'));
		// }



	// public function addSetting ($setting,$value) {
	// 	// insert new setting
	// 	//
	// 	// check if setting exist already in DB
	// 	if ($this->existSetting ($setting)) {
	// 		$this->modifySetting($setting,$value);
	// 		return ;
	// 	}
	// 	// add new setting
	// 	$db = $this->dbConnect();
	// 	$stmt=$db->prepare("INSERT INTO config (setting, value) VALUES (:Setting, :Value)");
	// 	$result=$stmt->execute(array(
	// 			'Setting' => $setting,
	// 			'Value' => $value));
	// }

// ##################################################################
// TODO ##################################################################
// ##################################################################
	public function clearSetting ($setting) {
		// Remove completely a pair setting/value
		$this->_table = $this->config;
		$defautValue = $this -> getKey('setting', $setting, 'defautValue');
		if  ($defautValue == 'comment') {
			// TODO comment
		}
		else {
			$db = $this->dbConnect();
			$stmt=$db->prepare("UPDATE config SET value = defautValue WHERE (setting = :Setting)");
			$result=$stmt->execute(array(
					'Setting' => $setting,
					));
		}
	}

	// public function existSetting ($setting) {
	// 	// Test exitence of key
	// 	// return (boolean)
	// 	//
	// 	$this->_table = $config;
	// 	$where = "setting = '".$setting."'" ;
	// 	$result = $this-> keyExist($where);
	// 	return $result;
	// }
  //

}
