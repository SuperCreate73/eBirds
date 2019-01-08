<?php 
	$title = 'Mon nichoir: Informations';
	$styles = '<link rel="stylesheet" type="text/css" href="public/css/styleInformation.css">';
	$javaScripts = '<script src="public/js/assistants.js"></script>'
?>

<?php ob_start(); ?>	<!-- Contenu de la page, intégré à la variable $content -->

	<h1>Information générale</h1>
	<section class="row">
		<div title="Dernières nouvelles :" id="news" class="twelve columns">
			<div class="row">
				<p>
					Actuellement, les nichoirs sont encore en phase de tests et d'améliorations. Notre devise : <strong>toujours plus loin, toujours plus fort, toujours plus fous</strong>.  Mais où cela va-t-il s'arrêter ?
				</p>
				<p>
					Un nichoir est complètement opérationnel, installé sur la façade arrière de l'EPN de Chaumont-Gistoux, mais malgré les attraits non négligeables (chauffage central, connection Wi-Fi, système anti-intrusion, ...) , il n'a	pas encore été pris d'assaut ...  Des soupçons sont portés sur un individu de race indéterminée, recouvert de poils et se déplaçant à quatre pattes.  Il aurait été aperçu, rodant aux alentours et effrayant ces pauvres oiseaux sans défenses ...  <strong> Mais que fait la police ? </strong> 
				</p>
			</div>

			<div class="separateur"></div>

			<div class="row">
				<p>Les services secrets <strong>BirdWatch</strong> sont sur le qui-vive ! <br /> 
Attention, cette information n'a pas encore été confirmée et doit être prise avec toutes les réserves ... mais ... il semblerait, au conditionnel donc,qu'un message menaçant circulerait sur internet, promettant moultes représailles à qui s'installerait dans un des nichoirs high-tech mis à leur disposition par eBirds(R). 
				</p> 
				<p>L'origine de cette missive a été revendiquée par le goupe extrémiste <strong>CatPower</strong>, ce qui est quand me douteux vu qu'aucune missive n'a été trouvée et qu'on connait les talents rédactionnels de ces félins de sous-ordre.<br/>
Mais bon, comme je l'ai déjà dit, je pense, cette information doit encore être validée par le commité exécutif de <strong>BirdWatch</strong>.  Affaire à suivre donc ! 
				</p>
			</div>
		</div>
	</section>

<?php $content = ob_get_clean(); ?>

<?php require('view/template.php'); ?>
