function Graph_algo_polarity() {

	// ***** Réglages du graphique ***** //
	var color_newman_girvan = 'blue';
	var color_tf_idf 		= 'red';

	google.setOnLoadCallback(drawChart);

	function drawChart() {
		$.ajax({
			url: 'ajax/getChartsPolarityNewmanGirvan.php?id=' + getId(),
			dataType: "text",
			success: function(data) {
						
				// On recupère les données de l'algorithme de Newman Girvan
				var datas_NG = $.parseJSON(data);
				
				$.ajax({
					url: 'ajax/getChartsPolarityTfIdf.php?id=' + getId(),
					dataType: "text",
					success: function(data) {
					
						// On recupère les données de l'algorithme TF-IDF
						var datas_TFIDF = $.parseJSON(data);
						
						// On genère un dataSet à partir de ces données
						var dataSet_NG = new google.visualization.DataTable(datas_NG);
						var dataSet_TF_IDF = new google.visualization.DataTable(datas_TFIDF);
						var dataSet = google.visualization.data.join(dataSet_NG, dataSet_TF_IDF, 'full', [[0, 0]], [1], [1]);
						
						// On recupère les labels customs
						var custom_labels = [];
						for(i = 0; i < datas_TFIDF['rows'].length; i++)
							custom_labels.push({v:i, f:datas_TFIDF['rows'][i]['c'][0]['f']});
						
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
									color: color_newman_girvan,
									//labelInLegend: 'Newman Girvan',
									lineWidth: 2
								},
								1: {
									type: 'polynomial',
									degree: degree_poly,
									visibleInLegend: false,
									color: color_tf_idf,
									//labelInLegend: 'TF IDF',
									lineWidth: 2
								}
							},
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

						var chart = new google.visualization.LineChart(document.getElementById('graph_algo_polarity'));
						chart.draw(dataSet, options);
										
					}
				});
							
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