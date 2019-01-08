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

	<div id="row">
		<div class="twelve columns">
			<div class="menu-left">
				<a href="index.php?action=photothumb&amp;param1=next">Next</a>
				<a href="index.php?action=photothumb&amp;param1=previous">Previous</a>
				<a href="index.php?action=photothumb&amp;param1=first">First</a>
				<a href="index.php?action=photothumb&amp;param1=last">Last</a>
				<a href="#">Next</a>
				<a href="#">Previous</a>
				<a href="#">First</a>
				<a href="#">Last</a>
				<a href="#">Select</a>
				<a href="#">Tag</a>
				<a href="#">Open</a>
				<a href="#">Download</a>
				<a href="#">Delete</a>
				<a href="#">Comment</a>
				<a href="#">Tag</a>
			</div>
		</div>
	</div>

   	<div id="photothumb" class="main-pane">

			<!-- Fotorama -->
	<div class="row">
		<div class="twelve columns">
			<div class="fotorama" data-allowfullscreen="true" data-nav="thumbs" data-width="700" data-ratio="700/467" data-max-width="100%">
				<?= $listePhotos;?>
			</div>

		</div>
	</div>
			
   	</div>

<?php $content = ob_get_clean(); ?>

<?php require('view/template.php'); ?>

