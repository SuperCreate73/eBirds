function logout() {
  // On utilise l'objet XMLHttpRequest pour faire une déconnexion en ajax.
  // Càd qu'on utilise Javascript pour appeler le script php de déconnexion
  // sans quitter ou recharger la page.
    var xmlhttp = new XMLHttpRequest(); // On crée une instance de l'objet
    xmlhttp.open("GET", "jsRouter.php", true); // On ouvre la connexion vers le script php
    xmlhttp.send();// On envoie la requête et le script php est exécuté.
    document.getElementById("logout").innerHTML=""; // On trouve l'élément html qui contient le lien
    //de déconnexion et on supprime le contenu de la balise en question. De la sorte on enlève le lien de la page.
    location.reload();
}

function shutdown(){
	var condition = true
  	var xmlhttp = new XMLHttpRequest();
  	xmlhttp.open("GET", "jsRouter.php?action=shutdown", true);
  	xmlhttp.send();
  	document.getElementById("FenetreContenu").innerHTML="<br><div class='row'><div class ='offset-by-three columns'><div class='six columns'>L'ordinateur Raspberry Pi de votre nichoir est en train de s'éteindre.<br><br>Vous pourrez le débrancher en toute sécurité dans quelques instants.</div></div></div>";
  	document.getElementById("FenetreMessage").classList.remove('cache');
  	document.getElementById("FenetreMessage").classList.add('montre');
}

function reboot(){
	var condition = true
  	var xmlhttp = new XMLHttpRequest();
  	xmlhttp.open("GET", "jsRouter.php?action=reboot", true);
  	xmlhttp.send();
  	document.getElementById("FenetreContenu").innerHTML="<br><div class='row'><div class ='offset-by-three columns'><div class='six columns'>L'ordinateur Raspberry Pi de votre nichoir est en train de redémarrer...<br><br>Veuillez patienter quelques instants ...</div></div></div>";
  	document.getElementById("FenetreMessage").classList.remove('cache');
  	document.getElementById("FenetreMessage").classList.add('montre');
}

function upgrade(){
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.open("GET", "jsRouter.php?action=upgrade", true);
  xmlhttp.send();
  document.getElementById("FenetreContenu").innerHTML="<br><div class='row'><div class ='offset-by-three columns'><div class='six columns'>L'ordinateur Raspberry Pi de votre nichoir est en train de se mettre à jour ...<br><br>Veuillez patienter quelques instants et rafraichir cette page.</div></div></div>";
  document.getElementById("FenetreMessage").classList.remove('cache');
  document.getElementById("FenetreMessage").classList.add('montre');

}

function distupgrade(){
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.open("GET", "jsRouter.php?action=distupgrade", true);
  xmlhttp.send();
  document.getElementById("FenetreContenu").innerHTML="<br><div class='row'><div class ='offset-by-three columns'><div class='six columns'>Le système d'exploitation du Raspberry Pi de votre nichoir est en train de se mettre à jour ...<br><br>Veuillez patienter quelques instants et rafraichir cette page.</div></div></div>";
  document.getElementById("FenetreMessage").classList.remove('cache');
  document.getElementById("FenetreMessage").classList.add('montre');

}

function addUser(){
  document.getElementById("FenetreContenu").innerHTML="<br><div class='row'><div class='offset-by-three columns'><div class='six columns'><label>Nom:</label><input class='u-full-width' type='text' placeholder='Nom' name='nom'></div></div></div><div class='row'><div class='offset-by-three columns'><div class='six columns'><label>mot de passe</label><input class='u-full-width' type='password' placeholder='mot de passe' name='passwd'></div></div></div>";
  document.getElementById("FenetreMessage").classList.remove('cache');
  document.getElementById("FenetreMessage").classList.add('montre');

}

function changerNom(){
        var nom = document.getElementById("nom_nichoir").value;
	console.log(nom);
  	var xmlhttp = new XMLHttpRequest();
  	xmlhttp.open("GET", "jsRouter.php?action=changeName&param1="+nom, true);
	xmlhttp.send();
      
}
