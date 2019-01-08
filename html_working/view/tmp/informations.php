<?php
include ('scripts/php/verifSession.php');

 ?>
<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<title>Mon nichoir: Informations</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="//fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="css/normalize.css">
	<link rel="stylesheet" href="css/skeleton.css">
	<link rel="stylesheet" type="text/css" href="css/style3.css">
	<link rel="icon" type="image/png" href="images/favicon.png">
	<script src="scripts/JS/assisstants.js"></script>

</head>


<body>
	<div class="container">
		<header>
			<div class="row">
				<div class="eight columns" id="header_icon">

				</div>
				<div class="four columns" id="headertag">
					eBirds interface v0.1
					<?php echo $logout; ?>
				</div>
			</div>

			<nav>
				<div class="row">
					<div class ="twelve columns">
						<ul class="menu">
							<li><a class="prems menuitem" href="index.php" ><div id="home_icon" class="menu_icon"></div><div class="menu_titre">tableau de bord</div></a></li>
							<li><a class="menuitem " href="graphique.php" ><div id="donnees_icon" class="menu_icon"></div><div class="menu_titre">données</div></a></li>
							<li><a class="menuitem selected" href="informations.php" ><div id="informations_icon" class="menu_icon"></div><div class="menu_titre">informations</div></a></li>
							<li class="right" ><a class="der menuitem" href="reglages.php"><div id="reglages_icon" class="menu_icon"></div><div class="menu_titre">réglages</div></a></li>
						</ul>
					</div>
				</div>
			</nav>
		</header>

		<div id="content">

			</div>



			<footer>
			</footer>





		</div>

	</body>




	</html>
