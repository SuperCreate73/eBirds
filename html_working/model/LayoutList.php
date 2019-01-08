<?php

class LayoutList {
	// Classe qui gère les fichiers sur le serveur
	// Attention, nécessite le droi d'exécution sur les fichiers et répertoires !
	
//Attributs:
	private $_columns = 3; //
	private $_rows = 30;
//---------------------------------------------	

//Méthodes:
//	public function __construct() {
	// méthode appelée lorsque la classe est instanciée (= un objet est créé à partir de cette classe)	
		// chequer et enregistrer le filePath
		//
		// Initialiser le fileMask
//		$this->_fileMask=$inputMask;

		//initialiser la liste de fichier
//		$this->_fileList=$this->setFileList($this->_filePath,$this->_fileMask);

		// Initialiser le compteur fileCount
//		$this->_fileCount=$this->setFileCount($this->_fileList);
	}
	
  	// Liste des getters : fonctions permettant de récupérer les valeurs des attributs privés 
//  	public function fileCount() {return $this->_fileCount;}
//  	public function fileList() {return $this->_fileList;}
	// ----------------------------------------------------

  	// Autres méthodes de la classe
	private function checkPath($inputPath) {
	// vérifie l'existance du chemin d'accès en paramètre	
	
		return realpath($inputPath);
	}

	public function setFileList() {
		//Initialise la liste de fichiers correspondant au 'inputMask' dans le répertoire donné
		$inputPath=$this->_filePath;
		$fileMask=$this->_fileMask; // not used for the moment ...
		return array_filter(scandir($inputPath), function($fileTest) { return ($fileTest !="." && $fileTest != ".."); });
	}		

	private function setFileCount($fileList) {
	// 	
		return count($fileList);
	}
	
	public function deleteFiles($fileList) {
	// Efface les fichiers donnés en arguments
	
		// ouverture du répertoire
		$openedPath = opendir($this->_filePath);

		// parcours du tableau avec les fichiers à effacer
		foreach ($fileList as $file) {
			// association du chemin d'accès et du nom de fichier
			$fileToDelete = $this->_filePath.$file;
            // Les variables qui contiennent toutes les infos nécessaires.
            //$infos = pathinfo($chemin); à utiliser pour d'autres tests sur les fichies : extension, date de création, ...
            //$extension = $infos['extension'];
            //$age_fichier = time() - filemtime($chemin);

			unlink($fileToDelete);
        }
		closedir($openedPath); // On ferme !
	}

	private function createDirectory ($inputPath) {

		if (!is_dir($inputPath)) {
			mkdir($inputPath);
		}
	}
}

