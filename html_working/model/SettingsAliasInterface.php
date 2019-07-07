<?php

class SettingsAliasInterface extends ModelInterface
{
// Accepte une liste de settings en entrée et un objet DbMngSettings.
// Ce dernier est initialisé avec la liste de tous les settings et la liste des
// alias issues de la DB. Un nouveau record dans la db engendrera automatiquement
// une nouvelle propriété de l'objet.
//
// Le nom des propriétés correspond aux settings de la vue Réglage (table config).
	private $dbMngSettings;
	public $allAliasArray = [];


	public function __construct()
	// filtre la liste de paramètres en entrée et lance l'hydratation des
	// variables
	{
		$dbMngSettings = new NewDbMngSettings('configAlias');
		$this -> allAliasArray =  $this -> sortArray($dbMngSettings -> getAll());

		// get all motion settings and parameters from config table
		// $listSettings = $dbMngSettings-> allSettingsArray;
		// // input array treatment
		// foreach ($inputArray as $setting => $value)
		// {
		// 	// if motion setting
		// 	if (array_key_exists($setting, $listSettings))
		// 	{
		// 		// if value altered, check validity and store in new table
		// 		if ( $value != $listSettings[$setting][0])
		// 		{
		// 			$this -> validateSettings($setting, $value, $listSettings[$setting][2], $dbMngSettings);
		// 		}
		// 		$this -> setHydrate ($setting, $value);
		// 	}
		// }
	}

	private function sortArray($unformatedList)
	{
		$formatedList = [];
		foreach ($unformatedList as $row)
		{
			if (array_key_exists($row[0], $formatedList))
			{
				if (array_key_exists($row[1], $formatedList[$row[0]]))
				{
					// array_push($formatedList[$row[0]][$row[1]], $row[2] => $row[3]);
					$formatedList[$row[0]][$row[1]][$row[2]]=$row[3];
				}
				else
				{
					$formatedList[$row[0]][$row[1]] = [$row[2] => $row[3]];
				}
			}
			else
			{
				$formatedList[$row[0]] = [$row[1] => [$row[2] => $row[3]]];
			}
		}
		$output = shell_exec('echo "AliasInterface : '. json_encode($formatedList) .'" >> /var/www/debug.log');
		return $formatedList;
	}

}
