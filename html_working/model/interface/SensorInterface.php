<?php

class SensorInterface extends ModelInterface {
	// Get sensor definition list to save as an array to save in a hided element for managing sensors_add
  //  -> [sensorName => [sensorReadout], [sensorPin]] (sensorName as key)
  //  ->

  // Get sensor already defined to create the list of installed sensors in viewReglages

	private $sensorName = [];


	public function __construct() {
	// filtre la liste de paramètres en entrée et lance l'hydratation des
	// variables

		$sensorName = new SensorName('sensor_name');
		$this->setHydrate('sensorName', $sensorName->getAllSensor());
		(getenv("HTTP_DEBUG_MODE") >= 1) ? debugToFile('LEVEL1:SENSORINTERFACE-Construct', json_encode($sensorName->getAllSensor())) : NULL ;

	}

	public function setSensorName($inputArray) {
		// input Array is a two dimensional array with one row for each sensor
		
		foreach ($inputArray as $key => $row)  {
			$value[]=$row['sensorName'];
		}
	$this->_properties['sensorName'] = $value;
	(getenv("HTTP_DEBUG_MODE") >= 1) ? debugToFile('LEVEL1:setSensorName', json_encode($value)) : NULL ;
	}
}
