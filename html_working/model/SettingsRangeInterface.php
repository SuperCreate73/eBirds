<?php

class SettingsRangeInterface extends ModelInterface {
	// Get Settings ranges from table, and store all in an Array.
	//

	public $allRangesArray = [];


	public function __construct() {
	// filtre la liste de paramètres en entrée et lance l'hydratation des
	// variables

		$dbMngSettings = new DbMngSettings('configRange');
		$this -> allRangesArray =  $this -> sortArray($dbMngSettings -> getAll());

		unset($dbMngSettings);

	}


	private function sortArray($unformatedList) {
		// sort and format input array


		$formatedList = [];
		foreach ($unformatedList as $row) {

			if (array_key_exists($row[0], $formatedList)) {
				array_push($formatedList[$row[0]], $row[1]);
				sort($formatedList[$row[0]]);
			}

			else {
				$formatedList[$row[0]] = [$row[1]];
			}
		}

		return $formatedList;

	}


}
