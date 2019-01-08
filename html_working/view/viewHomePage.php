<!--  Initialisation des variables utilisées pour l'affichage de la page -->
<?php 
	$title = 'Mon nichoir: Home sweet home';
	$styles = '<link rel="stylesheet" type="text/css" href="public/css/styleHomePage.css">'; 
 	$javaScripts = '<script src="public/js/assistants.js"></script> ';
?>

<?php ob_start(); ?>	<!-- Contenu de la page, intégré à la variable $content -->

	<h1>Tableau de bord</h1>
	<section class="row">
		<div class="two-thirds column"><img class="u-max-full-width camera" src=<?= "'http:\/\/".$_SERVER['SERVER_NAME'].":9081'" ; ?>></div>

		<div title="derniere mesure : <?= $dateHeure ?>" id="sensTemperature" class="sensors one-third column">
			<div class="row">
				<div class="two columns label">T°</div>
				<div class="five columns label">Int</div>
				<div class="five columns label">Ext</div>
			</div>

			<div class="separateur"></div>

			<div class="row">
				<div class="two columns icone"><img src="public/images/thermo.png" /></div>
				<div class="five columns valeur"><?= $tempExt ?></div>
				<div class="five columns valeur"><?= $tempInt ?></div>
			</div>
		</div>


		<div id="sensHumidity" title="derniere mesure : <?= $dateHeure ?>"  class="sensors one-third column">
			<div class="row">
				<div class="two columns label">H</div>
				<div class="five columns label">Int</div>
				<div class="five columns label">Ext</div>
			</div>

			<div class="separateur"></div>
	
			<div class="row">
				<div class="two columns icone"><img src="public/images/goutte.png" /></div>
				<div class="five columns valeur"><?= $humExt ?></div>
				<div class="five columns valeur"><?= $humInt ?></div>
			</div>
		</div>

		<div id="sensMovement" title="Nombre de visites : <?= $visites ?> " class="sensors one-third column">
			<div class="row">
				<div class="two columns label">I/O</div>
				<div class="five columns label">I</div>
				<div class="five columns label">O</div>
			</div>

			<div class="separateur"></div>

			<div class="row">
				<div class="two columns icone"><img src="public/images/io.png" /></div>
				<div class="five columns valeur"><?= $entrees ?></div>
				<div class="five columns valeur"><?= $sorties ?></div>
			</div>
		</div>
	</section>

<?php $content = ob_get_clean(); ?>


<!-- Appel de la page -->
<?php require('view/template.php'); ?>

