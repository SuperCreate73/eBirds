<?php
try{

$fichier_db = new PDO('sqlite:/var/www/nichoir.db');
$fichier_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$result = $fichier_db->query('SELECT * FROM users');
foreach($result as $rangee){
echo "nom: ".$rangee['login']." mot de passe: ".$rangee['password']."\n";
}
}
catch(PDOException $e){
echo $e->getMessage();
}
?>
