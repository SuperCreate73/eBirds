<?php

class SettingsInterface extends ModelInterface
{
// Accepte une liste de settings en entrée et un objet DbMngSettings.
// Ce dernier est initialisé avec la liste de tous les settings et la liste des
// alias issues de la DB. Un nouveau record dans la db engendrera automatiquement
// une nouvelle propriété de l'objet.
//
// Le nom des propriétés correspond aux settings de la vue Réglage (table config).
	private $dbMngSettings;
	public $allSettingsArray = [];

	public function __construct($inputArray)
	// filtre la liste de paramètres en entrée et lance l'hydratation des
	// variables
	{
		$this->dbMngSettings 		= new NewDbMngSettings('config') ;
		$this->allSettingsArray =  $this -> sortArray($this -> dbMngSettings -> getAll());

		// if inputArray is empty, fill it with DB values
		if (!isset($inputArray) || $inputArray == "")
		{
			foreach ($this->allSettingsArraythis as $key => $row) {
				$inputArray[$key] = $row[0];
			}
		}
		$settingsRangeInterface = new SettingsRangeInterface;
		// get all motion settings and parameters from config table
		// $listSettings = $dbMngSettings-> allSettingsArray;
		// input array treatment
		foreach ($inputArray as $setting => $value)
		{
			// if motion setting
			if (array_key_exists($setting, $this -> allSettingsArray))
			{
				// if value altered, check validity and store in new table
				if ( $value != $this -> allSettingsArray[$setting][0])
				{
					$this -> validateSettings($setting,
																		$value,
																		$settingRangesInterface);
				}
				$this -> setHydrate ($setting, $value);
			}
		}
		unset($settingsRangeInterface);

	}

	public function updateValues()
	{
		$this -> dbMngSettings -> updateValues($this->_properties);
	}


	private function sortArray($unformatedList)
	{
		foreach ($unformatedList as $row)
		{
			$formatedList[$row[0]] = [$row[1], $row[2], $row[3]];
		}
		uasort($formatedList, 'cmp');

		return $formatedList;
	}



	// if (!function_exists('cmp'))
	// {
	// }
	private function cmp($a, $b)
	{
		if ($a[1] == $b[1])
		{
			return 0;
		}
		return ($a[1] < $b[1]) ? -1 : 1;
	}


// ##################################################################
	private function validateSettings ($setting, $value, $settingsRangeInterface)
	{ // validate input data before updating DB
		//
		$valueType = $this -> allSettingsArray[$setting][2];
		// query table initialisation -> to get range values for settings
		if ($valueType == 'discreet')
		{
			if (! in_array($value, $settingsRangeInterface -> allRangesArray[$setting]))
			{
				throw new Exception('Paramètre non valide : '. $setting .' - valeur : '.$value);
			}
		}

		elseif($valueType == 'range')
		{
			if (!($value >= $settingRangesInterface->allRangesArray[$setting][0] &&
						$value <= $settingRangesInterface->allRangesArray[$setting][1]))
			{
				throw new Exception('Range non valide : '. $setting .' - valeur : '. $value);
			}
		}

		elseif ($valueType == 'email')
		{
			$atom   = '[-a-z0-9!#$%&\'*+\\/=?^_`{|}~]';   // caractères autorisés avant l'arobase
			$domain = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)'; // caractères autorisés après l'arobase (nom de domaine)
			$regex = '/^' . $atom . '+' .   // Une ou plusieurs fois les caractères autorisés avant l'arobase
			'(\.' . $atom . '+)*' .         // Suivis par zéro point ou plus
			// séparés par des caractères autorisés avant l'arobase
			'@' .                           // Suivis d'un arobase
			'(' . $domain . '{1,63}\.)+' .  // Suivis par 1 à 63 caractères autorisés pour le nom de domaine
			// séparés par des points
			$domain . '{2,63}$/i';          // Suivi de 2 à 63 caractères autorisés pour le nom de domaine

			// test de l'adresse e-mail
			if (!preg_match($regex, $value) && !$value == "" )
			{
				throw new Exception('Paramètre non valide : '. $setting .' - valeur : '.$value);
			}
		}

		elseif ($valueType == 'text')
		{
			if (!is_string($value))
			{
				throw new Exception('Paramètre non valide : '. $setting .' - valeur : '.$value);
			}
		}

		else
		{
			throw new Exception('Paramètre non valide : '. $setting .' - valeur : '.$value);
		}
	}
}
