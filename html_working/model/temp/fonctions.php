<?php

function setFocus($position) {

	$tabFocus[0]='prems menuitem';
	for ($nbre = 1; $nbre<=2; $nbre++) {
		$tabFocus[$nbre]='menuitem';
	}
	$tabFocus[3]='der menuitem';
	$tabFocus[$position].=' selected';
	for ($nbre = 0; $nbre<=3; $nbre++) {
		$tabFocus[$nbre]="'".$tabFocus[$nbre]."'";
	}
	return $tabFocus;
}

function setFocusMen2($position) {

	for ($nbre = 0; $nbre<=3; $nbre++) {
		$tabFocus[$nbre]='itemMS';
	}
	$tabFocus[$position]='s_select '.$tabFocus[$position];
	for ($nbre = 0; $nbre<=3; $nbre++) {
		$tabFocus[$nbre]="'".$tabFocus[$nbre]."'";
	}
	return $tabFocus;
}

//function scanDirectory($directory,$limit=100){
// fonction qui renvoie un tableau des fichiers du répertoire en paramètre 
//	$myDirectory = opendir($directory) or die('Erreur');
//	$count=0;
//	$output=array();
// 	while(false !== ($entry = readdir($myDirectory)) && $count<$limit) 
// 	if(!is_dir($directory.'/'.$entry) && $entry != '.' && $entry != '..') {
//              		$output[] = $entry;
//          	}
//		$count++;
//	}
//	closedir($myDirectory);
//	return $output;
//}
 
function layoutPane($allArrayData,$page,$path,$colnum=3,$limit=30){
	// fonction de mise en forme en '$colnum' nombre de colonne
	//
	$arrayLayout = array();
	$arrayColumn=0;
	$arrayBegin=$limit*$colnum*($page-1);
	$arrayData=array_slice($allArrayData,$arrayBegin,($limit*$colnum));
	for ($iter=1; $iter<=$colnum; $iter++) {
		$arrayLayout[]= " ";
	}
	foreach ($arrayData as $key => $value) {
		if ($key % $limit == 0 && $key > 0) {
			$arrayColumn+=1;
		}	
		$arrayLayout[$arrayColumn]=layoutDisplay($arrayLayout[$arrayColumn],$path.$value);
	}
	return $arrayLayout;
}

function layoutDisplay($strInput,$fileToAdd) {
	$strInput .= PHP_EOL;
	$pathParts = pathinfo($fileToAdd);
	//	return ($strInput.'<div class="fileList '.$pathParts["extension"].'" extension="'.$pathParts["extension"].'" onclick="selectFiles(this)" id="'.$pathParts["filename"].'">'.$pathParts["filename"].'</div>');
	$strInput.= '<div class="fileList" extension="'.$pathParts["extension"].'" onclick="selectFiles(this)" id="'.$pathParts["filename"].'">'.PHP_EOL;
	$strInput.= '<div class="fileExtension '.$pathParts["extension"].'"></div>'.PHP_EOL;
	$strInput.= '<div class="fileItem">'.$pathParts["filename"].'</div>'.PHP_EOL;
	$strInput.= '<div class="fileTag tag"></div>'.PHP_EOL;
	$strInput.= '<div class="fileComment comment"></div>'.PHP_EOL.'</div>';

	return ($strInput);
}

function numberOfPage($allArrayData) {
	if (count($allArrayData) % 90 == 0) {
		return intdiv(count($allArrayData),90);
	}
	else {
		return (intdiv(count($allArrayData),90)+1);
	}
}

function layoutPaneThumb($arrayData){
	// fonction de mise en forme en '$colnum' nombre de colonne
	//
	$strLayout = '';
	foreach ($arrayData as $value) {
		echo $value;
		$strLayout.=PHP_EOL.'<a href="public/cameraShots/'.$value.'"></a>';
	}
	return $strLayout;
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
