<?php

class FileManager {
	// Classe qui gère les fichiers sur le serveur
	// Attention, nécessite le droit d'exécution sur les fichiers et répertoires !

//Attributs:
	private $_filePath = '/var/www/html/public/cameraShots/' ; //
	private $_fileMask;
	private $_fileCount;
	private $_fileList;
//---------------------------------------------

//Méthodes:
	public function __construct($inputMask = NULL) {
	// méthode appelée lorsque la classe est instanciée (= un objet est créé à partir de cette classe)
		// chequer et enregistrer le filePath
		//
		// Initialiser le fileMask
		$this->_fileMask=$inputMask;

		//initialiser la liste de fichier
//		$this->_fileList=$this->setFileList($this->_filePath,$this->_fileMask);

		// Initialiser le compteur fileCount
//		$this->_fileCount=$this->setFileCount($this->_fileList);
	}

  	// Liste des getters : fonctions permettant de récupérer les valeurs des attributs privés
//  	public function fileCount() {return $this->_fileCount;}
//  	public function fileList() {return $this->_fileList;}
  	public function filePath() {return $this->_filePath;}
	// ----------------------------------------------------

  	// Autres méthodes de la classe

	public function setFileList() {
		//Initialise la liste de fichiers correspondant au 'inputMask' dans le répertoire1 donné
		$inputPath=$this->_filePath;
		$fileMask=$this->_fileMask; // unable to pass the mask to the function
		if (isset($fileMask)) {
			return array_filter(scandir($inputPath),
				function($fileTest) {
					return ($fileTest !="." && $fileTest != ".." && $fileTest  != ".ini" && pathinfo($fileTest)['extension']== 'jpg');
				}
			);
		}
		else {
			return array_filter(scandir($inputPath),
				function($fileTest) {
					return ($fileTest !="." && $fileTest != ".." && $fileTest  != ".ini");
				}
			);
		}
	}

	public function deleteFiles($fileList) {
		// Efface les fichiers donnés en arguments

		try {
			for ($i = 0; $i <= count($fileList); $i+=10) {
				$fileListSlice = array_slice($fileList, $i,10);
				$fileString = "";
				foreach ($fileListSlice as $file) {
					// Formattage du tableau
					$fileToDelete = $this->_filePath.$file.".jpg";
					$fileString = $fileString." ".$fileToDelete;
				}
				$output = shell_exec('sudo rm'. $fileString );
			}
		 }

		 catch (Exception $e) {
			 $output = shell_exec('echo "Erreur :'. $e .'" >> /var/www/debug.log');
		 }
		 return ;
	}

	public function zipFiles($fileList) {
		// compresse les fichiers donnés en arguments
		// Efface l'archive existante si elle existe
		if (is_file('Archive.zip')) {
			$openedPath = opendir('./');
			unlink('Archive.zip');
			closedir($openedPath);
		}
		// création de l'archive
		$zip = new ZipArchive();

		if ($zip -> open('Archive.zip', ZipArchive::CREATE) == True) {
			if (count($fileList) == 0) {
				$fileList = $this->setFileList();
			}
			foreach ($fileList as $file) {
				if (!$zip->addFile($this->_filePath.$file.'.jpg', $file.'.jpeg')) {
					throw new Exception ('Unable to add file "'.$this->_filePath.$file.'.jpg" to archive');
				}
			}
			$zip->close();

			header('Content-Transfer-Encoding: binary'); //Transfert en binaire (fichier).
			header('Content-Disposition: attachment; filename="Archive.zip"'); //Nom du fichier.
			header('Content-Length: '.filesize('Archive.zip')); //Taille du fichier.
			readfile('Archive.zip');
			return 'Archive.zip';
		}
		else {throw new Exception('Unable to create archive');}
	}

	private function setFileCount($fileList) {
		// Compte le nombre de fichiers en paramètres
		return count($fileList);
	}

	private function createDirectory ($inputPath) {
		// Création du répertoire en paramètre
		if (!is_dir($inputPath)) {
			mkdir($inputPath);
		}
	}

	private function checkPath($inputPath) {
		// vérifie l'existance du chemin d'accès en paramètre
		return realpath($inputPath);
	}

}
