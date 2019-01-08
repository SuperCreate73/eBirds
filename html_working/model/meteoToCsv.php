<?php
include ('scripts/php/helpers.php');
include ('scripts/php/connexionDB.php');

function array_to_csv_download($array, $filename, $delimiter=";") {
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="'.$filename.'";');
    $f = fopen('php://output', 'w');
    foreach ($array as $line) {
        fputcsv($f, $line, $delimiter);
    }
}



try{
  $fichier_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $sql = "SELECT * FROM meteo ORDER BY dateHeure DESC;";
  $resultat = $fichier_db->query($sql);
}
catch(PDOException $e){
  echo $e->getMessage();
}

$tableau = array();

foreach ($resultat as $rangee){
  $dateCorr = convertirTimezone($rangee['dateHeure'], "GMT", "Europe/Brussels");
  $arr = array($dateCorr, $rangee['tempExt'], $rangee['tempInt'],$rangee['humExt'],$rangee['humInt']);
  array_push($tableau, $arr);
}

array_to_csv_download($tableau,"donnees_meteo.csv");



 ?>
