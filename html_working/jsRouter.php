<?php

require_once("controller/jsController.php");

try
{
	session_start();
	$action = (isset($_GET['action'])) ? htmlspecialchars($_GET['action']) : '';
	$parameter1 = (isset($_GET['param1'])) ? htmlspecialchars($_GET['param1']) : '';
	$parameter2 = (isset($_GET['param2'])) ? htmlspecialchars($_GET['param2']) : NULL;

	$actionArray = array (
		'shutdown' => 'shutdown',
		'reboot' => 'reboot',
		'upgrade' => 'upgrade',
		'distUpgrade' => 'distUgrade',
		'deletefiles' => 'deleteFiles',
		'download' => 'zipUpload',
		'changeName'=>'changeName',
		'viewselection'=>'viewSelection',
		'saveUser' => 'saveUser',
	);

	if ( ! array_key_exists($action, $actionArray)) {
		throw new Exception('Action non valide !');
	}

	if ($action == 'changeName' ) {
		$actionArray[$action]($parameter1);
	}
	elseif ($action == 'viewselection') {
		$actionArray[$action]('selectionArray');
	}
	else {
		$actionArray[$action]($parameter1,$parameter2);
	}
}

catch(Exception $e) {
   	// S'il y a eu une erreur, alors...
    echo 'Erreur : ' . $e->getMessage();
}
