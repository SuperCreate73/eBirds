<?php

function setFocus($position) {
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
	for ($nbre = 0; $nbre<=3; $nbre++) {
		$tabFocus[$nbre]='';
	}
	$tabFocus[$position]='selected';
	return $tabFocus;
}

function setFocusMen2($position) {

	for ($nbre = 0; $nbre<=3; $nbre++) {
		$tabFocus[$nbre]='menu2Item';
	}
	$tabFocus[$position]='menu2ItemSelect '.$tabFocus[$position];
	for ($nbre = 0; $nbre<=3; $nbre++) {
		$tabFocus[$nbre]="'".$tabFocus[$nbre]."'";
	}
	return $tabFocus;
}

function layoutPane($allArrayData,$page,$limit=10,$colnum=3){
	// fonction de mise en forme en '$colnum' nombre de colonne
	//
	// initialisation des variables
	for ($iter=0; $iter<$colnum; $iter++) {
		$arrayDataByCol[$iter]=array_slice($allArrayData,($limit*$colnum*($page-1)+($iter*$limit)),$limit);
	}
	
	return $arrayDataByCol;
}

function numberOfPage($allArrayData,$listMax) {
	// Calcule le nombre de page à prévoir pour l'affichage des fichiers photos
	// allArrayData = Array des fichiers à compter
	// listMax = Nombre max d'item par page (taille des colonnes x nombre de colonnes)
	if (count($allArrayData) % $listMax == 0) {
		return intdiv(count($allArrayData),$listMax);
	}
	else {
		return (intdiv(count($allArrayData),$listMax)+1);
	}
}

function resetSelection ($fileTable) {

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
}

//function layoutDisplayThumb($strInput,$addString) {
//	$strInput .= PHP_EOL;
//	return ($strInput.'<img src="public/cameraShots/'.$addString.'">');
//}


function convertirTimezone($time, $deTz = "GMT", $versTz = "Europe/Brussels") {
// timezone by php friendly values
	$date = new DateTime($time, new DateTimeZone($deTz));
    $date->setTimezone(new DateTimeZone($versTz));
    $time= $date->format('Y-m-d H:i:s');
    return $time;
}

