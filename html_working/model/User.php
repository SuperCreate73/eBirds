<?php

require_once("model/DbManager.php");

class User extends DbManager {

	public function setUser ($login,$password) {
		// Create new user or update existing one if already nown in DB
		//
		// password hash (MD5)
		$passmd5 = $this->md5Hash($password);
		// If user nown, modifying password

		$result=$this->checkUser($login);
		if ($result) {
			$this->modifyUser($login,$passmd5);
		}
		else {
			// Create new user
			$db = $this->dbConnect();
			$stmt=$db->prepare("INSERT INTO users (login, password) VALUES (:Login, :Password)");
			$result=$stmt->execute(array(
					'Login' => $login,
					'Password' => $passmd5));
		}
	}

	public function modifyUser ($login,$password) {
		// Modify user
		//
			$db = $this->dbConnect();
			$stmt=$db->prepare("UPDATE users SET password = :Password WHERE (login = :Login)");
			$resultat=$stmt->execute(array(
				'Login'	=>	$this->clean($login),
				'Password' => $password));
	}

	public function delUser ($login) {
		// Delete user
		//
			$db = $this->dbConnect();
			$stmt=$db->prepare("DELETE FROM users WHERE (login = :Login) ");
			$resultat=$stmt->execute(array(
				'Login'	=>	$this->clean($login),
				));
			// If no more users, create default one admin admin
			if ($this->countUser() == 0) {
				$this->setUser('admin','admin');
			}
	}

	public function getUsers () {
		// Donne la liste des utilisateurs du nichoir
		//
			$db = $this->dbConnect();
			$sql = "SELECT login FROM users;";
			$resultat = $db->query($sql);
			$list = $resultat->fetchall();
			return($list);
	}

	public function logUser ($login) {
		// Enregistre le login dans la variable de session 'nom' pour garder en mémoire l'enregistrement de l'utilisateur
		//
		$_SESSION['nom']=$login;
	}

	public function unlogUser () {
		// Efface les variables de session 'message' et 'nom' utilisées pour vérifier si un utilisateur est enregistré
		//
		unset($_SESSION['message']);
		unset($_SESSION['nom']);
	}

	public function checkUser($login,$password = NULL) {
		// Vérification du login et du mot de passe de l'utilisateur dans la table Users
		//
		$db=$this->dbConnect();
		// On "hashe" en md5 (type d'encryption) le mot de passe avant de faire la requête.
  	// En effet, les mots de passe sont stockés encryptés dans la DB.
		// On utilise la fonction "clean" définie dans la classe mère pour filtrer et éventuellement ajouter des caractères
		// d'échappement dans les informations transmises par le formulaire (pour éviter un problèmede sécurité appelé "injection SQL")
		if($password) {
			$stmt=$db->prepare("SELECT count(*) as nbres FROM users WHERE login=? AND password=?");
			$stmt->execute(array($this->clean($login), $this->md5Hash($password)));
		}
		else {
			$stmt=$db->prepare("SELECT count(*) as nbres FROM users WHERE login=?");
			$stmt->execute(array($this->clean($login), ));
		}

  	$row = $stmt->fetch();

  	// Si nbres contient "1" c'est qu'il y a bien une ligne avec mot de passe et identifiant associés
  	if($row['nbres'] == 1 ) {
  		return TRUE; // La fonction de vérification renvoie "TRUE"
		}
		else { // Autrement (à priori nbre == 0), il n'y a pas de ligne avec ce login et mot de passe
    	return FALSE; // la fonction renvoie "FALSE"
		}
	}

	public function countUser($login = NULL) {
		// compte le nombre d'utilisteurs dans la table Users
		//
		$db=$this->dbConnect();

		if ($login) {
				$stmt=$db->prepare("SELECT count(*) as nbres FROM users WHERE (login=:Login)");
				$resultat=$stmt->execute(array(
					'Login'	=>	$this->clean($login),
					));
		}
		else {
			$stmt=$db->prepare("SELECT count(*) as nbres FROM users");
			$resultat=$stmt->execute();
		}
  	$row = $stmt->fetch();
		return $row['nbres']; // La fonction de vérification renvoie "TRUE"
	}
}
