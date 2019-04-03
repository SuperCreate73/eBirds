<?php

require_once("model/DbManager.php");

class User extends DbManager {

	public function setUser ($login,$password) {
		// Création d'un nouvel utilisateur
		//
		echo $login.$password;
		$password= md5(htmlspecialchars($password));
		if ($this->checkUser($login)) {
			$this->modifyUser($login,$password);
		}
		else {
			$db = $this->dbConnect();
			// $sql=("INSERT INTO users (login, password) VALUES ('$login','$password');");
			// $resultat = $db->query($sql);
			$stmt=$db->prepare("INSERT INTO users (login, password) VALUES (:Login, :Password)");
			$result=$stmt->execute(array(
					'Login' => $login,
					'Password' => $password));
			// $resultat->execute(array($this->clean($login),$password));
		}
	}

	public function modifyUser ($login,$password) {
		// Modification d'un utilisateur
		//
			$db = $this->dbConnect();
			$resultat=$db->prepare("UPDATE users SET password = ? WHERE (user=?)");
			$resultat->execute(array($this->clean($login),$password));
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

		$db=$this->dbConnect();
		// On "hashe" en md5 (type d'encryption) le mot de passe avant de faire la requête.
  		// En effet, les mots de passe sont stockés encryptés dans la DB.
		if($password) {
			$password= md5(htmlspecialchars($password));
		}
		else {
			$password = '*';
		}
		$resultat=$db->prepare("SELECT count(*) as nbres FROM users WHERE login=? AND password=?");
  		// On utilise la fonction "clean" définie dans la classe mère pour filtrer et éventuellement ajouter des caractères
		// d'échappement dans les informations transmises par le formulaire (pour éviter un problèmede sécurité appelé "injection SQL")
		$resultat->execute(array($this->clean($login),$password));

  		$row = $resultat->fetch();

  		// Si nbres contient "1" c'est qu'il y a bien une ligne avec mot de passe et identifiant associés
  		if($row['nbres'] == 1) {
  			return TRUE; // La fonction de vérification renvoie "TRUE"
		}
		else { // Autrement (à priori nbre == 0), il n'y a pas de ligne avec ce login et mot de passe
    		return FALSE; // la fonction renvoie "FALSE"
		}
	}
}
