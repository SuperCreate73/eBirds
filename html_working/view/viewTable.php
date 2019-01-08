<?php 
	$title = 'Mon nichoir: Table des donnees';
	$styles= '<link href="public/css/styleTable.css" rel="stylesheet" type="text/css" />';
	$javaScripts = '<script src="public/js/assistants.js"></script>'.PHP_EOL;
  	$javaScripts .=	'<script src="public/js/list.js"></script>';
?>

<?php ob_start(); ?>	<!-- Contenu de la page, intégré à la variable $content -->

	<div id="row">
		<div class="twelve columns">
			<div class="menu-left">
				<a href="#">Graphique</a>
				<a href="#">Tables</a>
				<a href="#">Export</a>
				<a href="#">In/Out</a>
				<a href="#">Divers</a>
			</div>
		</div>
	</div>

   	<div id="tableMeteo">
   		<div id="row">
   			<div class="two columns">
       			<input class="search" placeholder="Filtre" />
       			<input class="button u-full-width boutonLogin" value="Export" onclick="window.location.assign('model/meteoToCsv.php')" />
   			</div>
			<div class="ten columns">
				<div id="table" class="main-pane">					
					<table class='table-fill'>
						<thead>
							<tr>
								<th class='text-center'><button class='sort bhtable' data-sort='fDate'>Date</button></th>
								<th class='text-center'><button class='sort bhtable' data-sort='ftExt'>T° Ext</button></th>
								<th class='text-center'><button class='sort bhtable' data-sort='ftInt'>T° Int</button></th>
								<th class='text-center'><button class='sort bhtable' data-sort='fhExt'>H. Ext</button></th>
								<th class='text-center'><button class='sort bhtable' data-sort='fhInt'>H. Int</button></th>
							</tr>
						</thead>
						<tbody class='list text-center'>
							<?php foreach ($tableau as $row) { ?>
								<tr>  
									<td class='fDate text-center'><?= convertirTimezone($row['dateHeure']) ?></td>
									<td class='ftExt text-center'><?= $row['tempExt'] ?></td>
									<td class='ftInt text-center'><?= $row['tempInt'] ?></td>
									<td class='fhExt text-center'><?= $row['humExt'] ?></td>
									<td class='fhInt text-center'><?= $row['humInt'] ?></td>
								</tr> <?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>


<script type="text/javascript">

    var options = {
      valueNames: [ 'fDate', 'ftExt', 'ftInt', 'fhExt', 'fhInt' ]
    };

    var userList = new List('tableMeteo', options);

</script>

<?php $content = ob_get_clean(); ?>

<?php require('view/template.php'); ?>

