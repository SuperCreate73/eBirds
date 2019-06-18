<?php

//require_once("model/DbManager.php");

class DbMngSettings extends DbManager {
	// 'setting' table management : add, remove, modify, get records from table
	// table 'config': setting, value, default value (or comment), value type: (discreet, range, file)
	// linked table 'configRange' : setting, allowValue
	//			--> one row for each value or (min or max)
	// 			--> multiple row for each setting

	// TODO gestion de l'option 'none' pour le Timelapse
	// TODO gestion du mode d'enregistrement de l'image

//	public function __construct() {
		public $config = "config";
		public $configRange = "configRange";
//	}

public function getSettingList ()
	// get a list of all settings as key and array of value, priority and valueType as value
	{
		function cmp($a, $b)
		{
  		if ($a[1] == $b[1])
			{
        return 0;
    	}
    	return ($a[1] < $b[1]) ? -1 : 1;
		}

		$this->_table = $this->config;
		$unformatedList = $this -> getAll(['setting', 'value', 'priority', 'valueType']);
		foreach ($unformatedList as $key => $row)
		{
			$formatedList[$row[0]] = [$row[1], $row[2], $row[3]];
		}
		uasort($formatedList, 'cmp');
		return $formatedList;
	}

// ##################################################################
	public function getSettingFromAlias ($alias, $value)
	{
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
	private function getSettingRange ($setting, $sorted = False)
	{
		// get possible range for setting -> return list
		$this->_table = $this->configRange;

		// get values : list of dict with key = '0' or 'rangeValue'
		$getArray = $this -> getKey('setting', $setting, 'rangeValue');

		$returnList = array();
		// rearrange in list of values
		foreach ($getArray as $value) {
				array_push($returnList, $value['rangeValue']);
		}

		if ($sorted)
		{
			asort($returnList);
		}

		return ($returnList);
	}

// ##################################################################
	public function getSettingValue ($setting)
	{
		// Return current value from setting
		$this->_table = $this->config ;
		$result= $this->getKey('setting', $setting, 'value');

		return ($result['value']);
	}

// ##################################################################
	public function getAliasValue ($alias)
	{
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
		$list = array_shift($list);

		return($list['aliasValue']);
	}

	//	public function validateValue ($setting, $value) {
// ##################################################################
	public function updateValues ($inputArray)
	{ // primary function used to update settings
		//

		// variable Réinitialisation
		$changedSettings = array();
		// get all motion settings and parameters from config table
		$listSettings = $this-> getSettingList();
		// input array treatment
		foreach ($inputArray as $setting => $value)
		{
			// if motion setting
			if (array_key_exists($setting, $listSettings))
			{
				// if value altered, check validity and store in new table
				if ( $value != $listSettings[$setting][0])
				{
					$this -> validateSettings($setting, $value, $listSettings[$setting][2]);
					$changedSettings[$setting] = $value;
				}
			}
		}
		// if values are altered, modify in DB
		if (count($changedSettings) > 0) {
			$this -> modifySettings ($changedSettings);
		}
		// return list of settings for further usage (motion_Interface)
		return $listSettings;
		// TODO activate when tests are done
		//throw new Exception('Paramètre inconnu : '. $setting);
	}
// ##################################################################
	private function validateSettings ($setting, $value, $valueType)
	{ // validate input data before updating DB
		//

		// query table initialisation -> to get range values for settings
		$this->_table = $this->configRange;

		if ($valueType == 'discreet')
		{
			$tempValue = $this -> getSettingRange ($setting);
			if (! in_array($value, $tempValue))
			{
				throw new Exception('Paramètre non valide : '. $setting .' - valeur : '.$value);
			}
		}

		elseif($valueType == 'range')
		{
			$range = $this -> getSettingRange ($setting, True);
			if (!($value >= $range[0] && $value <= $range[1]))
			{
				throw new Exception('Range non valide : '. $setting .' - valeur : '.$value .'('.$range[0].'  - '.$range[1].' )');
			}
		}

		elseif ($valueType == 'email')
		{
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
			if (!preg_match($regex, $value) && !$value == "" )
			{
				throw new Exception('Paramètre non valide : '. $setting .' - valeur : '.$value);
			}
		}
		elseif ($valueType == 'text')
		{
			if (!is_string($value))
			{
				throw new Exception('Paramètre non valide : '. $setting .' - valeur : '.$value);
			}
		}

		else
		{
			throw new Exception('Paramètre non valide : '. $setting .' - valeur : '.$value);
		}
	}

// ##################################################################
	public function modifySettings ($listSettings)
	{
		// modify values of altered settings
		foreach($listSettings as $setting => $value)
		{
			$this->_table = $this->config;
			$db = $this->dbConnect();
			$stmt=$db->prepare("UPDATE config SET value = :Value WHERE (setting = :Setting)");
			$resultat=$stmt->execute(array(
				'Setting'	=>	$setting,
				'Value' => $value));
		}
	}

	public function getAliasArray()
	{ // get list of all alias name
		// Return current value from alias
		$db = $this->dbConnect();
		$sql = "SELECT DISTINCT alias FROM configAlias ;";
		$stmt = $db->query($sql);
		$list = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

		return($list);
	}

	// ##################################################################
	public function keyTest ($table, $where) {
		// modify value
		$this->_table = $table ;
		return ($this->keyExist($where)) ;
	}

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

// if (getenv("HTTP_DEBUG_MODE") == 4)
// {
// 	$output = shell_exec('echo "DbMngSetting_validateValue_getKey-returnValueArray : '. json_encode($valueTypeArr) .'" >> /var/www/debug.log');
// 	$output = shell_exec('echo "DbMngSetting_validateValue_getKey-returnValue : '. $valueType .'" >> /var/www/debug.log');
// }
