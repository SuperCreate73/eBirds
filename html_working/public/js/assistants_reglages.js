function logout() {
  // On utilise l'objet XMLHttpRequest pour faire une déconnexion en ajax.
  // Càd qu'on utilise Javascript pour appeler le script php de déconnexion
  // sans quitter ou recharger la page.
  fetch ("jsRouter.php",{
    method:'GET'
  });
    document.getElementById("logout").innerHTML=""; // On trouve l'élément html qui contient le lien
    //de déconnexion et on supprime le contenu de la balise en question. De la sorte on enlève le lien de la page.
    location.reload();
}

function shutdown(){
	var condition = true
  fetch ("jsRouter.php?action=shutdown",{
    method:'GET'
  });
    	document.getElementById("FenetreContenu").innerHTML="<br><div class='row'><div class ='offset-by-three columns'><div class='six columns'>L'ordinateur Raspberry Pi de votre nichoir est en train de s'éteindre.<br><br>Vous pourrez le débrancher en toute sécurité dans quelques instants.</div></div></div>";
  	document.getElementById("FenetreMessage").classList.remove('cache');
  	document.getElementById("FenetreMessage").classList.add('montre');
}

function reboot(){
	var condition = true
  fetch ("jsRouter.php?action=reboot",{
    method:'GET'
  });
  	document.getElementById("FenetreContenu").innerHTML="<br><div class='row'><div class ='offset-by-three columns'><div class='six columns'>L'ordinateur Raspberry Pi de votre nichoir est en train de redémarrer...<br><br>Veuillez patienter quelques instants ...</div></div></div>";
  	document.getElementById("FenetreMessage").classList.remove('cache');
  	document.getElementById("FenetreMessage").classList.add('montre');
}

function upgrade(){
  fetch ("jsRouter.php?action=upgrade",{
    method:'GET'
  });
  document.getElementById("FenetreContenu").innerHTML="<br><div class='row'><div class ='offset-by-three columns'><div class='six columns'>L'ordinateur Raspberry Pi de votre nichoir est en train de se mettre à jour ...<br><br>Veuillez patienter quelques instants et rafraichir cette page.</div></div></div>";
  document.getElementById("FenetreMessage").classList.remove('cache');
  document.getElementById("FenetreMessage").classList.add('montre');

}

function distupgrade(){
  fetch ("jsRouter.php?action=distupgrade",{
    method:'GET'
  });
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

  // recherche des login et password de l'utilisateur à créer
  var nameUser = document.getElementById("nameUser").value;
  var passwdUser = document.getElementById("passwdUser").value;

  // contrôle de validité du login et password
  if (nameUser.length < 3 || passwdUser.length < 6)
  {
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
  fetch ("jsRouter.php?action=saveUser&param1="+nameUser+"&param2="+passwdUser,{
    method:'GET'
  });

  fermerFenetre();
  document.location.reload();

}

function changerNom(){
  var nom = document.getElementById("nom_nichoir").value;

  fetch ("jsRouter.php?action=changeName&param1="+nom,{
    method:'GET'
  });
}

function removeUser(nom){

  fetch ("jsRouter.php?action=delUser&param1="+nom,{
    method:'GET'
  });
  document.getElementById(nom).innerHTML="";

}
function changeEmail(){

  // changer la fnction pour éviter un redémarrage intempestif du daemon
  var newMail = document.getElementById('email').value;
  fetch ("jsRouter.php?action=motionDetect&param1="+newMail,{
    method:'GET'
  });

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

function updateQualite(val) {
          document.getElementById('texteQualite').innerHTML=val;
        }
function updateIntervalle(val) {
          document.getElementById('texteIntervalle').innerHTML=val;
      }
function updateDetection(val) {
          document.getElementById('texteDetection').innerHTML=val;
    }
