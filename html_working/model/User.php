<?php

require_once("model/DbManager.php");

class User extends DbManager {
	//
	// Class managing 'users' table -> query, modification, add
	//
	public function __construct() {
		// assign value of active table to protected variable '_table',
		// used in DbManager class
		$this->_table = "users";
	}

	public function setUser ($login,$password) {
		// Create new user or update existing one if already registred in DB
		//
		// password hash (MD5)
		$passmd5 = $this->md5Hash($password);
		$login=$this->clean($login);

		// check if user already registred
		$result=$this->checkLogin($login);
		if ($result) {
			// If user already registred, modifying password
			$this->modifyUser($login,$passmd5);
		}
		else {
			// Create new user
			$db = $this->dbConnect();
			$sql = "INSERT INTO users (login, password) VALUES ('".$login."', '".$passmd5."');";
			$stmt = $db->query($sql);
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
			// If no more users, create default user : admin admin
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
		// Le mot de passe est crypté (MD5 hash) avant de faire la requête, car sont stockés
		// cryptés dans la DB. On utilise la fonction "clean" définie dans la classe mère
		// pour filtrer et éventuellement ajouter des caractères d'échappement (pour
		// éviter un problèmede sécurité appelé "injection SQL")
		$where = "login = '".$this->clean($login)."' AND password = '".$this->md5Hash($password)."'" ;
		$result = $this-> keyExist($where);
		return $result;
	}

	public function checkLogin($login) {
		// Vérification de l'existance du login dans la table users
		//
		$where = "login = '".$login."'" ;
		$result = $this-> keyExist($where);
		return $result;
	}

	public function countUser($login = NULL) {
		// compte le nombre d'utilisteurs dans la table Users
		//
		$where = (is_null($login)) ? NULL : "login = '".$login."'";
		$result = $this-> countRecords($where);
		return $result;
	}
}
