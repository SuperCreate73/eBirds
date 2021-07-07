<?php

class Sensor extends DbManager {
// classe sensorSet indépendante, pas une extension de DbManager.
// Cette classe instancie les différentes classes des sensors et gère les input/output.
// Toutes les classes sont déclarées dans le même fichier qui porte le nom de la classe
// maître, comme ça elle sera automatiquement inclue grâce à la fonction générale.
//
	//
	// Class managing sensors tables
	// - sensor_definition
	// - sensorconfig
	// -> get, push, modify
  //
	//	Input parameter -> Object with program parameters
	// - Language
  // - labels	-> Object managing labels
	//

	public function __construct($table) {
		// assign value of active table to protected variable '_table',
		// -> used in DbManager class
		// $this->_table = 'sensor_name'
		// $this->_sensorNameList = $this->getAll('nameSensor')
		$this->_table = $table;
	}

  // Liste des getters : fonctions permettant de récupérer les valeurs des attributs privés

  // ex: public function tempExt() {return $this->_tempExt;}

	function getSensor(){
		// return one sensor from sensor_config
    //


	}

	function getSensorlabel(sensorID){
		// return label of sensor in parameter
		//


	}
	function getAllSensor(){
		// return all sensor from sensor_config
    // output is a table of dictionary with column name as keys


	}

	function setSensor(sensorTable){
		// save a new sensor

	}

	function setSensorlabel(sensorID){
		// set label of sensor in parameter
		//

		// if already exist, modify
		// otherwhise create it

	}

	function deleteSensor($id){
		// delete a sensor with PK in parameter
		//
			$db = $this->dbConnect();
			$stmt = $db->prepare("DELETE FROM ".$this->_table." WHERE (id = :id) ");
			$stmt->bindParam('id',$id);
			$stmt->execute();
			$result = $stmt->fetch();
			$stmt->closeCursor();
			return $result
	}

	function getSensorNames() {

		$sensorName = new SensorName('sensor_name');
		return $sensorName->getAllSensor();

	}

}

class SensorName extends DbManager {
	//
	// Class managing sensor_definition table
	// -> get, push, modify
	//

	public function __construct($table) {
		// assign value of active table to protected variable '_table',
		// -> used in DbManager class
		$this->_table = $table;
		$this->sensorList = $this->getAll()
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
