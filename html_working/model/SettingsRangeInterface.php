<?php

class SettingsRangeInterface extends ModelInterface
{
// Accepte une liste de settings en entrée et un objet DbMngSettings.
// Ce dernier est initialisé avec la liste de tous les settings et la liste des
// alias issues de la DB. Un nouveau record dans la db engendrera automatiquement
// une nouvelle propriété de l'objet.
//
// Le nom des propriétés correspond aux settings de la vue Réglage (table config).
	public $allRangesArray = [];


	public function __construct()
	// filtre la liste de paramètres en entrée et lance l'hydratation des
	// variables
	{
		$dbMngSettings = new NewDbMngSettings('configRange');
		$this -> allRangesArray =  $this -> sortArray($dbMngSettings -> getAll());
		unset($dbMngSettings);
	}


	private function sortArray($unformatedList)
	{
		$formatedList = [];
		foreach ($unformatedList as $row)
		{
			if (array_key_exists($row[0], $formatedList))
			{
				array_push($formatedList[$row[0]], $row[1]);
				sort($formatedList[$row[0]]);
			}
			else
			{
				$formatedList[$row[0]] = [$row[1]];
			}
		}
		return $formatedList;
	}

}
