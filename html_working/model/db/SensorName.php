<?php


class SensorName extends DbManager {
	//
	// Class managing sensor_definition table
	// -> get, push, modify
	//

	public function __construct($table) {
		// assign value of active table to protected variable '_table',
		// -> used in DbManager class
		$this->_table = $table;
		$this->sensorList = $this->getAll();
	}

  // Liste des getters : fonctions permettant de récupérer les valeurs des attributs privés

  // ex: public function tempExt() {return $this->_tempExt;}

	function getSensor() {
		// return one sensor from sensor_config
    //


	}

	function getAllSensor() {
		// return all sensors from sensor_config on a list
    // TODO possibilité de limiter le nombre de colonne retournées
    //
		return $this->sensorList;

	}


}
