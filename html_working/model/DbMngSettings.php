<?php

require_once("model/DbManager.php");

class DbMngSettings extends DbManager {
	// 'setting' table management : add, remove, modify, get records from table
	//

	private function modifySetting ($setting,$value) {
		// Modify setting
		// If already checked if record exist in DB, $testExist is set to False
		//

		// update existing setting
		$db = $this->dbConnect();
		$stmt=$db->prepare("UPDATE config SET value = :Value WHERE (setting = :Setting)");
		$resultat=$stmt->execute(array(
			'Setting'	=>	$setting,
			'Value' => $value));
	}

	public function addSetting ($setting,$value) {
		// insert new setting
		// If already checked if record exist in DB, $testExist is set to False
		//
		// check if setting exist already in DB
		if ($this->existSetting ($setting)) {
			$this->modifySetting($setting,$value,False);
			return ;
		}
		// add new setting
		$db = $this->dbConnect();
		$stmt=$db->prepare("INSERT INTO config (setting, value) VALUES (:Setting, :Value)");
		$result=$stmt->execute(array(
				'Setting' => $setting,
				'Value' => $value));
	}

	public function removeSetting ($setting) {
		// Remove completely a pair setting/value
		//
		$db = $this->dbConnect();
		$stmt=$db->prepare("DELETE FROM config WHERE (setting= :Setting)");
		$result=$stmt->execute(array(
				'Setting' => $setting,
				));
	}

	public function existSetting ($setting) {
		// Test exitence of key
		// return (boolean)
		//
		$db = $this->dbConnect();
		$stmt=$db->prepare("SELECT EXISTS(SELECT 1 FROM config WHERE (setting= :Setting)) AS returnVal");
		//$stmt=$db->prepare("SELECT EXISTS(SELECT 1 FROM config WHERE (setting= :Setting) AND value IS NOT NULL) as returnVal");
		$stmt->execute(array(
				'Setting' => $setting,
				));
		$result = $stmt->fetch();
		if ($result['returnVal'] > 0) {
			return True;
		}
		else {
			return False;
		}
	}

	public function getSetting ($setting) {
		// Return value from key
		// return (value)
		//
		// if non existing setting, return NULL
		if (!$this->existSetting($setting)) {
			return NULL;
		}
		// get setting value
		$db = $this->dbConnect();
		$stmt=$db->prepare("SELECT value FROM config WHERE setting=:Setting");
		$stmt->execute(array(
				'Setting' => $setting,
				));
		$result = $stmt->fetch();
		return ($result['value']);
	}

}
