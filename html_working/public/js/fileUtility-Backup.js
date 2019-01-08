var selectionArray = new Array();
var oldID;
var toggleSelect = false;

function countSelection() {
	var locElement = document.getElementById('nbrSelected');
	locElement.textContent = locElement.getAttribute("value") + selectionArray.length.toString();
}

function selectAll() {
	var elements = document.getElementsByClassName('fileList');
	for (var count=0; count < (elements.length-1); count++) {
		selectionArray.push(elements[count].id);
		elements[count].classList.add('selected');
	}
	selectionArray.push(elements[elements.length-1].id);
	elements[elements.length-1].classList.add('highSelected');
	oldID = elements[elements.length-1].id;
	document.getElementById('btn_toggleSelect').classList.remove('toggleUnSelect');
	document.getElementById('btn_toggleSelect').classList.add('toggleSelect');
	toggleSelect = true;
	showThumbnail(oldID);
	countSelection();
}

function invertSelection (currentPane) {
	if (!toggleSelect) {return;}
	var elements = currentPane.getElementsByTagName('div')
	for (var count=0; count < (elements.length); count++) {
		if (selectionArray.indexOf(elements[count].id) == -1){
			selectionArray.push(elements[count].id);
			elements[count].classList.add('selected');
		}
		else {
			selectionArray.splice(selectionArray.indexOf(elements[count].id), 1);
			elements[count].classList.remove('selected');
		}
	}
	$('div').filter('.highSelected').each(function(){
		$(this).removeClass('highSelected');
	});
	if (selectionArray.length<1) {hideThumbnail();}
	else {
		document.getElementById(selectionArray[selectionArray.length-1]).classList.add('highSelected');
		oldID = selectionArray[selectionArray.length-1];
		countSelection();
		showThumbnail(oldID);
	}
}

function toggleSelectMode () {

	if (toggleSelect) {
		document.getElementById('btn_toggleSelect').classList.remove('toggleSelect');
		document.getElementById('btn_toggleSelect').classList.add('toggleUnSelect');
		toggleSelect = false;
		if (selectionArray.length > 0) {
			selectionArray.forEach(function(element) {
				document.getElementById(element).classList.remove('highSelected');
				document.getElementById(element).classList.remove('selected');
			});
			selectionArray = [];
			hideThumbnail();
		}
	}
	else {
		document.getElementById('btn_toggleSelect').classList.remove('toggleUnSelect');
		document.getElementById('btn_toggleSelect').classList.add('toggleSelect');
		toggleSelect = true;
	}
}

function showThumbnail(currentImage) {

    var img = new Image(),
        photoThumb = document.getElementById('photoThumb');


    img.addEventListener('load', function() {
        photoThumb.innerHTML = '';
        photoThumb.appendChild(img);
    });

	img.width = 213;
	img.heigth = 160;
    img.src = 'public/cameraShots/'+ currentImage;
	photoThumbContainer.classList.remove('photoUnselect');
    photoThumbContainer.classList.add('photoSelected');
	photoThumb.innerHTML = '<span>Chargement en cours...</span>';
}

function hideThumbnail() {
	
	photoThumb = document.getElementById('photoThumb');
	photoThumb.innerHTML= '';
	photoThumbContainer.classList.add('photoUnselect');
	photoThumbContainer.classList.remove('photoSelected');
}

function selectFiles(myElement) {
	
	if (selectionArray.indexOf(myElement.id) == -1){
		if (!toggleSelect && selectionArray.length > 0) {
			selectionArray = [];
			document.getElementById(oldID).classList.remove('highSelected');
		}	
		selectionArray.push(myElement.id);
		if (selectionArray.length > 1) {
			document.getElementById(oldID).classList.remove('highSelected');
			if (toggleSelect) {
				document.getElementById(oldID).classList.add('selected');
			}
		}
		myElement.classList.add('highSelected');
		oldID = myElement.id;
		countSelection();
		showThumbnail(myElement.id); 	
	}
	else {
		selectionArray.splice(selectionArray.indexOf(myElement.id), 1);
		if (selectionArray.length<1) {
			hideThumbnail();
		}
		else {
			countSelection();
			showThumbnail(selectionArray[(selectionArray.length-1)]);
			oldID=selectionArray[(selectionArray.length-1)];
			document.getElementById(oldID).classList.add('highSelected');
			document.getElementById(oldID).classList.remove('selected');
		}
		myElement.classList.remove('selected');
		myElement.classList.remove('highSelected');
	}
}

function deleteFiles() {
	// envoi de la liste de fichier au serveur (routeur javaScript) pour les effacer
	if (selectionArray.length<1) {return;}
	$.ajax({
		data: 'selectionArray=' + selectionArray,
		method: 'POST',
		url: "jsRouter.php?action=deletefiles",
		success: function(result){
			hideThumbnail();
			// affichage message de retours du serveur - a priori erreur
			if (result!== '' && result !== null) {
				document.getElementById("FenetreContenu").innerHTML="<br><div class='row'><div class ='offset-by-three columns'><div class='six columns'>"+result+"</div></div></div>";
  				document.getElementById("FenetreMessage").classList.remove('cache');
  				document.getElementById("FenetreMessage").classList.add('montre');
			}
			// efface les noms de fichiers sur la page active
			else {
				selectionArray.forEach(function(element) {
					document.getElementById(element).innerHTML="";
			})}
			selectionArray.forEach(function(element) {
				document.getElementById(element).classList.remove('highSelected');
				document.getElementById(element).classList.remove('selected');
			})
			selectionArray.splice(0,selectionArray.length);
			},
		error: function(eXhr,eStatus, eError){
			document.getElementById("FenetreContenu").innerHTML="<br><div class='row'><div class ='offset-by-three columns'><div class='six columns'>xhr: "+xhr+"</br>Status: "+status+"</br>Error :"+error+"</div></div></div>";
			document.getElementById("FenetreMessage").classList.remove('cache');
			document.getElementById("FenetreMessage").classList.add('montre');
        	}
	});
}

function showPhotoSelection () {
	
	if (selectionArray.length < 1) {return;}
	// location.href="../.php"
	var txtSelection = '';
	for (var count=0; count < (selectionArray.length); count++) {
		txtSelection +='<img src="public/cameraShots/'+selectionArray[count]+'">\n';
	}
//	console.log(txtSelection);
	document.getElementById("fotorama").innerHTML=txtSelection;
	$('.fotorama').fotorama();
	document.getElementById("photoSelectedGallery").classList.remove("cache");
	document.getElementById("photoSelectedGallery").classList.add("montre");
}

function hidePhotoSelection () {
	document.getElementById("fotorama").innerHTML='';
	document.getElementById("photoSelectedGallery").classList.remove("montre");
	document.getElementById("photoSelectedGallery").classList.add("cache");
}
//var element = document.getElementById('btn_toggleSelect');
//element.addEventListener('dblclick', function() {
//	alert("Select All !");
//});
