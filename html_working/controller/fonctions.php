<?php

function classLoad ($className) {
	// Automatic class load -> drived by spl_autoload_register('classLoad')
	// This function will scan all the path present in $paths to find $className + '.php'
	//
	$prePath = getcwd();
	$paths = array(
			'model/',
			'model/db/',
			'model/interface/',
	);

	foreach ($paths as $path) {
			$curPath = $prePath ."/". $path . $className . '.php';
			// echo "$curPath  -  ";
			if (file_exists($curPath)) {
					require_once($curPath);
					break;
			}
	}
}

spl_autoload_register('classLoad');

function setFocus($position)
{
	// Gère la page ayant le focus dans le menu principal en complétant le tableau tabFocus
	// avec la classe 'selected' à la position de liste correspondant à la position de page
	//
	// TODO - changer le paramètre: on envoie le nom de la page active à la fonction
	// 		- on crée un dictionnaire avec le nom des pages et l'index désiré
	// 		- on compte le nombre d'entrées pour la limite max de la boucle
	// 		- on recherche l'index correspondant à la page
	// 		- assignation du nom de classe ou seulement du bon index ? Idéalement, le nom
	// 		   de classe est donnée dans la vue
	//
	for ($nbre = 0; $nbre<=3; $nbre++)
	{
		$tabFocus[$nbre]='';
	}

	$tabFocus[$position]='selected';
	return $tabFocus;
}

function setFocusMen2($position)
{

	for ($nbre = 0; $nbre<=3; $nbre++)
	{
		$tabFocus[$nbre]='menu2Item';
	}

	$tabFocus[$position]='menu2ItemSelect '.$tabFocus[$position];
	for ($nbre = 0; $nbre<=3; $nbre++)
	{
		$tabFocus[$nbre]="'".$tabFocus[$nbre]."'";
	}
	return $tabFocus;
}

function layoutPane($allArrayData, $page, $limit=10, $colnum=3)
{
	// fonction de mise en forme en '$colnum' nombre de colonne
	//
	// initialisation des variables
	for ($iter=0; $iter<$colnum; $iter++)
	{
		$arrayDataByCol[$iter] = array_slice($allArrayData, ($limit * $colnum * ($page - 1) + ($iter * $limit)), $limit);
	}

	return $arrayDataByCol;
}

function numberOfPage($allArrayData, $listMax)
{
	// Calcule le nombre de page à prévoir pour l'affichage des fichiers photos
	// allArrayData = Array des fichiers à compter
	// listMax = Nombre max d'item par page (taille des colonnes x nombre de colonnes)
	if (count($allArrayData) % $listMax == 0)
	{
		return intdiv(count($allArrayData), $listMax);
	}
	else
	{
		return (intdiv(count($allArrayData), $listMax) + 1);
	}
}

function debug_to_console($info, $data)
{
    if ( is_array( $data ) ) {
        $output = json_encode($data);
		}
		else {
			$output = $data;
		}

		echo "<script>console.log( '" . $info . " : " . $output . "' );</script>";
}

function debugToFile($info, $data)
{
    if ( is_array( $data ) ) {
        $output = json_encode($data);
		}
		else {
			$output = $data;
		}

		$file = getenv("HTTP_DEBUG_FILE");
	  $handle = fopen($file, 'a');
	  fwrite($handle, date('Y-m-d H:i:s')." # ". $info . " : " . $output . " ##\n");
	  fclose($handle);
}

function resetSelection ($fileTable) {

}
//	$strScript='<script type="text/javascript">';
//	foreach ($fileTable as $key => $value){
//
//    var options = {
//      valueNames: [ 'fDate', 'ftExt', 'ftInt', 'fhExt', 'fhInt' ]
//    };

//    var userList = new List('tableMeteo', options);

//</script>
//	if ($fileTable.length<1) {return;}
//	fileTable.forEach(function(element) {
//		document.getElementById(element).classList.add('selected');
//	})
//	document.getElementById(fileTable.length-1).classlist.add('highSelected');
//	document.getElementById(fileTable.length-1).classlist.remove('selected');

//function layoutDisplayThumb($strInput,$addString) {
//	$strInput .= PHP_EOL;
//	return ($strInput.'<img src="public/cameraShots/'.$addString.'">');
//}


function convertirTimezone($time, $deTz = "GMT", $versTz = "Europe/Brussels")
{
// timezone by php friendly values
	$date = new DateTime($time, new DateTimeZone($deTz));
    $date->setTimezone(new DateTimeZone($versTz));
    $time= $date->format('Y-m-d H:i:s');
    return $time;
}

// function motionSettings ($post, $dbMngSettings)
// {
// 	//
// 	// // validate settings and store it as properties
// 	// $settingsInterface = new SettingsInterface($post, $dbMngSettings);
// 	// // update DB with new values
// 	// $dbMngSettings -> updateValues($settingsInterface -> getAllSettings());
// 	// transpose view settings in Motion settings and store it as properties
// 	$motionInterface = new MotionInterface($post, $dbMngSettings);
// 	// create object for managing motion.conf
// 	$motionMng = new MotionManager();
// 	// back-up motion config file (motion.conf.back)
// 	$motionMng -> backUpMotion();
// 	// update settings in config file (motion.conf)
// 	$motionMng -> setAllSettings($motionInterface -> getAllSettings());
// 	// restart motion daemon for applying settings
// 	$motionMng -> restartMotion();
// }

// function doMotionSettings ($inputList)
// {
// 	// TODO
// 	// general function for manage motion settings
// 	$sendMailPath = '/var/www/html/public/bash/motionSendMail.sh';
// 	$dbMngSettings = new DbMngSettings();
// 	$motionMng = new MotionManager();
// 	// $dbMngSettings->_table = 'configAlias';
//
//  	foreach ($inputList as $key => $value)
// 	{
// 		// check validity of $value
// 		if (! $dbMngSettings-> validateValue($key, $value))
// 		{
// 			// continue ;
// 			throw new Exception('Paramètre non valide : '. $key .' - valeur : '.$value);
// 		}
//
//
// 		$dbMngSettings-> modifySetting ($key, $value);
//
// 		if ($key == 'on_motion_detected')
// 		{
// 			$tmpStr =  '" send '.$key .' - '. $value.'"';
// 			$output = shell_exec('echo '. $tmpStr .' >> /var/www/debug.log');
// 			$motionMng-> setSendMail($key, $value);
// 			$motionMng-> setSetting($key, $sendMailPath);
// 		}
// 		else
// 		{
// 			$motionMng-> setSetting ($key, $value);
// 		}
// 	}
// }
