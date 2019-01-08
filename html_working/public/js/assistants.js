function logout() {
  // On utilise l'objet XMLHttpRequest pour faire une déconnexion en ajax.
  // Càd qu'on utilise Javascript pour appeler le script php de déconnexion
  // sans quitter ou recharger la page.
    var xmlhttp = new XMLHttpRequest(); // On crée une instance de l'objet
    xmlhttp.open("GET", "scripts/php/logout.php", true); // On ouvre la connexion vers le script php
    xmlhttp.send();// On envoie la requête et le script php est exécuté.
    document.getElementById("logout").innerHTML=""; // On trouve l'élément html qui contient le lien
    //de déconnexion et on supprime le contenu de la balise en question. De la sorte on enlève le lien de la page.

}
