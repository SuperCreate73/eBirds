<?php
session_start();
if (isset($_SESSION['nom'])){
  $nom = $_SESSION['nom'];
$logout="<br><span id='logout' onclick='logout()'>bonjour, ".$nom." !<br>Se déconnecter</span>";}

// Si la variable de session "nom" existe, c'est que le visiteur est connecté avec un bon mot de passe.
// Dans ce cas, on peuple la variable $logout avec le code html ci dessus qui sera rendu dans la page
// visitée dans le header et crée un lien pour pouvoir se déconnecter.
// Le lien appelle une fonction javascript "logout()", inclue dans la page html et qui permet de se déconnecter
// en ajax, sans devoir recharger la page.  Voir dans le fichier scripts/JS/assistants.js pour cette fonction.

//// Si la session n'est pas ouverte, la variable $logout est vide et donc rien n'est affiché.



?>
