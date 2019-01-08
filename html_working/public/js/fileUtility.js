var selectionArray = new Array();
var toggleSelect = false;

// fonction appelée lorsque la page est chargée pour mettre à jour la sélecion
window.onload = function() {
//$(function() {
	// initialisation du tableau de sélection avec le contenu du stockage local
	if (localStorage.getItem('selectionArray')) {
		selectionArray = JSON.parse(localStorage.getItem('selectionArray'));
	}
	// Mise à jour de l'affichage
	resetSelection();
}


function countSelection() {
	// Compte et affiche le nombre d'éléments sélectionnés
	//
	$('#nbrSelected').text($('#nbrSelected').attr('value') + selectionArray.length.toString());
}


function selectAll() {
	// Selection de toutes les images 
	//
	// parcours de tous les fichiers et stockage dans le tableau de sélection
	$('.fileList').each(function() {selectionArray.push($(this).attr('id'));});
	// mise à jour de l'affichage en changeant la classe
	$('.fileList').addClass('selected');
	// affichage du dernier élément avec la classe 'highSelected'
	$('.fileList:last').removeClass('selected').addClass('highSelected');
	$('#btn_toggleSelect').removeClass('toggleUnSelect').addClass('toggleSelect');
	toggleSelect = true;
	showThumbnail($('.fileList:last').attr('id'));
	countSelection();
	localStorage.setItem('selectionArray', JSON.stringify(selectionArray));
}


function invertSelection (currentPane) {
	// Inverse la sélection de la colonne double clickée
	//
	if (!toggleSelect) {return;}

	// mise à jour du tableau contenant la sélection active
	var elements = $('#'+currentPane+' .fileList');
	for (var count=0; count < (elements.length); count++) {
		if (selectionArray.indexOf(elements[count].id) == -1){selectionArray.push(elements[count].id);}
		else {selectionArray.splice(selectionArray.indexOf(elements[count].id), 1);}
	}
	// inversion de la sélection en adaptant les classes
	$('#'+currentPane+' .highSelected').removeClass('highSelected').addClass('selected');
	$('#'+currentPane+' .fileList').toggleClass('selected');
	// si plus de sélection, cache l'aperçu
	if (selectionArray.length<1) {hideThumbnail();}
	else {
		// affiche le dernier élément en highSelected
		$('.highSelected').removeClass('highSelected').addClass('selected');
		document.getElementById(selectionArray[selectionArray.length-1]).classList.add('highSelected');
		document.getElementById(selectionArray[selectionArray.length-1]).classList.remove('selected');

//		$('#'+selectionArray[selectionArray.length-1]).addClass('highSelected').removeClass('selected');
		// compte le nombre de fichier sélectionné pour affichage dans l'aperçu
		countSelection();
		// montre l'image du fichier actif dans l'aperçu
		showThumbnail(selectionArray[selectionArray.length-1]);
	}
	localStorage.setItem('selectionArray', JSON.stringify(selectionArray));
}


function toggleSelectMode () {
	// Active ou désactive le mode de sélection.  Si désactivé, annule la sélection courante.
	//
	if (toggleSelect) {
		// sort du mode 'sélection' et efface la liste courante
		$('#btn_toggleSelect').removeClass('toggleSelect').addClass('toggleUnSelect');
		toggleSelect = false;
		$('.fileList').removeClass('highSelected').removeClass('selected');
		if (selectionArray.length > 0) {
			selectionArray = [];
			hideThumbnail();
		}
	}
	else {
		// active le mode 'sélection'
		$('#btn_toggleSelect').removeClass('toggleUnSelect').addClass('toggleSelect');
		toggleSelect = true;
	}
	localStorage.setItem('selectionArray', JSON.stringify(selectionArray));
}


