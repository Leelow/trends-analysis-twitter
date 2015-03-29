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

function ajax(_url, _async = true) {

	var datas;

	$.ajax({
			url: _url,
			async : _async,
			dataType: "text",
			success: function(data) {

				// On parse les données reçues au format JSON
				datas = $.parseJSON(data);
			
			}
		});
		
	return datas;
}

var campaign_name = document.getElementById('tweet_graph').className;
var initialized = false;
var update = ajax('/app/campaign/' + campaign_name + '/tweets_count.json', false);


if(update.length >= 2)
	initialized = true;

var canvas = document.getElementById('chart_tweets'),
    ctx = canvas.getContext('2d'),
    update;//,
    //latestLabel = startingData.labels[6];

// Reduce the animation steps for demo clarity.
var myLiveChart = new Chart(ctx).Line(update, {animationSteps: 25});




setInterval(function(){
  // Add two random numbers for each dataset

	// On recupère l'ancien label
	if(typeof update !== 'undefined')
		var last_label = update['labels'][update['labels'].length-1];
	else
		var last_label = 'undefined';
  
	// On recupère le fichier du comptage de tweets
	update = ajax('/app/campaign/' + campaign_name + '/tweets_count.json', false);
	
	if(update['labels'].length >= 2) {
	
		if(update['labels'].length == 2 && initialized == false) {
		
			initialized = true;
		
			canvas = document.getElementById('chart_tweets'),
				ctx = canvas.getContext('2d'),
				update;//,
				//latestLabel = startingData.labels[6];

			// Reduce the animation steps for demo clarity.
			myLiveChart = new Chart(ctx).Line(update, {animationSteps: 25});
		
		} else {
	
	
			var new_label = update['labels'][update['labels'].length-1];
		
			if(new_label != last_label) {
		  
				// On recupère le dernier élèment de la liste s'il existe pour les deux datasets
				var data_length = update['labels'].length;				
				var data1 = update['datasets'][0]['data'][data_length-1];
				var data2 = update['datasets'][1]['data'][data_length-1];
				
				//alert(data1 + ' ' + data2 + ' ' + new_label);
				
				// On met à jour le graphique
				myLiveChart.addData([data1, data2], new_label);
				
			}
			
		}
	
	}

}, 30000);