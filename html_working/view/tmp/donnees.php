<?php
include ('scripts/php/verifSession.php');
include ('scripts/php/helpers.php');
include ('scripts/php/connexionDB.php');

try
{
	$fichier_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM meteo ORDER BY dateHeure;";
	$resultat = $fichier_db->query($sql);
}
catch(PDOException $e)
{
	echo $e->getMessage();
}


$tableau = array();
foreach ($resultat as $rangee){
  $dateCorr = convertirTimezone($rangee['dateHeure'], "GMT", "Europe/Brussels");
  $arr = array('y'=>(int)$rangee['tempExt'], 'x'=>$dateCorr, 'group'=> 0);
  array_push($tableau, $arr);
  $arr = array('y'=>(int)$rangee['tempInt'], 'x'=>$dateCorr, 'group'=> 1);
  array_push($tableau, $arr);
  $arr = array('y'=>(int)$rangee['humExt'], 'x'=>$dateCorr, 'group'=> 2);
  array_push($tableau, $arr);
  $arr = array('y'=>(int)$rangee['humInt'], 'x'=>$dateCorr, 'group'=> 3);
  array_push($tableau, $arr);
  // 'group'=> 0

}


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
  <script src="scripts/JS/vis.js"></script>
  <link href="css/vis.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" type="text/css" href="css/style3.css">


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
					<div class="menu-left">

					<a href="#">Graphique</a>
					<a href="#">Tables</a>
					<a href="#">Export</a>

					<a href="#">In/Out</a>
					<a href="#">Divers</a>
					</div>

</div>


				</div>
        <div id="row">
				<div class="ten columns">
				 <div id="graphique" class="main-pane"></div>
				</div>
        <div id="legendes" class="two columns">
          <h2 class="legendeTitre">Légende</h2>
            <div id = "contenant">

              <div class="legende" >Temp. Ext. <label class="switch">
                <input type="checkbox" onclick="toggleText()" checked>
                <span class="slider tExt round"></span>
              </label></div>

              <div class="legende">Temp. Int. <label class="switch">
                <input type="checkbox" onclick="toggleTint()" checked>
                <span class="slider tInt round"></span>
              </label></div>

              <div class="legende">Hum. Ext. <label class="switch">
                <input type="checkbox" onclick="toggleHext()" checked>
                <span class="slider hExt round"></span>
              </label></div>
              <div class="legende">Hum. Int. <label class="switch">
                <input type="checkbox" onclick="toggleHint()" checked>
                <span class="slider hInt round"></span>
              </label></div>

            </div>

        </div>






			</div>





			</div>



			<footer>
        <?php include('footer.php') ?>
			</footer>





		</div>
<script type="text/javascript">

var tExt = true;
var tInt = true;
var hExt = true;
var hInt = true;
 var container = document.getElementById('graphique');

 var options = {
   drawPoints:{size: 5, style: 'circle'},
   maxHeight: '450px',
   orientation: 'top',
   clickToUse: false,
   dataAxis: {alignZeros: true, left:{title:{text:"Température"}}, right:{title:{text:"Humidité"}}}
 };
var groupes = new vis.DataSet();
groupes.add(
  {id: 0,className:"styleGraph", content: "Temp. Ext.", options: {drawPoints: false}}
);
groupes.add(
  {id: 1, className:"styleGraph2", content: "Temp. Int.", options: {drawPoints: false}}
);
groupes.add(
  {id: 2, className:"styleGraph3", content: "Hum. Ext.", options: {drawPoints: false, yAxisOrientation: 'right'}}
);
groupes.add(
  {id: 3, className:"styleGraph4", content: "Hum. Int.", options: {drawPoints: false,yAxisOrientation: 'right'}}
);


  <?php

    echo "var donnees = ".json_encode($tableau).";";

   ?>

 var dataset = new vis.DataSet(donnees);
 //dataset.add({id: 0, content: "Temp Ext"});

 var graph2D = new vis.Graph2d(container, dataset, groupes, options);

function toggleText(){
  var prop = {};
  prop[0] = tExt = !tExt;
  graph2D.setOptions({groups:{visibility:prop}});
}
function toggleTint(){
  var prop = {};
  prop[1] = tInt = !tInt;
  graph2D.setOptions({groups:{visibility:prop}});
}
function toggleHext(){
  var prop = {};
  prop[2] = hExt = !hExt;
  graph2D.setOptions({groups:{visibility:prop}});
}
function toggleHint(){
  var prop = {};
  prop[3] = hInt = !hInt;
  graph2D.setOptions({groups:{visibility:prop}});
}
</script>

	</body>






	</html>