function showThumbnail(currentImage) {
	// Montre l'aperçu de l'image active
	//
	// déclaration des variables
    var img = new Image(),
        photoThumb = document.getElementById('photoThumb');

	// fonction de 'callback' appelée quand l'image est chargée
    img.addEventListener('load', function() {
		// on vide le contenu html et on ajoute la photo
		$('#photoThumb').html('').append(img);
  //      photoThumb.innerHTML = '';
    //    photoThumb.appendChild(img);
    });
	
	// définition des caractéristiques de l'image
	img.width = 213;
	img.heigth = 160;
	// assignation de la source de l'image, qui lance également son téléchargement
    img.src = 'public/cameraShots/'+ currentImage +'.jpg' ;
	// montre l'aperçu photo
	if ($('#photoThumbContainer').hasClass('photoUnselect')) {
		$('#photoThumbContainer').removeClass('photoUnselect').addClass('photoSelected');
		//active la transition CSS
		photoThumbContainer.style.height='170px';
		$('html, body').animate({scrollTop:($('html, body').scrollTop()+180)}, 1000);
	}
	// affichage d'un message d'attente
	$('#photoThumb').html('<span>Chargement en cours...</span>');
	// la page est scrollée pour compenser l'aparition du thumbnail
}


function hideThumbnail() {
	// Cache l'aperçu photo en changeant les classes
	//
	// le contenu html est effacé
	$('#photoThumb').html('');
	// les classes sont adaptées pour gérer la visibilité
	$('#photoThumbContainer').addClass('photoUnselect').removeClass('photoSelected');	
	// active la transition CSS
	photoThumbContainer.style.height='0px';
	// la page est scrollée pour compenser la disparition du thumbnail
	if ($('html, body').scrollTop()-180 < 0) {$('html, body').animate({scrollTop:0}, 1000);}
	else {$('html, body').animate({scrollTop:($('html, body').scrollTop()-180)}, 1000);}
}


function selectFiles(myElement) {
	// Ajoute l'élément actif dans la sélection ou le retire si il est déjà présent
	//
	// si l'élément n'est pas encore dans le tableau
	if (selectionArray.indexOf(myElement.id) == -1){
		// si le tableau est vide et la sélection inactive, on enlève simplement la classe 'highSelected'
		if (!toggleSelect && selectionArray.length > 0) {
			selectionArray = [];
			$('.highSelected').removeClass('highSelected');
		}
		// ajout de l'élément dans le tableau
		selectionArray.push(myElement.id);
		// si plusieurs éléments dans le tableau, on change les éléments highSelected en selected
		if (selectionArray.length > 1) { $('.highSelected').removeClass('highSelected').addClass('selected'); }
		// ajout de la classe highSelected à l'élément actif
		myElement.classList.add('highSelected');
		// compte le nombre de fichier sélectionné pour affichage dans l'aperçu
		countSelection();
		// montre l'image du fichier actif dans l'aperçu
		showThumbnail(myElement.id); 	
	}
	else {
		// efface l'id de l'élément courant de la sélection active
		selectionArray.splice(selectionArray.indexOf(myElement.id), 1);
		// enlève les classes de sélection de l'élément courant
		myElement.classList.remove('selected');
		myElement.classList.remove('highSelected');
		// cache la fenêtre de l'aperçu si la sélection active est vide
		if (selectionArray.length<1) {
			hideThumbnail();
		}
		else {
			// compte le nombre de fichier sélectionné pour affichage dans l'aperçu
			countSelection();
			// montre l'image du dernier fichier dans l'aperçu
			showThumbnail(selectionArray[(selectionArray.length-1)]);
			// ajout des classes de sélection au dernier élément
			document.getElementById(selectionArray[(selectionArray.length-1)]).classList.add('highSelected');
			document.getElementById(selectionArray[(selectionArray.length-1)]).classList.remove('selected');
		}
	}
	// mise à jour du stockage local du tableau de sélection
	localStorage.setItem('selectionArray', JSON.stringify(selectionArray));
}


function deleteFiles() {
	// envoi de la liste de fichier au serveur (routeur javaScript) pour les effacer
	//
	if (selectionArray.length<1) {return;}
	$.ajax({
		data: 'selectionArray=' + selectionArray,
		method: 'POST',
		url: "jsRouter.php?action=deletefiles",
		success: function(result){
			hideThumbnail();
			// affichage message de retours du serveur - a priori erreur
			if (result!== '' && result !== null) {
				$('#FenetreContenu').html("<br><div class='row'><div class ='offset-by-three columns'><div class='six columns'>"+result+"</div></div></div>");
  				$('#FenetreMessage').removeClass('cache').addClass('montre');
			}
			// efface les noms de fichiers sur la page active
			else {
				selectionArray.forEach(function(element) {
					document.getElementById(element).innerHTML="";
			})}
			$('.fileList').removeClass('highSelected selected');
			selectionArray =[];
			},
		error: function(eXhr,eStatus, eError){
			$('#FenetreContenu').html("<br><div class='row'><div class ='offset-by-three columns'><div class='six columns'>xhr: "+xhr+"</br>Status: "+status+"</br>Error :"+error+"</div></div></div>");
  			$('#FenetreMessage').removeClass('cache').addClass('montre');
        	}
	});
}


