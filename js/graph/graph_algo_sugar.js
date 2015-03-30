function Graph_algo_sugar() {

	// ***** Réglages du graphique ***** //
	var color_sugar = 'green';

	google.setOnLoadCallback(drawChart);

	function drawChart() {
						
	$.ajax({
		url: 'ajax/getChartsPolaritySugar.php?id=' + getId(),
		dataType: "text",
		success: function(data) {
		
			// On recupère les données de l'algorithme TF-IDF
			var datas_SUGAR = $.parseJSON(data);
			
			// On genère un dataSet à partir de ces données
			var dataSet_SUGAR = new google.visualization.DataTable(datas_SUGAR);
			
			// On recupère les labels customs
			var custom_labels = [];
			for(i = 0; i < datas_SUGAR['rows'].length; i++)
				custom_labels.push({v:i, f:datas_SUGAR['rows'][i]['c'][0]['f']});
			
			// On ajuste le degré du polynome
			//var degree_poly = Math.min(Math.max(3, custom_labels.length / 5), 50);
			var degree_poly = Math.round(Math.min(Math.max(3, custom_labels.length / 5), 50));
			
			var options = {
				//title: '',
				// Courbes de tendances						
				trendlines: {
					0: {
						type: 'polynomial',
						degree: degree_poly,
						visibleInLegend: false,
						color: color_sugar,
						//labelInLegend: 'Newman Girvan',
						lineWidth: 2
					}
				},
				color: color_sugar,
				// Desactivation du tooltip
				tooltip : {
				  trigger: 'none'
				},
				// Réduction du padding
				chartArea: {'width': '80%', 'height': '70%'},
				// Positionnement de la légende
				legend: { position: 'bottom' },
				lineWidth: 0.5,
				visibleInLegend: false,
				vAxis: { 
					title: 'Polarité',
					viewWindowMode:'explicit',
					viewWindow:{
						max:1.1,
						min:-1.1
					}
				},
				hAxis: {
					ticks: custom_labels,
					gridlines: {
						color: 'transparent'
					}
				}
			};

			
			function selectHandler() {
			  var selectedItem = chart.getSelection()[0];
			  if (selectedItem) {
				var topping = dataSet_SUGAR.getValue(selectedItem.row, 0);
				// On affiche le tweet correspondant
				display_tweet(topping);
			  }
			}
			
			var chart = new google.visualization.LineChart(document.getElementById('graph_algo_sugar'));
			
			//
			google.visualization.events.addListener(chart, 'select', selectHandler);					

			
			chart.draw(dataSet_SUGAR, options);
			
						chart.setSelection([{row:0,column:1}]);
							
		}
	});
							
	}

	// On rafraichit le graphique toutes les 60 secondes si la campagne est en cours
	var timer = setInterval(function() {
		drawChart();
		if(getState() != 'STARTED') {
			clearInterval(timer);
		}
	}, 60000);

}