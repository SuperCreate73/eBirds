<?php

// depreceted

class DbMngLocation extends DbMngSettings {
	// 'location' table management : add, remove, modify, get records from table
	// table 'location': location, value, priority, value type: (discreet, range, file)
	//

		public $allLocationArray = [];
		public $locationList = [];
//	}

public function __construct()
{
	$this->_table = "location";
	$this -> allLocationArray = $this -> getSettingList("location");
	// $output = shell_exec('echo "liste dbMngLocation : '. json_encode($this -> locationArray) .'" >> /var/www/debug.log');

	foreach ($this -> allLocationArray as $key => $array)
	{
		$this -> locationList[$key] = $array[0];
	}
}

// ##################################################################
	// public function getSettingFromAlias ($alias, $value)
	// {
	// 	// Get all records from $configAlias
	// 	// where alias = $alias and aliasValue = $value
	// 	//
	// 	$this->_table = 'configAlias';
	// 	$db = $this->dbConnect();
	// 	$sql = "SELECT setting, settingValue
	// 					FROM configAlias
	// 					WHERE alias = '".$alias."' AND aliasValue = '".$value."' ;";
	// 	$stmt = $db->query($sql);
	// 	$list = $stmt->fetchall(PDO::FETCH_KEY_PAIR);
  //
	// 	return($list);
	// }


// ##################################################################
	// public function getSettingRange ($setting, $sorted = False)
	// {
	// 	// get possible range for setting -> return list
	// 	$this->_table = $this->configRange;
  //
	// 	// get values : list of dict with key = '0' or 'rangeValue'
	// 	$getArray = $this -> getKey('setting', $setting, 'rangeValue');
  //
	// 	$returnList = array();
	// 	// rearrange in list of values
	// 	foreach ($getArray as $value) {
	// 			array_push($returnList, $value['rangeValue']);
	// 	}
  //
	// 	if ($sorted)
	// 	{
	// 		asort($returnList);
	// 	}
  //
	// 	return ($returnList);
	// }


// // ##################################################################
// 	public function getSettingValue ($setting)
// 	{
// 		// Return current value from setting
// 		$result= $this->getKey('setting', $setting, 'value');
//
// 		return ($result['value']);
// 	}
//
// // ##################################################################
// 	private function getAliasArray()
// 	{ // get list of all alias name
// 		// Return current value from alias
// 		$db = $this->dbConnect();
// 		$sql = "SELECT DISTINCT alias FROM configAlias ;";
// 		$stmt = $db->query($sql);
// 		$list = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
//
// 		return($list);
// 	}

// ##################################################################
	public function updateValues ($inputArray)
	// update settings in DB
	{
		if (count($inputArray) == 0)
		{
			return ;
		}
		foreach ($inputArray as $location => $value)
		{
			$db = $this->dbConnect();
			$stmt = $db->prepare("UPDATE location SET value = :Value WHERE (location = :Location)");
			$resultat = $stmt->execute(array(
				'Location'	=>	$location,
				'Value' => $value));
		}
	}

	// public function keyTest ($table, $where) {
	// // ##################################################################
	// 	// modify value
	// 	return ($this->keyExist($where)) ;
	// }


// if (getenv("HTTP_DEBUG_MODE") == 4)
// {
// 	$output = shell_exec('echo "DbMngSetting_validateValue_getKey-returnValueArray : '. json_encode($valueTypeArr) .'" >> /var/www/debug.log');
// 	$output = shell_exec('echo "DbMngSetting_validateValue_getKey-returnValue : '. $valueType .'" >> /var/www/debug.log');
// }

}
