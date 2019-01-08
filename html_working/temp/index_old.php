<?php

//OK-include ('scripts/php/verifSession.php');
//OK-include ('scripts/php/connexionDB.php');
// On inlus un script php qui sera interprété par le serveur.
// Ce script permet de vérifier si une session est déjà ouverte, si l'utilisateur
// est déjà connecté avec un login et un mot de passe.
/* OK-try
{
  	//$fichier_db = new PDO('sqlite:/var/www/nichoir.db');
  	$fichier_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$sql = "SELECT * FROM meteo ORDER BY dateHeure DESC LIMIT 1;";
  	$resultat = $fichier_db->query($sql);
  	$row = $resultat->fetch();
	$tempExt = $row['tempExt'];
	$humExt = $row['humExt'];
	$tempInt = $row['tempInt'];
	$humInt = $row['humInt'];
	$dateHeure = $row['dateHeure'];
	
	$sql = "SELECT count(*) FROM InOut_IR  WHERE FDatim >= date('now', 'start of day') AND FStatus like 'E%';";
  	$resultat = $fichier_db->query($sql);
	$row = $resultat->fetch();
  	$entrees = $row[0];

	$sql = "SELECT count(*) from InOut_IR  where FDatim >= date('now','start of day') and FStatus like 'S%';";
	$resultat = $fichier_db->query($sql);
	$row = $resultat->fetch();
	$sorties = $row[0];

	$sql = "SELECT count(*) from InOut_IR  where FDatim >= date('now','start of day') and FStatus like 'V%';";
	$resultat = $fichier_db->query($sql);
	$row = $resultat->fetch();
	$visites = $row[0];

}
catch(PDOException $e)
{
	echo $e->getMessage();
}

?>*/
<!--déclaration du type de document: Html 5 -->
<!DOCTYPE html>

<head>
	<!-- On inclus les métadonnées de base utiles à la page html -->
	<meta charset="UTF-8">
	<title>Mon nichoir: Home, sweet home</title>
	<!-- la donnée "viewport" est spécifique aux navigateurs de téléphones ou tablettes -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
		<!--  Ensuite on charge la police de caractères servie par Google et les diverses feuilles de style:
		normalize.css : permet de "mettre à zéro" le style des divers navigateurs
		skeleton.css : une série de règles de CSS pour simplifier le design responsive
		style.css : notre feuille de style du nichoir.

		Aussi l'icone "favicon"...
        -->
	<link href="//fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="css/normalize.css">
	<link rel="stylesheet" type="text/css" href="css/skeleton.css">
	<link rel="stylesheet" type="text/css" href="css/style3.css">
	<link rel="icon" type="image/png" href="images/favicon.png">

	<!--    Chargement de nos scripts Javascript         -->
	<script src="scripts/JS/assisstants.js"></script>
</head>


<body>
	<div class="container">
		<header><!--     L'en-tête de la page           -->
			<div class="row"><!--      Une rangée pour skeleton.css         -->
				<div class="eight columns" id="header_icon">
					<!--  On prend 8 colonnes sur 12 de la rangée pour le logo.         -->
				</div>
				<div class="four columns" id="headertag">
					<!--    4 colonnes pour les infos login etc...           -->
					eBirds interface v0.1
					<?php echo $logout;
					// La variable $logout est remplie par le script php ci-dessus si une session est ouverte.
					 ?>

				</div>
			</div>

			<nav> <!--Balise indiquant le menu principal               -->
				<div class="row">
					<div class ="twelve columns">
						<!-- Le menu est déclaré sous forme d'un élément liste en html.
					   	Il s'agit en réalité d'une liste de liens
						Nous avons pour chaque élément de la liste (<li>)
						deux balises <div> à l'intérieur. Une pour le logo, l'autre pour le texte.-->
						<ul class="menu">
							<li><a class="prems selected menuitem" href="index.php" ><div id="home_icon" class="menu_icon"></div><div class="menu_titre">tableau de bord</div></a></li>
							<li><a class="menuitem" href="graphique.php" ><div id="donnees_icon" class="menu_icon"></div><div class="menu_titre">données</div></a></li>
							<li><a class="menuitem" href="informations.php" ><div id="informations_icon" class="menu_icon"></div><div class="menu_titre">informations</div></a></li>
							<li class="right" ><a class="der menuitem" href="reglages.php"><div id="reglages_icon" class="menu_icon"></div><div class="menu_titre">réglages</div></a></li>
						</ul>
					</div>
				</div>
			</nav>
		</header>

		<div id="content">
			<h1>Tableau de bord</h1>
			<div class="row">
				<div class="two-thirds column">
					<img class="u-max-full-width camera" src=<?php echo"'http:\/\/".$_SERVER['SERVER_NAME'].":9081'"; ?>>
				</div>

				<div title="derniere mesure : <?php echo $dateHeure ?>" id="sensors" class="one-third column">

					<div class="row">
						<div class="two columns label">T°</div>
						<div class="five columns label">Int</div>
						<div class="five columns label">Ext</div>
					</div>
					<div class="separateur"></div>
					<div class="row">
						<div class="two columns icone">
							<img src="images/thermo.png" />
						</div>
						<div class="five columns valeur"><?php echo $tempExt ?></div>
						<div class="five columns valeur"><?php echo $tempInt ?></div>
					</div>

				</div>


				<div id="sensors" title="derniere mesure : <?php echo $dateHeure ?>"  class="one-third column">
					<div class="row">
						<div class="two columns label">H</div>
						<div class="five columns label">Int</div>
						<div class="five columns label">Ext</div>
					</div>
					<div class="separateur"></div>
					<div class="row">
						<div class="two columns icone">
							<img src="images/goutte.png" />
						</div>
						<div class="five columns valeur"><?php echo $humExt ?></div>
						<div class="five columns valeur"><?php echo $humInt ?></div>
					</div>
				</div>

				<div id="sensors" title="Nombre de visites : <?php echo $visites ?> " class="one-third column">
					<div class="row">
						<div class="two columns label">I/O</div>
						<div class="five columns label">I</div>
						<div class="five columns label">O</div>
					</div>
					<div class="separateur"></div>
					<div class="row">
						<div class="two columns icone">
							<img src="images/io.png" />
						</div>
						<div class="five columns valeur"><?php echo $entrees ?></div>
						<div class="five columns valeur"><?php echo $sorties ?></div>
					</div>
				</div>
			</div>
		</div>
		<footer>
      <?php include('footer.php'); ?>
		</footer>
	</div>
</body>
</html>

