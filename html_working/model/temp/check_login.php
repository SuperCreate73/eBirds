<?php

//**************************************************
// Script qui est appellé pour
// Vérifier l'existence d'un utilisateur et mot de passe correspondant dans la base de données
// Il est appellé et exécuté quand le bouton "submit" du formulaire de connexion est cliqué.
//***************************************************


session_start(); //Initialisation de la Session
if (isset($_POST['login']) && isset($_POST['passe']))
{
  	//Si les variables "login" et "passe" du tableau POST sont existantes
  	//càd si la page a bien envoyé les valeurs du formulaire,
  	$login = $_POST['login']; //On applique la valeur login à une variable locale $login
  	$passe = $_POST['passe']; //On applique la valeur passe à une variable locale $passe

  	if (checkLogin($login, $passe))
  	{
		//On appelle la fonction verification (voir ci dessous)
    	// en passant les variables $login et $passe comme paramètres.
    	// Si cette fonction renvoie "true" :
    	session_regenerate_id(); // On purge la session de l'identifiant unique du client.
    	$_SESSION['nom'] = $login; // on met le login en variable de session "nom".
    	$message = "mot de passe valide !";
    	header("Location: ../../reglages.php"); // On recharge la page réglages.
  	}
	else
	{
    	// Si la correspondance mot de passe et login n'existe pas
    	// (la fonction verification renvoie "FALSE"):
    	$message = "<span class='alerte'>Identifiant ou mot de passe non reconnus !</span> <br>";
    	$message .= "veuillez ré-essayer :";
    	// On crée une variable $message avec l'html précisant que le mot de passe n'est pas reconnu
    	$_SESSION['message']=$message;
    	//On ajoute cette variable en variable de session
  		header("Location: ../../reglages.php");
  		//On recharge la page réglages.php.
  		// Comme la variable de session "nom" n'existe pas, on sera redirigé vers la page de login
  		// qui affichera la variable de session $message en remplacement du premier texte d'invitation
  		// a compléter le formulaire.
  	}
}

//echo $message;


/*
 * Comment : remplacé par la fonction checkLogin
function verification($login,$passe){
  // On attribue à des variables les différentes informations permettant de se connecter à la DB :
      /*$dbhote = "localhost";
      $dbuser = "root";
      $dbpass = "root";
      $dbbase = "nichoir";*/

  // On utilise l'objet PHP PDO qui doit permettre une portabilité facile du code pour changer de DB.
        //$dsn = "mysql:dbname=$dbbase; host=$dbhote";
        //$dbh = new PDO($dsn, $dbuser, $dbpass);
/*
try{
  $fichier_db = new PDO('sqlite:/var/www/nichoir.db');
  $fichier_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // On "hashe" en md5 le mot de passe avant de faire la requête.
  // En effet, les mots de passe sont stockés encryptés dans la DB.
  $passe= md5($passe);
  // On utilise la fonction "quote" pour filtrer et éventuellement ajouter des caractères
  // d'échappement dans les informations transmises par le formulaire
  $nom_sql = $fichier_db->quote($login);
  $pass_sql = $fichier_db->quote($passe);

  // On formule la requête SQL. On ajoute à la variable nbres le nombre de lignes de la DB dans
  // lesquelles le mot de passe et le login correspondent à ceux fournis.
  $sql = "SELECT count(*) as nbres FROM Users WHERE login=$nom_sql AND password=$pass_sql";

            //$resultat = $dbh->query($sql);
  $resultat = $fichier_db->query($sql);

  $row = $resultat->fetch();
  $resultat = null;

  // Si nbres contient "1" c'est qu'il y a bien une ligne avec mot de passe et identifiant associés
  if($row['nbres'] == 1){
    return TRUE; // La fonction de vérification renvoie "TRUE"

  }else{ // Autrement (à priori nbre == 0), il n'y a pas de ligne avec ce login et mot de passe
    return FALSE; // la fonction renvoie "FALSE"
  }
}
catch(PDOException $e){
echo $e->getMessage();
}
}

?>

 */
