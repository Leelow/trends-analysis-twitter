(function(){

	var data = [],
		barsCount = 50,
		labels = new Array(barsCount),
		updateDelayMax = 1500,
		$id = function(id){
			return document.getElementById(id);
		},
		random = function(max){ return Math.round(Math.random()*100)},
		helpers = Chart.helpers;


	Chart.defaults.global.responsive = true;


	for (var i = barsCount - 1; i >= 0; i--) {
		data.push(Math.round(Math.random() * 100));
	};
	new Chart($id('hero-bar').getContext('2d')).Bar({
		labels : labels,
		datasets : [{
			fillColor : 'rgba(91,144,191,0.05)',
			data : data
		}]
	},{
		showScale : false,
		barShowStroke : false,
		barValueSpacing: 1,
		showTooltips : false,
		onAnimationComplete : function(){
			// Get scope of the hero chart during updates
			var heroChart = this,
				timeout;
			// Stop this running every time the update is fired
			this.options.onAnimationComplete = randomUpdate;

			this.options.animationEasing = 'easeOutQuint';

			randomUpdate();

			function randomUpdate(){
				heroChart.stop();
				clearTimeout(timeout);
				// Get a random bar
				timeout = setTimeout(function(){
					var randomNumberOfBars = Math.floor(Math.random() * barsCount),
						i;
					for (i = randomNumberOfBars - 1; i >= 0; i--) {
						heroChart.datasets[0].bars[Math.floor(Math.random() * barsCount)].value = Math.round(Math.random() * 100);
					};
					heroChart.update();
				},Math.random() * updateDelayMax);
			};
		}
	});
})();