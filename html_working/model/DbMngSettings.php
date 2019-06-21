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
		// public $config ;
		// public $configRange ;
		public $allSettingsArray = [];
		public $aliasArray = [];
		public $settingsList = [];
//	}

public function __construct()
{
	$this -> config = "config";
	$this -> configRange = "configRange";
	$this -> allSettingsArray = $this -> getSettingList();
	$this -> aliasArray = $this ->getAliasArray();
	foreach ($this -> allSettingsArray as $key => $array)
	{
		$this -> settingsList[$key] = $array[0];
	}
}


private function getSettingList ()
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
	public function getSettingRange ($setting, $sorted = False)
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
	private function getAliasArray()
	{ // get list of all alias name
		// Return current value from alias
		$db = $this->dbConnect();
		$sql = "SELECT DISTINCT alias FROM configAlias ;";
		$stmt = $db->query($sql);
		$list = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

		return($list);
	}

// ##################################################################
	public function updateValues ($inputArray)
	// update settings in DB
	{
		if (count($inputArray) == 0)
		{
			return ;
		}
		$this->_table = $this->config;
		foreach ($inputArray as $setting => $value)
		{
			$db = $this->dbConnect();
			$stmt=$db->prepare("UPDATE config SET value = :Value WHERE (setting = :Setting)");
			$resultat=$stmt->execute(array(
				'Setting'	=>	$setting,
				'Value' => $value));
		}
	}

	// ##################################################################
	public function keyTest ($table, $where) {
		// modify value
		$this->_table = $table ;
		return ($this->keyExist($where)) ;
	}
}

// if (getenv("HTTP_DEBUG_MODE") == 4)
// {
// 	$output = shell_exec('echo "DbMngSetting_validateValue_getKey-returnValueArray : '. json_encode($valueTypeArr) .'" >> /var/www/debug.log');
// 	$output = shell_exec('echo "DbMngSetting_validateValue_getKey-returnValue : '. $valueType .'" >> /var/www/debug.log');
// }
