/* 	Scripts JavaScript liés à la page viewGraphique  */
/* 	-----------------------------------------------  */
var tExt = true;
var tInt = true;
var hExt = true;
var hInt = true;
var container = document.getElementById('graphique');

var options = {
	drawPoints:{size: 5, style: 'circle'},
   	maxHeight: '450px',
   	orientation: 'top',
   	clickToUse: false,
   	dataAxis: {alignZeros: true, left:{title:{text:"Température"}}, right:{title:{text:"Humidité"}}}
 };

var groupes = new vis.DataSet();
groupes.add(
  	{id: 0,className:"styleGraph", content: "Temp. Ext.", options: {drawPoints: false}}
);
groupes.add(
  	{id: 1, className:"styleGraph2", content: "Temp. Int.", options: {drawPoints: false}}
);
groupes.add(
  	{id: 2, className:"styleGraph3", content: "Hum. Ext.", options: {drawPoints: false, yAxisOrientation: 'right'}}
);
groupes.add(
  	{id: 3, className:"styleGraph4", content: "Hum. Int.", options: {drawPoints: false,yAxisOrientation: 'right'}}
);

// <?= "var donnees = ".json_encode($tableau).";"; ?>

 var dataset = new vis.DataSet(donnees);
 //dataset.add({id: 0, content: "Temp Ext"});

 var graph2D = new vis.Graph2d(container, dataset, groupes, options);

function toggleText(){
  var prop = {};
  prop[0] = tExt = !tExt;
  graph2D.setOptions({groups:{visibility:prop}});
}

function toggleTint(){
  var prop = {};
  prop[1] = tInt = !tInt;
  graph2D.setOptions({groups:{visibility:prop}});
}

function toggleHext(){
  var prop = {};
  prop[2] = hExt = !hExt;
  graph2D.setOptions({groups:{visibility:prop}});
}

function toggleHint(){
  var prop = {};
  prop[3] = hInt = !hInt;
  graph2D.setOptions({groups:{visibility:prop}});
}

