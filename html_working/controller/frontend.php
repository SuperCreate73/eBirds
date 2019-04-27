<?php

require_once('controller/fonctions.php');
require_once('model/DbMngData.php');
require_once('model/User.php');
require_once('model/FileManager.php');
require_once('model/ImageFileManager.php');

function homePage($nom) {
	// Contrôleur de la HomePage du site
	$dbMngData = new DbMngData();
	$tempExt = $dbMngData -> tempExt();
	$tempInt = $dbMngData -> tempInt();
	$humExt = $dbMngData -> humExt();
	$humInt = $dbMngData -> humInt();
	$dateHeure = $dbMngData -> dateHeure();
	$entrees = $dbMngData -> entrees();
	$sorties = $dbMngData -> sorties();
	$visites = $dbMngData -> visites();
	$tabFocus=setFocus(0);

	require('view/viewHomePage.php'	);
}

function graphique($nom) {
	$dbMngData = new DbMngData();
	$tableau = $dbMngData -> setDataGraph(100);
	$tabFocus=setFocus(1);
	$tabFocusMen2=setFocusMen2(0);

	require('view/viewGraphique.php');
}

function photoList($nom, $none ,$mvtPage) {
	$listMax=25;
	$fileManager= new FileManager('jpg');
	$fileList=$fileManager -> setFileList();
	$numberOfPage = numberOfPage($fileList,$listMax*3);
	$page=(isset($_SESSION['listeCourante'])) ? $_SESSION['listeCourante'] : 1;
	if ($mvtPage == 'next') {
		$page=($page==$numberOfPage) ? $page : ($page+1);
	}
	elseif ($mvtPage == 'previous') {
		$page=($page==1) ? $page : ($page-1);
	}
	elseif ($mvtPage == 'first') {
		$page=1;
	}
	elseif ($mvtPage == 'last'){
		$page=$numberOfPage;
	}
	$_SESSION['listeCourante'] = $page;
	$explorerPane = layoutPane($fileList,$page,$listMax);
	if (isset($_SESSION['selectionCourante'])) {
		$selectionCourante=$_SESSION['selectionCourante'];
		unset($_SESSION['selectionCourante']);
	}
	else { $selectionCourante='';}
	$tabFocus=setFocus(1);
	$tabFocusMen2=setFocusMen2(2);

	require('view/viewPhotoList.php');
}

function photoThumb($nom, $action) {
	if ($action == 'table') {
		$fileList=json_decode($_POST['valueTable']);
		foreach ($fileList as $item) {
			$item .= '.'.'jpg';
		}
//		$_SESSION['selectionCourante']=$_POST['valueTable'];
	}
	else {
		$fileManager= new FileManager();
		$fileList=$fileManager -> setFileList();
//		if (isset($_SESSION['selectionCourante'])) {
//			unset($_SESSION['selectionCourante']);
//		}
	}
	$tabFocus=setFocus(1);
	$tabFocusMen2=setFocusMen2(3);

	require('view/viewPhotoThumb.php');
}

function tableData($nom) {
	$dbMngData = new DbMngData();
	$tableau = $dbMngData -> setDataTable(100);
	$tabFocus=setFocus(1);
	$tabFocusMen2=setFocusMen2(1);
	require('view/viewTable.php');
}

function information($nom) {
	$tabFocus=setFocus(2);

	require('view/viewInformations.php');
}

function reglages($nom) {
	$tabFocus=setFocus(3);

	$user = new User();
	$users = $user->getUsers();
	if (! $users) {
		//impossible de charger les utilisateurs
		$users="Unable to load users";
	}
		require('view/viewReglages.php');
}

function login($message) {
	if ($_SESSION['pageCourante']=='reglages') {
		$tabFocus=setFocus(3);
	}
	else {
		$tabFocus=setFocus(0);
	}
	$nom = '';

	require('view/viewLogin.php');
}

function loginVerify($nom, $password) {

	$user = new User();
	if ($user->checkUser($nom,$password)) {
		//Si l'utilisateur est trouvé On recharge la page précédant le login
		$user->logUser($nom);
		header('Location: index.php?page='.$_SESSION['pageCourante']);
		// On recharge la page précédant le login.
	}
	else {
		$_SESSION['message']='Impossible de valider votre identification, veuillez réessayer !';
    	header('Location: index.php?page=login'); // On recharge la page de login
	}
}

function logOut() {
	$user = new User();
	$user->unlogUser();
	header('Location: index.php?page='.$_SESSION['pageCourante']);

	// On supprime la variable de session 'nom' pour déconnecter l'usager et on redirige vers la dernière page consultée
}

function doReglages() {

	if (htmlspecialchars($_POST['nom'])=='') {
		//update parameters
	}
}
