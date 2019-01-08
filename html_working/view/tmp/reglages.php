<?php
include ('scripts/php/PageSecurisee.php');
// L'accès à cette page est réservée aux personnes connectées avec un mot de passe.
// Le script PageSécurisee.php vérifie qu'un utilisateur est bien connecté.
// Dans le cas contraire, il redirige vers une page de connexion (login.php)
//  Pour sécuriser une page, il suffit donc d'inclure ce script en amont de l'html.
$nom_hote = gethostname();

// Les variables ici servent pour une connexion à une db mysql utilisée pendant le développement.
// $dbhote = "localhost";
//$dbuser = "root";
//$dbpass = "root";
//$dbbase = "nichoir";
//$dsn = "mysql:dbname=$dbbase; host=$dbhote";

//$fichier_db = new PDO($dsn, $dbuser, $dbpass);
$fichier_db = new PDO('sqlite:/var/www/nichoir.db');  //cette ligne sert à établir la connexion à la db mysql sur le raspberry.
$fichier_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT * FROM Users";
$demande = $fichier_db->query($sql);

$listeUtilisateurs ="";
foreach($demande as $rangee){
	$listeUtilisateurs.="
		<div class='row'>
			<div class='offset-by-four columns'>
				<div class='five columns'>".$rangee['login']."
				</div></div></div>";

}

?>

<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<title>Mon nichoir: Reglages</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="//fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="css/normalize.css">
	<link rel="stylesheet" href="css/skeleton.css">
	<link rel="stylesheet" type="text/css" href="css/style3.css">
	<link rel="icon" type="image/png" href="images/favicon.png">



	<script src="scripts/JS/assistants_reglages.js"></script>

</head>


