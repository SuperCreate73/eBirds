<?php
session_start(); //Initialisation de la Session
 ?>
<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<title>Mon nichoir: Reglages</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="//fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="css/normalize.css">
	<link rel="stylesheet" href="css/skeleton.css">
	<link rel="stylesheet" type="text/css" href="css/style3.css">
	<link rel="icon" type="image/png" href="images/favicon.png">
</head>


<body>
	<div class="container">
		<header>
			<div class="row">
				<div class="eight columns" id="header_icon">

				</div>
				<div class="four columns" id="headertag">
					eBirds interface v0.1
				</div>
			</div>

			<nav>
				<div class="row">
					<div class ="twelve columns">
						<ul class="menu">
							<li><a class="prems menuitem" href="index.php" ><div id="home_icon" class="menu_icon"></div><div class="menu_titre">tableau de bord</div></a></li>
							<li><a class="menuitem " href="graphique.php" ><div id="donnees_icon" class="menu_icon"></div><div class="menu_titre">données</div></a></li>
							<li><a class="menuitem" href="informations.php" ><div id="informations_icon" class="menu_icon"></div><div class="menu_titre">informations</div></a></li>
							<li class="right" ><a class="der menuitem selected" href="reglages.php"><div id="reglages_icon" class="menu_icon"></div><div class="menu_titre">réglages</div></a></li>
						</ul>
					</div>
				</div>
			</nav>
		</header>

		<div id="content">


			<h1>Login</h1>
			<form class="loginform" action="scripts/php/check_login.php" method="POST">
				<div class="row">

					<div class="offset-by-three columns">
						<div class="six columns" id="presentation">
							<?php
							if(isset($_SESSION['message'])){
								$message = $_SESSION['message'];

								echo $message;
							} else{
								echo "L'accès aux réglages du nichoir nécessite un nom d'utilisateur et un mot de passe.<br/><br/> Merci de compléter ce formulaire :";
							}
							 ?>

						</div>
					</div>

					<div class="row">
						<div class="offset-by-three columns">
							<div class="six columns">

								<input class="u-full-width" type="text" placeholder="Votre login" name="login">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="offset-by-three columns">
							<div class="six columns">

								<input class="u-full-width" type="password" placeholder="Votre mot de passe" name="passe">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="offset-by-three columns">
							<div class="six columns">
								<input class="button boutonLogin" type="submit" value="OK !">
							</div>
						</div>
					</div>
				</form>
			</div>



			<footer>
			</footer>





		</div>

	</body>




	</html>
