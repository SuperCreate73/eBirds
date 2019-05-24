<?php

require_once('controller/fonctions.php');
//require_once('model/DbMngData.php');
//require_once('model/User.php');
//require_once('model/FileManager.php');
//require_once('model/ImageFileManager.php');

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

	if ($mvtPage == 'next')
	{
		$page=($page==$numberOfPage) ? $page : ($page+1);
	}
	elseif ($mvtPage == 'previous')
	{
		$page=($page==1) ? $page : ($page-1);
	}
	elseif ($mvtPage == 'first')
	{
		$page=1;
	}
	elseif ($mvtPage == 'last')
	{
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

function reglages($nom, $action=null) {
	if (! is_null($action)) {
		// debug_to_console( "test 2" );
		motionSettings();
	}
	$tabFocus=setFocus(3);

	$user = new User();
	$users = $user->getUsers();
	if (! $users) {
		//impossible de charger les utilisateurs
		$users="Unable to load users";
	}
	$config = new DbMngSettings();
	$on_motion_detected = $config->getSettingValue('on_motion_detected');
	$threshold = $config->getSettingValue('threshold');
	$quality = $config->getSettingValue('quality');
	$ffmpeg_timelapse = $config->getSettingValue('ffmpeg_timelapse');
	$ffmpeg_timelapse_mode = $config->getSettingValue('ffmpeg_timelapse_mode');
	$imageSize = $config->getAliasValue('imageSize');
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
		header('Location: index.php?page=reglages');
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
	header('Location: index.php?page=homepage');

	// On supprime la variable de session 'nom' pour déconnecter l'usager et on redirige vers la dernière page consultée
}

// function doReglages() {
// 	// TODO
// 	// general function for manage motion settings
// 	debug_to_console( "test 2" );
// 	$sendMailPath = '/var/www/html/public/bash/motionSendMail.sh';
// 	$config = new DbMngSettings();
// 	$motion = new MotionManager();
// 	// $config->_table = 'configAlias';
// 	$inputList = $_POST;
//  	foreach ($inputList as $key => $value) {
//
// 		if ($config->keyTest('configAlias', 'alias = "'.$key.'" AND aliasValue = "'.$value.'"'))
// 		{
// 			debug_to_console( "1 doMotionSettings" );
// 			doMotionSettings($config -> getSettingFromAlias($key, $value));
// 			continue ;
// 		}
// 		// check validity of $value
// 		if (! $config-> validateValue($key, $value))
// 		{
// 			debug_to_console( "2 validateValue" );
// 			continue ;
// 		}
// 		// check if same values in DB
// 		if ($value == $config-> getSettingValue($key))
// 		{
// 			debug_to_console( "3 sameValues" );
// 			continue ;
// 		}
// 		debug_to_console( "4 modifySetting" );
// 		$config-> modifySetting ($key, $value);
//
// 		if ($key = 'on_motion_detected')
// 		{
// 			debug_to_console( "5 send mail" );
// 			$motion-> setSendMail($key, $value);
// 			$motion-> setSetting($key, $sendMailPath);
// 		}
// 		else
// 		{
// 			debug_to_console( "6 setSetting" );
// 			$motion-> setSetting ($key, $value);
// 		}
// 	}
// }
