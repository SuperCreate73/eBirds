<?php

$dossier = "motion/";
$fichiers = glob($dossier."*.avi");
foreach($fichiers as $fichier){
echo "<a href=".$fichier.">".basename($fichier)."</a><br>";

}
echo "gotcha";

?>
