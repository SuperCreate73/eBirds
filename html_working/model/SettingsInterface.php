<?php

class SettingsInterface extends ModelInterface {
	// Get Settings from table, and store all in an Array.
	// Keep a connection with DbMngSettings for updating

	private $dbMngSettings;
	public $allSettingsArray = [];


	public function __construct($inputArray = NULL) {
	// filtre la liste de paramètres en entrée et lance l'hydratation des
	// variables

		$this->dbMngSettings 		= new DbMngSettings('config') ;
		$this->allSettingsArray =  $this -> sortArray($this -> dbMngSettings -> getAll());

		// if inputArray is empty, fill it with DB values
		if (!isset($inputArray) || $inputArray == "")
		{
			foreach ($this->allSettingsArray as $key => $row) {
				$inputArray[$key] = $row[0];
			}
		}

		// settings ranges initialisation
		$settingsRangeInterface = new SettingsRangeInterface;

		// input array treatment
		foreach ($inputArray as $setting => $value) {

			// filtering settings
			if (array_key_exists($setting, $this -> allSettingsArray)) {

				// if value altered, check validity and store in new table
				if ( $value != $this -> allSettingsArray[$setting][0]) {
					$this -> validateSettings($setting,
																		$value,
																		$settingsRangeInterface);
				}

				$this -> setHydrate ($setting, $value);
			}
		}

		// release object
		unset($settingsRangeInterface);

	}


	public function updateValues() {
		// update values of properties stored in array _properties

		$this -> dbMngSettings -> updateValues($this->_properties);

	}

// TODO : uniformiser la fonction de mise en forme

	private function sortArray($unformatedList) {

		if (!function_exists('cmp')) {

			function cmp($a, $b) {

				if ($a[1] == $b[1]) {
					return 0 ;
				}
				return ($a[1] < $b[1]) ? -1 : 1 ;
			}
		}

		foreach ($unformatedList as $row) {
			$formatedList[$row[0]] = [$row[1], $row[2], $row[3]] ;
		}

		uasort($formatedList, 'cmp') ;

		return $formatedList ;
	}


// ##################################################################
	private function validateSettings ($setting, $value, $settingsRangeInterface) {
		// validate input data before updating DB
		//
		$valueType = $this -> allSettingsArray[$setting][2];
		// query table initialisation -> to get range values for settings
		if ($valueType == 'discreet') {
			if (! in_array($value, $settingsRangeInterface -> allRangesArray[$setting])) {
				throw new Exception('Paramètre non valide : '. $setting .' - valeur : '.$value);
			}
		}

		elseif($valueType == 'range') {
			if (!($value >= $settingsRangeInterface->allRangesArray[$setting][0] &&
						$value <= $settingsRangeInterface->allRangesArray[$setting][1])) {
				throw new Exception('Range non valide : '. $setting .' - valeur : '. $value);
			}
		}

		elseif ($valueType == 'email') {
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
			if (!preg_match($regex, $value) && !$value == "" ) {
				throw new Exception('Paramètre non valide : '. $setting .' - valeur : '.$value);
			}
		}

		elseif ($valueType == 'text') {
			if (!is_string($value)) {
				throw new Exception('Paramètre non valide : '. $setting .' - valeur : '.$value);
			}
		}

		else {
			throw new Exception('Paramètre non valide : '. $setting .' - valeur : '.$value);
		}
	}
}

// TODO :  $output = shell_exec('echo "RangeNonValide : '.$value.' - '. json_encode($settingsRangeInterface->allRangesArray) .' - '. json_encode($settingsRangeInterface->allRangesArray[$setting][1]) .'" >> /var/www/debug.log');
