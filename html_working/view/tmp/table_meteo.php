<?php
include ('scripts/php/verifSession.php');
include ('scripts/php/helpers.php');
include ('scripts/php/connexionDB.php');

try{
  $fichier_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $sql = "SELECT * FROM meteo ORDER BY dateHeure DESC;";
  $resultat = $fichier_db->query($sql);
}
catch(PDOException $e){
echo $e->getMessage();
}

$tableau = "<table class='table-fill'><thead><tr><th class='text-center'><button class='sort bhtable' data-sort='fDate'>Date</button></th><th class='text-center'><button class='sort bhtable' data-sort='ftExt'>T° Ext</button></th>
<th class='text-center'><button class='sort bhtable' data-sort='ftInt'>T° Int</button></th><th class='text-center'><button class='sort bhtable' data-sort='fhExt'>H. Ext</button></th><th class='text-center'><button class='sort bhtable' data-sort='fhInt'>H. Int</button></th></tr></thead><tbody class='list text-center'>";
foreach ($resultat as $rangee){
  $dateCorr = convertirTimezone($rangee['dateHeure'], "GMT", "Europe/Brussels");
  $tableau.="<tr><td class='fDate text-center'>".$dateCorr."</td><td class='ftExt text-center'>".$rangee['tempExt']."</td><td class='ftInt text-center'>".$rangee['tempInt']."</td><td class='fhExt text-center'>".$rangee['humExt']."</td><td class='fhInt text-center'>".$rangee['humInt']."</td></tr>";
}
$tableau.="</tbody></table>";
 ?>

<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<title>Mon nichoir: Donnees</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="//fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="css/normalize.css">
	<link rel="stylesheet" href="css/skeleton.css">
	<link rel="icon" type="image/png" href="images/favicon.png">
	<script src="scripts/JS/assisstants.js"></script>
	<script src="scripts/JS/list.js"></script>
  <link rel="stylesheet" type="text/css" href="css/style3.css">
  <link rel="stylesheet" type="text/css" href="css/tables.css">


</head>


<body>
	<div class="container">
		<header>
			<div class="row">
				<div class="eight columns" id="header_icon">

				</div>
				<div class="four columns" id="headertag">
					<?php echo $logout; ?>
				</div>
			</div>

			<nav>
				<div class="row">
					<div class ="twelve columns">
						<ul class="menu">
							<li><a class="prems menuitem" href="index.php" ><div id="home_icon" class="menu_icon"></div><div class="menu_titre">tableau de bord</div></a></li>
							<li><a class="menuitem selected" href="donnees.php" ><div id="donnees_icon" class="menu_icon"></div><div class="menu_titre">données</div></a></li>
							<li><a class="menuitem" href="informations.php" ><div id="informations_icon" class="menu_icon"></div><div class="menu_titre">informations</div></a></li>
							<li class="right" ><a class="der menuitem" href="reglages.php"><div id="reglages_icon" class="menu_icon"></div><div class="menu_titre">réglages</div></a></li>
						</ul>
					</div>
				</div>
			</nav>
		</header>

		<div id="content">
			<div id="row">
				<div class="twelve columns">
					<div class="menu-secondaire">

					<a href="graphique.php" class="itemMS">Graphique</a>
					<a href="table_meteo.php" class="s_select itemMS">Tables</a>
				

					</div>

</div>


				</div>
        <div id="tableMeteo">
        <div id="row">
          <div class="two columns">
            <input class="search" placeholder="Filtre" />
            <input class="button u-full-width boutonLogin" value="Export" onclick="window.location.assign('meteoToCsv.php')" />
          </div>
				<div class="ten columns">
				 <div id="table" class="main-pane"><?php echo $tableau ?></div>
				</div>


			   </div>
       </div>

			</div>
			<footer>
        <?php include('footer.php') ?>
			</footer>

		</div>
<script type="text/javascript">
    var options = {
      valueNames: [ 'fDate', 'ftExt', 'ftInt', 'fhExt', 'fhInt' ]
    };

    var userList = new List('tableMeteo', options);

</script>

	</body>






	</html>
