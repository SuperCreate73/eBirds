<!-- Partie déclarative - initialisation des variables -->
<?php
// initialisation des données de login apparaissant dans le coin supérieur droit
if (isset($_SESSION['nom'])) {
		$logout="<br><span id='logout' onclick=''>bonjour, ".$nom." !<br><a href='index.php?page=logout'> Se déconnecter </a></span>";
	}
	else {
		$logout="<br><span id='logout' onclick=''><br>Bienvenu cher visiteur,<br><a href='index.php?page=login'>Se connecter</a></span>";
	}

// initialisation du focus du sous-menu 'données' si nécessaire
if (!isset($tabFocusMen2)) {
	$tabFocusMen2=setFocusMen2(0);
}
?>


<!--	L'en-tête de la page           -->
<div class="row">	<!--	Une rangée pour skeleton.css         -->
	<div class="eight columns" id="header_icon"></div>	<!--	On prend 8 colonnes sur 12 de la rangée pour le logo.	-->
	<div class="four columns" id="headertag">			<!--	4 colonnes pour les infos login etc...	-->
		eBirds interface v0.1
		<?= $logout ?>		<!--	La variable $logout est remplie ci dessus.	-->
	</div>
</div>

<nav> <!--	Balise indiquant le menu principal     -->
			<!-- 	Le menu est déclaré sous forme d'un élément liste en html.
	   				Il s'agit en réalité d'une liste de liens. Nous avons pour chaque élément de la liste (<li>)
					deux balises <div> à l'intérieur. Une pour le logo, l'autre pour le texte.	-->
	<ul class="mainMenuContainer">
		<li class="mainMenuItem prems <?= $tabFocus[0] ?>"><a class="menuItem" href="index.php?page=homepage" >
			<div id="home_icon" class="menu_icon"></div>
			<div class="menu_titre">tableau de bord</div>
		</a></li>
		<li id="dataMenu" class="mainMenuItem <?= $tabFocus[1] ?>" onmouseover="displayMenu2()" onmouseleave="hideMenu2()" onmouseclick="displayMenu2()"><a class="menuItem" >
			<div id="donnees_icon" class="menu_icon"></div>
			<div class="menu_titre">donnees</div></a>

			<div id="menu2Container" class="menu2Container">
				<a href="index.php?page=graphique" class=<?= $tabFocusMen2[0] ?> >Graphique</a>
				<a href="index.php?page=tableData" class=<?= $tabFocusMen2[1] ?> >Tables</a>
				<a href="index.php?page=photoList" class=<?= $tabFocusMen2[2] ?> >Photos</a>
				<a href="index.php?page=photoThumb" id="photoThumbLink" class=<?= $tabFocusMen2[3] ?> >Diaporama</a>
			</div>
		</li>
		<li class="mainMenuItem <?= $tabFocus[2] ?>"><a class="menuItem" href="index.php?page=information" >
			<div id="informations_icon" class="menu_icon"></div>
			<div class="menu_titre">informations</div>
		</a></li>
		<li class="mainMenuItem der <?= $tabFocus[3] ?>" ><a class="menuItem" href="index.php?page=reglages">
			<div id="reglages_icon" class="menu_icon"></div>
			<div class="menu_titre">reglages</div>
		</a></li>
	</ul>
</nav>

<div class="menuSeparateur"></div>
