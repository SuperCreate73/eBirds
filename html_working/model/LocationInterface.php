<?php
// Checked
class LocationInterface extends ModelInterface
{
// Accepte une liste de location en entrée et un objet DbMngLocation.
// Ce dernier est initialisé avec la liste de toutes les locations.
// Un nouveau record dans la db engendrera automatiquement
// une nouvelle propriété de l'objet.
//
// Le nom des propriétés correspond aux locations de la vue Réglage (table location).
private $dbMngSettings;
public $allLocationArray = [];


public function __construct($inputArray = NULL)
// filtre la liste de paramètres en entrée et lance l'hydratation des
// variables
{
	// $output = shell_exec('echo "inputArray : '. json_encode($inputArray) .'" >> /var/www/debug.log');
	// get all motion settings and parameters from config table
	// $locationArray = $dbMngsettings-> allLocationArray;
	// input array treatment
	$this->dbMngSettings = new NewDbMngSettings('location') ;
	$this->allLocationArray =  $this -> sortArray($this->dbMngSettings -> getAll());

	if (is_null($inputArray) || $inputArray == "")
	{
		foreach ($this->allLocationArray as $key => $row) {
			$inputArray[$key] = $row[0];
		}
	}

	foreach ($inputArray as $fieldLocation => $value)
	{
		// if motion setting
		if (array_key_exists($fieldLocation, $this->allLocationArray))
		{
			// if value altered, check validity and store in new table
			if ( $value != $this->allLocationArray[$fieldLocation][0] && $value != "")
			{
				$this -> validateSettings($fieldLocation, $value);
			}
			$output = shell_exec('echo "LocationInterface : '. $fieldLocation.' - '.$value.'" >> /var/www/debug.log');
			$this -> setHydrate ($fieldLocation, $value);
		}
	}
}


public function updateValues()
{
	$this -> dbMngSettings -> updateValues($this -> _properties);
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
	private function validateSettings ($fieldLocation, $value)
	{ // validate input data before updating DB
		//
		$valueType = $this -> allLocationArray[$fieldLocation][2];
		// query table initialisation -> to get range values for settings
		if ($valueType == 'text')
		{
			if (!is_string($value))
			{
				throw new Exception('Paramètre non valide : '. $fieldLocation .' - valeur : '.$value);
			}
		}

		elseif ($valueType == 'integer')
		{
			if (!is_int(intval($value)) && ! $value == "")
			{
				throw new Exception('Paramètre non valide : '. $fieldLocation .' - valeur : '.$value);
			}
		}
		elseif ($valueType == 'long')
		{
			if (!is_float(floatval($value)) && ! $value == "")
			{
				throw new Exception('Paramètre non valide : '. $fieldLocation .' - valeur : '.$value);
			}
		}
		else {
			throw new Exception('Paramètre non valide : '. $setting .' - valeur : '.$value);
		}
	}
}
