<!--déclaration du type de document: Html 5 -->
<!DOCTYPE html>
<html>
	<head>		<!-- Métadonnées de base utiles à la page html -->
		<meta charset="UTF-8">
		<title><?= $title ?></title>	<!-- Titre de la page contenu dans la variable $title -->

		<meta name="viewport" content="width=device-width, initial-scale=1">	<!-- "viewport" est spécifique aux navigateurs de téléphones ou tablettes -->

		<link href="//fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">	<!-- Police de caractères de Google -->

		<!--  Feuilles de style pour la mise en page et le layout :
				normalize.css : permet de "mettre à zéro" le style des divers navigateurs
				skeleton.css : 	une série de règles de CSS pour simplifier le design responsive
				style.css : 	notre feuille de style du nichoir. -->
		<link rel="stylesheet" type="text/css" href="public/css/normalize.css">
		<link rel="stylesheet" type="text/css" href="public/css/skeleton.css">
		<link rel="stylesheet" type="text/css" href="public/css/styleGeneral.css">
		<?= $styles ?>
		
		<link rel="icon" type="image/png" href="public/images/favicon.png">	 <!--	Chargement de l'icone "favicon"    -->


	</head>


	<body>
		<div class="container">

			<header><?php include('view/header.php'); ?></header> <!-- Chargement de l'en-tête : fichier 'header.php' -->  

			<div id="content"><?= $content ?></div>   <!-- Chargement du corps de page contenu dans la variable $content -->  

			<footer><?php include('view/footer.php'); ?></footer> <!-- Chargement du pied de page : fichier 'footer.php' -->  

		</div>

		<div id="FenetreMessage" class="cache">
			<div id="btn_cancel" class="my_button" onclick="fermerFenetre()">x</div>
			<div id="FenetreContenu"></div>
		</div>

		<script src="public/js/global.js"></script>
		<?= $javaScripts ?>		<!--    Chargement des scripts Javascript contenu dans la variable $jScript         -->

	</body>
</html>

