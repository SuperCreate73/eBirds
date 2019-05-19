<?php
require_once('controller/fonctions.php');
//require_once('model/PiManager.php');
//require_once('model/FileManager.php');
//require_once('model/ImageFileManager.php');
//require_once('model/User.php');

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

function motionSettings () {
	// intermediate function for recursive use of doMotionSettings
	$motion = new MotionManager();
	$motion -> backUpMotion();
	doMotionSettings($_POST);
	$motion -> restartMotion();
}

function doMotionSettings ($inputList) {
	// TODO
	// general function for manage motion settings
	$sendMailPath = '/var/www/html/public/bash/motionSendMail.sh';
	$config = new DbMngSettings();
	$motion = new MotionManager();
	$config->_table = 'configAlias';
	foreach ($inputList as $key => $value) {
		if ($config->keyExist('alias = "'.$key.'" AND aliasValue = "'.$value.'"')) {
			doMotionSettings($config -> getSettingFromAlias($key, $value));
			continue ;
		}
		// check validity of $value
		if (! $config-> validateValue($key, $value)) {
			continue ;
		}
		if ($value == $config-> getSettingValue($key))	{
			continue ;
		}
		$config-> modifySetting ($key, $value);
		if ($key = 'on_motion_detected'){
			$motion-> setSendMail($key, $value);
			$motion-> setSetting($key, $sendMailPath);
		}
		else {
			$motion-> setSetting ($key, $value);
		}
	}
}

// function motionDetect($email) {
// 	// configure mail send on movement detection
// 	// TODO to delete, no more used
// 	//require_once('model/DbMngSettings.php');
// 	//require_once('model/MotionManager.php');
//
// 	// ecriture dans la DB, table des settings
// 	$config = new DbMngSettings();
// 	$oldMail = $config -> getSettingValue ("motionEmail");
// 	if ($oldmail == $email || $email == "") {
// 		return ;
// 	}
// 	$config -> addSetting ("motionEmail", $email);
//
// 	// configuration de motion
// 	$motion = new MotionManager();
// 	$motion -> backUpMotion();
// 	$motion -> setSendMail($email);
// 	$motion -> restartMotion();
// }
