<?php

class DbMngSettings extends DbManager {
  // Specific methods for table Settings
  //

	public function __construct($table) {
		// initialize the table name (property of DbManager)
		$this -> _table = $table;
	}


// ##################################################################
	public function updateValues ($inputArray) {
	// update settings in DB

		if (count($inputArray) == 0) {
			return ;
		}

    // config of column names
		if ($this->_table == 'config') {
			$column = 'setting';
		}

		else {
			$column = $this->_table;
		}

		// SQL query
		foreach ($inputArray as $key => $value)
		{
			$db = $this->dbConnect();
			$stmt=$db->prepare("UPDATE ". $this->_table ." SET value = :Value WHERE (". $column ." = :Key)");
			$resultat=$stmt->execute(array(
				'Key'	=>	$key,
				'Value' => $value));
		}
	}

}

// TODO : Debug string: shell_exec('echo "DbMngSetting_validateValue_getKey-returnValueArray : '. json_encode($valueTypeArr) .'" >> /var/www/debug.log');
// TODO : shell_exec('echo "DbMngSetting_validateValue_getKey-returnValue : '. $valueType .'" >> /var/www/debug.log');
//
// if (getenv("HTTP_DEBUG_MODE") == 4)
// {
// 	$output = shell_exec('echo "DbMngSetting_validateValue_getKey-returnValueArray : '. json_encode($valueTypeArr) .'" >> /var/www/debug.log');
// 	$output = shell_exec('echo "DbMngSetting_validateValue_getKey-returnValue : '. $valueType .'" >> /var/www/debug.log');
// }
