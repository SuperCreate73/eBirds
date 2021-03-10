<?php

class SettingsAliasInterface extends ModelInterface {
// Get Settings Aliases from table, and store all in an Array.
//

	public $allAliasArray = [];


	public function __construct() {
	// filtre la liste de paramètres en entrée et lance l'hydratation des
	// variables

		$dbMngSettings = new DbMngSettings('configAlias');
		$this -> allAliasArray =  $this -> sortArray($dbMngSettings -> getAll());

	}


	private function sortArray($unformatedList) {
	// sort and format input array

		$formatedList = [];

		foreach ($unformatedList as $row) {

			if (array_key_exists($row[0], $formatedList)) {

				if (array_key_exists($row[1], $formatedList[$row[0]])) {
					// array_push($formatedList[$row[0]][$row[1]], $row[2] => $row[3]);
					$formatedList[$row[0]][$row[1]][$row[2]]=$row[3];
				}

				else {
					$formatedList[$row[0]][$row[1]] = [$row[2] => $row[3]];
				}
			}

			else {
				$formatedList[$row[0]] = [$row[1] => [$row[2] => $row[3]]];
			}
		}

		return $formatedList;

	}

}
