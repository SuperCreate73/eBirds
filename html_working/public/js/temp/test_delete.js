var selectionArray = new Array();

function selectFiles(myElement) {
	if (selectionArray.indexOf(myElement.id) = -1){
		selectionArray.push(myElement.id);
		myElement.classList.add("selected");
	}
	else {
		selectionArray.splice(selectionArray.indexOf(myElement.id), 1);
		$(myElement).classList.remove("selecte");
	}
}

function deleteFiles() {
	
	$.ajax({
		data: 'selectionArray=' + selectionArray,
		method: "POST",
		url: "jsRouter.php?action=deletefiles"
	})
//	.done(function {
//		removeFilesFromDisplay;
		// .removeClass()-remove class ; .detach()-remove selection ; .empty()- remove all;
//	});
}
