<?php
  	session_start(); // initialisation de la session 
  	unset($_SESSION['message']);
  	unset($_SESSION['nom']);

	// On supprime la variable de session 'nom' pour déconnecter l'usager

