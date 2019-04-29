<?php

require_once("controller/frontend.php");

try	{	// instruction permettant de récupérer les erreurs éventuelles dans l'instruction 'catch'

	// Initialisation de la session et des variables
	session_start();	// active la session sur le serveur (accès aux variables de session)

	// variable de session 'nom'.
	// Vérification de l'état de login du visiteur en vérifiante la valeur de la

	// Si la variable de session "nom" existe, c'est que le visiteur est connecté
	// avec un mot de passe valide. Dans ce cas, on peuple la variable $logout
	// avec le code html ci dessus qui sera rendu dans le header de la page
	// visitée et crée un lien pour pouvoir se déconnecter.
	// Le lien appelle une fonction javascript "logout()", inclue dans la page
	// html et qui permet de se déconnecter en ajax, sans devoir recharger la
	// page.  Voir dans le fichier scripts/JS/assistants.js pour cette fonction.
	//
	// Si la session n'est pas ouverte, la variable $logout est vide et donc rien n'est affiché.

	$nom = (isset($_SESSION['nom'])) ? htmlspecialchars($_SESSION['nom']) : '';
	$page = (isset($_GET['page'])) ? htmlspecialchars($_GET['page']) : 'homepage'; //page à générer
	$action = (isset($_GET['action'])) ? htmlspecialchars($_GET['action']) : ''; // Action à faire
	$parameter1 = (isset($_GET['param1'])) ? htmlspecialchars($_GET['param1']) : ''; // Paramètre 1

	// TODO - harmoniser les noms de pages et des fonctions et remplacer le dico par la liste
	$pagesArray = array(
		'graphique' => 'graphique',
		'tableData' => 'tableData',
		'photoList' => 'photoList',
		'photoThumb' => 'photoThumb',
		'information' => 'information',
		'reglages' => 'reglages', //tomodify with test is already connected
		'login' => 'login', //tomodify with test is already connected
		'logout'=> 'logout',
		'loginVerify' => 'loginVerify',
		'doreglages' => 'doReglages',
		'homepage' => 'homePage',
	);

	// renvoie une erreur si la page n'est pas dans pagesArray
	//if ( ! in_array ($page, $pagesArray) ) {
	if ( ! array_key_exists($page, $pagesArray)) {
		throw new Exception('Page non valide !');
	}
		
	if ($page == 'reglages' && $nom == '' ) {
		//
		//	if ($nom == '') {
		$_SESSION['pageCourante'] = $page;
		$page='login';
	}

	if ($page == 'login') {			// ------------------ Login
		if(isset($_SESSION['message'])){
			$message = $_SESSION['message'];
		}
		else {
			$message = "L'accès aux réglages du nichoir nécessite un nom d'utilisateur et un mot de passe.<br/><br/> Merci de compléter ce formulaire :";
		}
		login($message);
	}

	elseif ($page == 'loginVerify') {	// ------------------ Login Verify
		loginVerify($_POST['login'],$_POST['passe']);
	}

	else {
		// initialisation de la variable de session pageCourante
		$_SESSION['pageCourante'] = $page;
		// appel de la fonction adhoc
		$pagesArray[$page]($nom, $action, $parameter1);
	}
}


catch(Exception $e) {
   	// S'il y a eu une erreur, alors...
    echo 'Erreur : ' . $e->getMessage();
}
