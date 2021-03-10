<?php

$dossier = "motion/";
$fichiers = glob($dossier."*.mp4");
foreach($fichiers as $fichier)
{
	echo "<a href=".$fichier.">".basename($fichier)."</a><br>";
}
echo "gotcha";

