<?php

require_once('model/PiManager.php');
require_once('model/FileManager.php');
require_once('model/User.php');
//require_once('model/ImageFileManager.php');

function shutdown() {
	// éteint le raspberri
	$piManager = new PiManager();
	$piManager->shutdown();
}

function reboot() {
	// redémarre le raspberri
	$piManager = new PiManager();
	$piManager->reboot();
}

function upgrade() {
	//upgrade du système linux
	$piManager = new PiManager();
	$piManager->upgrade();
}

function distUpgrade() {
	// upgrade de la distribution linux du raspberry
	$piManager = new PiManager();
	$piManager->distUpgrade();
}

function changeName($nom){
	// change le nom du raspberry
	$piManager = new PiManager();
	$piManager->changeName($nom);
}

function deleteFiles() {
	// efface les fichiers contenu dans la variable _POST selectionArray
	$fileManager = new FileManager();
	$fileManager->deleteFiles(explode(",",$_POST['selectionArray']));
}

function viewSelection($varName) {
	// met la liste POST dans la variable de session
	$_SESSION[$varName]=$_POST[$varName];
}

function zipUpload() {
	// crée un fichier zip des photos et les uploade
	$fileManager = new FileManager();
	$archive = $fileManager->zipFiles(explode(",",$_POST['selectionArray']));
	return($archive);
}

function saveUser($login,$password) {
	// Create new user or update existing one
	//
	// Validation of input parameters
	if (strlen(trim($login)) < 3){
		throw new Exception("Invalid user name ! ");
		return;
	}
	if (strlen(trim($password)) < 5){
		throw new Exception("Invalid password ! ");
		return;
	}

	//saveUser
	$user = new User();
	$user->setUser($login, $password);
}

function delUser($login) {
	// Revoque user from DB
	// If no more connection login, create default one (admin, admin)
	$user = new User();
	$user->delUser($login);
}
