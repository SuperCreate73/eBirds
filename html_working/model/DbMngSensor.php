<?php

class DbMngSensor extends DbManager {

	private $_tempExt;
	private $_humExt;
	private $_tempInt;
	private $_humInt;
	private $_dateHeure;
	private $_entrees;
	private $_sorties;
	private $_visites;

	public function __construct() {

		$this->setTempHum();
		$this->setEntrees();
		$this->setSorties();
		$this->setVisites();
	}

  // Liste des getters : fonctions permettant de récupérer les valeurs des attributs privés

  public function tempExt() {return $this->_tempExt;}
 	public function tempInt() {return $this->_tempInt;}
  public function humExt() {return $this->_humExt;}
  public function humInt() {return $this->_humInt;}
	public function dateHeure() {return $this->_dateHeure;}
	public function entrees() {return $this->_entrees;}
	public function sorties() {return $this->_sorties;}
	public function visites() {return $this->_visites;}

	private function setTempHum() {

		$_db=$this->dbConnect();
		$sql = "SELECT * FROM meteo ORDER BY dateHeure DESC LIMIT 1;";
  	$resultat = $_db->query($sql);
  	$tableauResultat = $resultat->fetch();
		$this->_tempExt = $tableauResultat['tempExt'];
		$this->_humExt = $tableauResultat['humExt'];
		$this->_tempInt = $tableauResultat['tempInt'];
		$this->_humInt = $tableauResultat['humInt'];
		$this->_dateHeure = $tableauResultat['dateHeure'];
	}

	private function setEntrees() {

		$_db=$this->dbConnect();
		$sql = "SELECT count(*) FROM InOut_IR  WHERE FDatim >= date('now', 'start of day') AND FStatus like 'E%';";
		$resultat = $_db->query($sql);
		$tableauResultat = $resultat->fetch();
  	$this->_entrees = $tableauResultat[0];
	}

	private function setSorties() {

		$_db=$this->dbConnect();
		$sql = "SELECT count(*) from InOut_IR  where FDatim >= date('now','start of day') and FStatus like 'S%';";
		$resultat = $_db->query($sql);
		$tableauResultat = $resultat->fetch();
		$this->_sorties = $tableauResultat[0];
	}


	private function setVisites() {

		$_db=$this->dbConnect();
		$sql = "SELECT count(*) from InOut_IR  where FDatim >= date('now','start of day') and FStatus like 'V%';";
		$resultat = $_db->query($sql);
		$tableauResultat = $resultat->fetch();
		$this->_visites = $tableauResultat[0];
	}

	public function setDataTable($maxDay) {

		$_db=$this->dbConnect();
		// vérification du paramètre d'entrée, si ce n'est pas un nombre entier, mis à 0
		if (!is_int($maxDay)) {
			$maxDay=0;
		}
		// Sélection du nombre de jours définit par $maxDay dans la BD ou sélection de tout si $maxDay=0
		if ($maxDay > 0) {
			// A tester, ne semble pas fonctionner
			//			$sql = "SELECT * FROM meteo WHERE dateHeure >= DATE_SUB(NOW(), INTERVAL " .$maxDay. " DAY) ORDER BY dateHeure ;";
			$sql = "SELECT * FROM meteo WHERE dateHeure >= date('now','-" .$maxDay. " days') ORDER BY dateHeure DESC ;";
		}
		else {
			$sql = "SELECT * FROM meteo ORDER BY dateHeure DESC ;";
		}

		$resultat = $_db->query($sql);

		return $resultat;
	}

	public function setDataGraph($maxDay) {

		$_db=$this->dbConnect();
		// vérification du paramètre d'entrée, si ce n'est pas un nombre entier, mis à 0
		if (!is_int($maxDay)) {
			$maxDay=0;
		}
		// Sélection du nombre de jours définit par $maxDay dans la BD ou sélection de tout si $maxDay=0
		if ($maxDay > 0) {
			$sql = "SELECT * FROM meteo WHERE dateHeure >= date('now','-" .$maxDay. " days') ORDER BY dateHeure ;";
		}
		else {
			$sql = "SELECT * FROM meteo ORDER BY dateHeure ;";
		}

		$resultat = $_db->query($sql);
		return $resultat;
	}

}
