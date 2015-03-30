// /**
 // * Function : dump()
 // * Arguments: The data - array,hash(associative array),object
 // *    The level - OPTIONAL
 // * Returns  : The textual representation of the array.
 // * This function was inspired by the print_r function of PHP.
 // * This will accept some data as the argument and return a
 // * text that will be a more readable version of the
 // * array/hash/object that is given.
 // * Docs: http://www.openjs.com/scripts/others/dump_function_php_print_r.php
 // */
function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	
	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}

// var tweets_count;
// var window;

// function initialize() {


	// refreshValues(false);

	// ctx = document.getElementById("canvas").getContext("2d");
	// window.myLine = new Chart(ctx).Line(tweets_count, { //tweets_count
		// responsive: true,
		// scaleShowVerticalLines: false,
		// pointDot : false,
		// pointHitDetectionRadius : 0,
		// bezierCurve: true,
		// showTooltips: false,
		// showScale: true
		// // Boolean - If we want to override wdith a hard coded scale
		// //scaleOverride: true,
		// // Number - The number of steps in a hard coded scale
		// //scaleSteps: 4,
		// // Number - The value jump in the hard coded scale
		// //scaleStepWidth: 100,
		// // Number - The scale starting value
		// //scaleStartValue: 400
	// });
	
// }

// function refreshValues(async_bool) {
	
	// var json;
	
	// //start ajax request
	// $.ajax({
		// url: '/app/campaign/' + document.getElementById('tweet_graph').className + '/tweets_count.json',
		// async : async_bool,
		// //force to handle it as text
		// dataType: "text",
		// success: function(data) {
		
			// tweets_count = $.parseJSON(data);
			
		// }
	// });

	// return json;
	
// }

// function refreshPoints() {
	
	// //start ajax request
	// $.ajax({
		// url: '/app/campaign/' + document.getElementById('tweet_graph').className + '/tweets_count.json',
		// async : true,
		// //force to handle it as text
		// dataType: "text",
		// success: function(data) {
			
			// var update = $.parseJSON(data);
				// //alert(dump(update['datasets'][0]['data']));
			
			// //if
			
			// window.addData([50, 50]);
			
		// }
	// });

// }

// initialize();
// refreshPoints();
// setInterval(function(){refreshPoints()}, 3000);

var datas;

$.ajax({
		url: '/app/campaign/' + document.getElementById('tweet_graph').className + '/tweets_count.json',
		async : false,
		dataType: "text",
		success: function(data) {

			// On parse les données reçues au format JSON
			datas = $.parseJSON(data);
		
		}
	});

// var datas = {
      // labels: [0],
      // datasets: [
          // {
              // fillColor: "rgba(220,220,220,0.2)",
              // strokeColor: "rgba(220,220,220,1)",
              // pointColor: "rgba(220,220,220,1)",
              // pointStrokeColor: "#fff",
              // data: [0]
          // },
          // {
              // fillColor: "rgba(151,187,205,0.2)",
              // strokeColor: "rgba(151,187,205,1)",
              // pointColor: "rgba(151,187,205,1)",
              // pointStrokeColor: "#fff",
              // data: [0]
          // }
      // ]
    // };

// var canvas = document.getElementById('chart_tweets'),
    // ctx = canvas.getContext('2d'),
    // datas,
    // latestLabel = datas.labels[6];

var canvas;
var myLiveChart;
var initialized = false;
	
	// TEST
/*if(datas['labels'].length >= 2) {
	
	// Reduce the animation steps for demo clarity.
	canvas = document.getElementById('chart_tweets'),
		ctx = canvas.getContext('2d'),
		datas;
		
	myLiveChart = new Chart(ctx).Line(datas,
		{animationSteps: 15,
		responsive: true,
		scaleShowVerticalLines: false,
		pointDot : false,
		pointHitDetectionRadius : 0,
		bezierCurve: true,
		showTooltips: false,
		showScale: true
		});

}	*/
	
// Recupère les dernières données et les ajoute au graphique
function addValue() {

	$.ajax({
		url: '/app/campaign/' + document.getElementById('tweet_graph').className + '/tweets_count.json',
		async : true,
		//force to handle it as text
		dataType: "text",
		success: function(data) {
			
			// On recupère le précèdent 
			var new_label  = update['labels'][update['labels'].length-1];
			
			// On parse les données reçues au format JSON
			var update = $.parseJSON(data);
			
			// TEST
			if(update['labels'].length >= 2) {
				
				if(update['labels'].length == 2 && initialized == false) {
				
					initialized = true;
				
					// Reduce the animation steps for demo clarity.
					canvas = document.getElementById('chart_tweets'),
						ctx = canvas.getContext('2d'),
						update;
						
					myLiveChart = new Chart(ctx).Line(update,
						{animationSteps: 15,
						responsive: true,
						scaleShowVerticalLines: false,
						pointDot : false,
						pointHitDetectionRadius : 0,
						bezierCurve: true,
						showTooltips: false,
						showScale: true
						});
				} else {
						
					// On recupère le dernier label du fichier
					
					var last_label = datas['labels'][datas['labels'].length - 1];
					
					//alert(new_label + ' ' + last_label);
					
					// On le compare avec le plus récent du graphe pour s'assurer que c'est bien un nouveau label
					//if((typeof last_label === 'undefined' && typeof new_label !== 'undefined') || (new_label != last_label)) {
					if(new_label != last_label) {
					
						// On recupère le dernier élèment de la liste s'il existe pour les deux datasets
						var data_length = update['labels'].length;				
						var data1 = update['datasets'][0]['data'][data_length-1];
						var data2 = update['datasets'][1]['data'][data_length-1];
						
						// On met à jour le graphique
						myLiveChart.addData([data1, data2], new_label);
					
					}
					
				}
			
			}
			
		}
	});

}


setInterval(function(){ addValue() }, 3000);