<body>
	<div class="container">
		<header>
			<div class="row">
				<div class="eight columns" id="header_icon">

				</div>
				<div class="four columns" id="headertag">
					eBirds interface v0.1
					<?php echo $logout; ?>
				</div>
			</div>

			<nav>
				<div class="row">
					<div class ="twelve columns">
						<ul class="menu">
							<li><a class="prems menuitem" href="index.php" ><div id="home_icon" class="menu_icon"></div><div class="menu_titre">tableau de bord</div></a></li>
							<li><a class="menuitem " href="donnees.php" ><div id="donnees_icon" class="menu_icon"></div><div class="menu_titre">données</div></a></li>
							<li><a class="menuitem" href="informations.php" ><div id="informations_icon" class="menu_icon"></div><div class="menu_titre">informations</div></a></li>
							<li class="right" ><a class="der menuitem selected" href="reglages.php"><div id="reglages_icon" class="menu_icon"></div><div class="menu_titre">réglages</div></a></li>
						</ul>
					</div>
				</div>
			</nav>
		</header>

		<div id="content">


			<h1>Réglages</h1>
			<div id="accordion">
			<form method="POST" action="">


				<div class="cadre systeme">
					<div class="row spaced">
						<div class="eight columns">
					<h2>Système</h2>
				</div>
						<div class="four columns">
							<div id="btn_shutdown" class='my_button' value="Shutdown" onclick="shutdown()"></div>
							<div id="btn_restart" class='my_button' value="Redémarrer" onclick="reboot()"></div>

						<!--input class="button u-full-width boutonLogin" value="Redémarrer" onclick="reboot()"-->

					</div>


				</div>

					<div class="row">
						<div class="offset-by-two columns">
							<div class="two columns">
								<h3>Nom :</h3>
							</div>

							<div class="seven columns">
								<input class="u-full-width" type="text" placeholder="<?php echo $nom_hote ?>" name="nom_nichoir">
							</div>
							<div class="one column">
								<div id="save_name" class='my_button save' value="Enregistrer" onclick="save_name()"></div>

								<!--input class="button u-full-width boutonLogin" value="Modifier"-->
							</div>
						</div>
					</div>

					<div class="row">
						<div class="offset-by-two columns">
							<div class="two columns">
								<h3>Identifiant :</h3>
							</div>
							<div class="seven columns">

								<input class="u-full-width" type="text" placeholder="Identifiant" name="id">
							</div>
							<div class="one column">
								<div id="save_id" class='my_button save' value="Enregistrer id" onclick="save_id()"></div>

								<!--input class="button u-full-width boutonLogin" value="Modifier"-->
							</div>
						</div>
					</div>

					<div class="row">
						<div class="offset-by-two columns">
							<div class="seven columns">
								<h3>Utilisateurs :</h3>
							</div>

							<div class="three columns">
								<div id="add_user" class='my_button' value="ajouter utilisateur" onclick="addUser()"></div>

								<!--input class="button u-full-width boutonLogin" value="Ajouter" onclick="addUser()"-->

							</div>

						</div>
					</div>
					<?php echo $listeUtilisateurs; ?>


				</div> <!--    FIN CADRE II -->










				<div class="cadre">
					<div class="row">
						<div class="two columns">
					<h2>Mode</h2>
				</div>
						<div class="five columns">
						<label for="modeNichoir">Mode de fonctionnement du nichoir:</label>
						<select class="u-full-width" id="modeNichoir">
							<option value="Option 1">Découverte</option>
							<option value="Option 2">Occupation</option>

						</select>
					</div>
					<div class="five columns">
						<label>Email(s) à contacter en cas d'occupation:</label>
						<input class="u-full-width" type="text" placeholder="email(s)" name="emails">
					</div>
					</div>

				</div> <!--    FIN CADRE II -->

				<div class="cadre">
					<div class="row">
						<div class="two columns">
					<h2>Caméra</h2>
				</div>
						<div class="five columns">

							<label for="modeCamera">Mode d'enregistrement images:</label>
							<select class="u-full-width" id="modeCamera">
								<option value="Option 1">Photo</option>
								<option value="Option 2">Video</option>

							</select>


					</div>
					<div class="five columns">
						<label for="definitionCamera">Définition de l'image:</label>
						<select class="u-full-width" id="definitionCamera">
							<option value="Option 1">High</option>
							<option value="Option 2">Medium</option>
							<option value="Option 2">Low</option>

						</select>

					</div>
					</div>

				</div> <!--    FIN CADRE III -->

				<div class="cadre">
					<div class="row">
						<div class="two columns">
					<h2>Variables composants</h2>
				</div>
						<div class="five columns">
						<label for="capacitance">Valeur seuil capacitance:</label>
								<input class="u-full-width" type="text" placeholder="Valeur" name="capacitance">

					</div>

					</div>





			</div>


			<div class="cadre">
				<div class="row">
					<div class="two columns">
						<h2>Général</h2>
					</div>
					<div class="ten columns">
						<h3>Localisation</h3>
					</div>
				</div>
				<div class="row">
					<div class="offset-by-two columns">
						<div class="seven columns">
							<label>Rue et numéro</label>
							<input class="u-full-width" type="text" placeholder="Rue" name="rue">
							<div class="row">
								<div class="four columns">
									<label>C.P.</label>
									<input class="u-full-width" type="text" placeholder="Code postal" name="CP">
								</div>
								<div class="eight columns">
									<label>Localité</label>
									<input class="u-full-width" type="text" placeholder="Localité" name="localite">
								</div>
							</div>
							<label>Pays</label>
							<input class="u-full-width" type="text" placeholder="Pays" name="pays">
						</div>
						<div class="three columns">
							<label>Latitude</label>
							<input class="u-full-width" type="text" placeholder="Latitude" name="latitude">
							<label>Longitude</label>
							<input class="u-full-width" type="text" placeholder="Longitude" name="longitude">
							<label>Altitude</label>
							<input class="u-full-width" type="text" placeholder="Altitude" name="altitude">
						</div>
					</div>
				</div>


			</div> <!-- Fin de cadre 1 -->

			<div class="row">
				<div class="offset-by-seven columns">
				<div class="five columns">
					<input class="button u-full-width boutonLogin" value="Enregistrer les modifications" type="submit">

				</div>
			</div>

			</div>

	</form>
</div>
	</div>

</div>

<div id="FenetreMessage" class="cache">
</div>


<footer>
</footer>





</div>

</body>




</html>