function showPhotoSelection () {
	// Affiche la page 'PhotoThumb' avec la sélection active 
	//
	// si pas de sélection, affiche tout
	if (selectionArray.length < 1) { 
		console.log ('array lenght = 0');
		location.href="index.php?page=photothumb";
		return false;
	}

	var selectionArrayMod = new Array();
	selectionArray.forEach (function(element){
		var elementChild = document.getElementById(element).firstChild.nextSibling;
		if (elementChild.classList.contains("jpg")) {element += '.jpg';}
		else {element += '.mpeg';}
		selectionArrayMod.push(element);
	});
	// envoi de la liste active au serveur en POST via un formulaire caché
	document.hiddenForm.valueTable.value = JSON.stringify(selectionArrayMod);
	document.hiddenForm.method='POST';
	document.hiddenForm.action = "index.php?page=photothumb&action=table";
	document.hiddenForm.submit(); 
}


function resetSelection () {
	// Affiche la sélection en mémoire à l'ouverture de la page
	// 
	if (selectionArray.length<1) {return;}
	$('.highSelected').removeClass('highSelected');
	selectionArray.forEach(function(element) {
		document.getElementById(element).classList.remove('highSelected');
		document.getElementById(element).classList.add('selected');
	});
	document.getElementById(selectionArray[selectionArray.length-1]).classList.add('highSelected');
	document.getElementById(selectionArray[selectionArray.length-1]).classList.remove('selected');
	countSelection();
	showThumbnail(selectionArray[selectionArray.length-1]);
	if (selectionArray.length > 1) {
		$('#btn_toggleSelect').removeClass('toggleUnSelect').addClass('toggleSelect');
		toggleSelect = true;
	}
}


function downloadFiles () {
	// Télécharge la sélection sous forme de fichier zip créé à la volée par le serveur
	//
	$.ajax({
		data: 'selectionArray=' + selectionArray,
		method: 'POST',
		url: "jsRouter.php?action=download",
		success: function(result){
			if (result!== '' && result !== null) {
				window.open('Archive.zip');
//				$('#FenetreContenu').html("<br><div class='row'><div class ='offset-by-three columns'><div class='six columns'>"+result+"</div></div></div>");
//				$('#FenetreMessage').removeClass('cache').addClass('montre');
//				selectionArray.forEach(function(element) {
//					document.getElementById(element).innerHTML=document.getElementById(element).innerHTML+"downloaded";
//			})
				}
			},
		error: function(eXhr,eStatus, eError){
			$('#FenetreContenu').html("<br><div class='row'><div class ='offset-by-three columns'><div class='six columns'>xhr: "+xhr+"</br>Status: "+status+"</br>Error :"+error+"</div></div></div>");
  			$('#FenetreMessage').removeClass('cache').addClass('montre');
        	}
	});
}


function commentFiles () {
	// Commente les fichiers sélectionnés
	//
	// Apparition d'une zone de saisie avec boutons 'OK' et 'Cancel'
	// Si CANCEL annulation
	// Si OK 
	// 		Test de validation du commentaire
	// 		envoi du commentaire et du tableau de sélection au serveur
	//
	// 		Si réponse OK
	// 			affichage des icones commentaires des fichiers sélectionnés
	// 		Sinon
	// 			affichage de l'erreur

}


function tagFiles () {
	// Ajoute un ou plusieurs tags aux fichier sélectionnés
	//
	// tags possibles :
	// 		favori, visite, nidification, nourrissage, oeufs, partie fine, oisillons, intrusion, ??
	//
	// Affichage d'un menu déroulant avec les choix possibles etboutons 'OK et 'CANCEL'
	// Si CANCEL - annulation
	// si OK
	// 		Test de validation du/des tags
	// 		envoi des tags et du tableau de sélection au serveur
	//
	// 		Si réponse OK
	// 			affichage des icones tags des fichiers sélectionnés
	// 		Sinon
	// 			affichage de l'erreur
}
