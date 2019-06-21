<?php

//require_once("model/DbManager.php");

class SettingsInterface extends ModelInterface {
// Accepte une liste de settings en entrée et un objet DbMngSettings.
// Ce dernier est initialisé avec la liste de tous les settings et la liste des
// alias issues de la DB. Un nouveau record dans la db engendrera automatiquement
// une nouvelle propriété de l'objet.
//
// Le nom des propriétés correspond aux settings de la vue Réglage (table config).


public function __construct($inputArray, $dbMngSettings)
// filtre la liste de paramètres en entrée et lance l'hydratation des
// variables
{
	// get all motion settings and parameters from config table
	$listSettings = $dbMngSettings-> allSettingsArray;
	// input array treatment
	foreach ($inputArray as $setting => $value)
	{
		// if motion setting
		if (array_key_exists($setting, $listSettings))
		{
			// if value altered, check validity and store in new table
			if ( $value != $listSettings[$setting][0])
			{
				$this -> validateSettings($setting, $value, $listSettings[$setting][2], $dbMngSettings);
			}
			$this -> setHydrate ($setting, $value);
		}
	}
}


// ##################################################################
	private function validateSettings ($setting, $value, $valueType, $dbMngSettings)
	{ // validate input data before updating DB
		//

		// query table initialisation -> to get range values for settings
		if ($valueType == 'discreet')
		{
			$tempValue = $dbMngSettings -> getSettingRange ($setting);
			if (! in_array($value, $tempValue))
			{
				throw new Exception('Paramètre non valide : '. $setting .' - valeur : '.$value);
			}
		}

		elseif($valueType == 'range')
		{
			$range = $dbMngSettings -> getSettingRange ($setting, True);
			if (!($value >= $range[0] && $value <= $range[1]))
			{
				throw new Exception('Range non valide : '. $setting .' - valeur : '.$value .'('.$range[0].'  - '.$range[1].' )');
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
