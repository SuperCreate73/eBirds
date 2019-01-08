<?php
// Script à inclure en haut des pages html pour les sécuriser.
session_start(); // On démarre la session
if (!isset($_SESSION['nom']))
	{ // Si la session ne contient pas la variable "nom"
  	header('Location: login.php'); // On redirige vers la page de login.
  	exit(); // Fonction exit pour arrêter l'exécution de la suite du script
	}
else
	{ // dans le cas contraire.
  	$nom = $_SESSION['nom']; // On récupère la variable de session "nom" dans une variable ordinaire de php.
  	$logout="<br><span id='logout' onclick='logout()'>bonjour, ".$nom." !<br>Se déconnecter</span>";
  	// On crée une variable "$logout" qui contient l'html pour le lien de déconnexion et saluer l'utilisateur
}

