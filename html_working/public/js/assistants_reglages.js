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
  document.getElementById("FenetreContenu").innerHTML="\
    <br>\
      <div class='row'>\
      <div class='offset-by-three columns'>\
        <div class='six columns'><label>Nom:</label> \
          <input id='nameUser' class='u-full-width' type='text' placeholder='Nom' name='nom'>\
        </div> \
      </div> \
    </div> \
    <div class='row'>\
      <div class='offset-by-three columns'>\
        <div class='six columns'><label>mot de passe</label>\
          <input id='passwdUser' class='u-full-width' type='password' placeholder='mot de passe' name='passwd'>\
    </div></div></div>\
    <div class='offset-by-seven columns'>\
      <div class='five columns'>\
        <input class='button u-full-width boutonLogin' value='Enregistrer les modifications' onclick='saveUser();'>\
      </div>\
    </div>\
";
  document.getElementById("FenetreMessage").classList.remove('cache');
  document.getElementById("FenetreMessage").classList.add('montre');

}

function saveUser() {
  var nameUser = document.getElementById("nameUser").value;
  var passwdUser = document.getElementById("passwdUser").value;
  console.log(nameUser, passwdUser);
  if (nameUser.length < 3 || passwdUser.length < 6){
    document.getElementById("FenetreContenu").innerHTML="\
      <br>\
      <div class='row'>\
      <div class='offset-by-three columns'>\
        <div class='six columns'>\
          <div class='u-full-width' type='text'>Le nom d'utilisateur ou le mot de passe ne sont pas valides\
        </div> \
      </div> \
    </div> \
        <div class='row'>\
        <div class='offset-by-three columns'>\
          <div class='six columns'><label>Nom:</label> \
            <input id='nameUser' class='u-full-width' type='text' placeholder='Nom' name='nom'>\
          </div> \
        </div> \
      </div> \
      <div class='row'>\
        <div class='offset-by-three columns'>\
          <div class='six columns'><label>mot de passe</label>\
            <input id='passwdUser' class='u-full-width' type='password' placeholder='mot de passe' name='passwd'>\
      </div></div></div>\
      <div class='offset-by-seven columns'>\
        <div class='five columns'>\
          <input class='button u-full-width boutonLogin' value='Enregistrer les modifications' onclick='saveUser();'>\
        </div>\
      </div>\
  ";

    return;
  }
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.open("GET", "jsRouter.php?action=saveUser&param1="+nameUser+"&param2="+passwdUser, true);
  xmlhttp.send();
  fermerFenetre();
  document.location.reload();

}

function changerNom(){
        var nom = document.getElementById("nom_nichoir").value;
	console.log(nom);
  	var xmlhttp = new XMLHttpRequest();
  	xmlhttp.open("GET", "jsRouter.php?action=changeName&param1="+nom, true);
	xmlhttp.send();

}
function removeUser(nom){
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.open("GET", "jsRouter.php?action=delUser&param1="+nom, true);
  xmlhttp.send();
  document.getElementById(nom).innerHTML="";

}
function changeEmail(){
  var newMail = document.getElementById('email').value;


  //xmlhttp.open("")

}
function changeMode(e){
  var mode = e.target.value;
  alert(mode);
}
function changeModeCamera(e){
  var mode = e.target.value;
  alert(mode);
}
function changeDefinitionCamera(e){
  var mode = e.target.value;
  alert(mode);
}
