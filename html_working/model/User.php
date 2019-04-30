<?php

require_once("model/DbManager.php");

class User extends DbManager {

	public function __construct() {
		$this->_table = "users";
	}

	public function setUser ($login,$password) {
		// Create new user or update existing one if already nown in DB
		//
		// password hash (MD5)
		$passmd5 = $this->md5Hash($password);
		// If user nown, modifying password

		$result=$this->checkLogin($login);
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
			$list = $this->getAll('login');
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

	public function checkUser($login,$password) {
		// Vérification du login et du mot de passe de l'utilisateur dans la table Users
		//
		// On "hashe" en md5 (type d'encryption) le mot de passe avant de faire la requête.
  	// En effet, les mots de passe sont stockés encryptés dans la DB.
		// On utilise la fonction "clean" définie dans la classe mère pour filtrer et éventuellement ajouter des caractères
		// d'échappement dans les informations transmises par le formulaire (pour éviter un problèmede sécurité appelé "injection SQL")
		$where = "login = '".$this->clean($login)."' AND password = '".$this->md5Hash($password)."'" ;
		$result = $this-> keyExist($where);
		return $result;
	}

	public function checkLogin($login) {
		// Vérification del'existance du login dans la table Users
		//
		$where = "login = '".$login."'" ;
		$result = $this-> keyExist($where);
		return $result;
	}

	public function countUser($login = NULL) {
		// compte le nombre d'utilisteurs dans la table Users
		//
		$db=$this->dbConnect();

		if (!is_null($login)) {
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
