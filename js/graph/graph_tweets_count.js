function Graph_tweets_count() {

	google.setOnLoadCallback(drawChart);

	function drawChart() {
		$.ajax({
			url: 'ajax/getChartsTweetsPerInterval.php?id=' + getId(),
			dataType: "text",
			success: function(data) {
						
				var datas = $.parseJSON(data);
				var dataSet = new google.visualization.DataTable(datas);
				
				var options = {
				  //title: 'Company Performance',		  
				  legend: { position: 'none' },
				  curveType: 'function'
				};

				var chart = new google.visualization.LineChart(document.getElementById('graph_tweets_count'));

				chart.draw(dataSet, options);
				
			}
		});
	}

	var timer = setInterval(function() {
		drawChart();
		if(getState() != 'STARTED') {
			clearInterval(timer);
		}
	}, 60000);
	
}