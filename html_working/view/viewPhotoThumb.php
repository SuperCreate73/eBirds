<?php 
	$title = 'Mon nichoir: Photos et vidéos';
 // styles de la page et style de la bibliothèque "photorama"
	$styles  = '<link href="public/css/stylePhotoThumb.css" rel="stylesheet" type="text/css" />'.PHP_EOL; 
	$styles .= '<link href="vendor/fotorama/fotorama.css" rel="stylesheet">';
 // bibliothèques javascript jQuery et "photorama"
	$javaScripts  = '<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>'.PHP_EOL;
	$javaScripts  .= '<script src="vendor/fotorama/fotorama.js"></script>';
?>

<?php ob_start(); ?>	<!-- Contenu de la page, intégré à la variable $content -->

   	<div id="photothumb" class="main-pane cadre">
		<!-- Fotorama -->
		<div class="fotorama" data-allowfullscreen="true" data-nav="thumbs" data-width="700" data-ratio="700/467" data-max-width="100%">

		<?php foreach ($fileList as $value) { ?>
			<a href="public/cameraShots/<?= $value ?>"></a>'
		<?php } ?>

		</div>
   	</div>

<?php $content = ob_get_clean(); ?>

<?php require('view/template.php'); ?>

