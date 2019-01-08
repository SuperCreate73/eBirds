
<?php 
	$title = 'Mon nichoir: Donnees graphiques';
	$styles = '<link href="public/css/vis.css" rel="stylesheet" type="text/css" />'.PHP_EOL;
    $styles .='<link href="public/css/styleGraphique.css" rel="stylesheet" type="text/css" />'; 
?>

<?php ob_start(); ?>


<?php	$tableauResultat = array();
	
		foreach ($tableau as $row){
  			$dateCorr = convertirTimezone($row['dateHeure']);
  			$arr = array('y'=>(int)$row['tempExt'], 'x'=>$dateCorr, 'group'=> 0);
  			array_push($tableauResultat, $arr);
  			$arr = array('y'=>(int)$row['tempInt'], 'x'=>$dateCorr, 'group'=> 1);
  			array_push($tableauResultat, $arr);
  			$arr = array('y'=>(int)$row['humExt'], 'x'=>$dateCorr, 'group'=> 2);
  			array_push($tableauResultat, $arr);
  			$arr = array('y'=>(int)$row['humInt'], 'x'=>$dateCorr, 'group'=> 3);
  			array_push($tableauResultat, $arr);
		} ?>


	<script> var donnees = <?= (json_encode($tableauResultat)); ?> </script>
	<script src="public/js/assistants.js"></script>
  	<script src="public/js/vis.js"></script>
  	<script src="public/js/JS_Graphique.js"></script>

<?php $javaScripts = ob_get_clean(); ?>

<?php ob_start(); ?>	<!-- Contenu de la page, intégré à la variable $content -->

	<div id="row" class="topMargin">
    	<div id="legendes" class="two columns">
        	<h2 class="legendeTitre">Légende</h2>
		
			<div id = "contenant">

				<div class="legende" >Temp. Ext. <label class="switch">
               		<input type="checkbox" onclick="toggleText()" checked>
               		<span class="slider tExt round"></span>
              		</label>
				</div>
				
				<div class="legende">Temp. Int.	<label class="switch">
               		<input type="checkbox" onclick="toggleTint()" checked>
               		<span class="slider tInt round"></span>
               		</label>
				</div>

               	<div class="legende">Hum. Ext. <label class="switch">
               		<input type="checkbox" onclick="toggleHext()" checked>
               		<span class="slider hExt round"></span>
              		</label>
				</div>
		  
				<div class="legende">Hum. Int. <label class="switch">
               		<input type="checkbox" onclick="toggleHint()" checked>
               		<span class="slider hInt round"></span>
              		</label>
				</div>
			</div>
		</div>

		<div class="ten columns">
			<div id="graphique" class="main-pane"></div>
		</div>
	</div>
	

<?php $content = ob_get_clean(); ?>

<?php require('view/template.php'); ?>

