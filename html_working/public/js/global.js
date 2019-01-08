function fermerFenetre() {
	// fermeture de la fenêtre modale utilisée pour l'affichage de messages
  	document.getElementById("FenetreMessage").classList.remove('montre');
  	document.getElementById("FenetreMessage").classList.add('cache');
}

function displayMenu2() {
	// affichage du sous-menu des Données au survol du menu relatif
	document.getElementById("menu2Container").classList.add("displayMenu2");
}

function hideMenu2() {
	//  cache le sous-menu des Données 
	document.getElementById("menu2Container").classList.remove("displayMenu2");
}
