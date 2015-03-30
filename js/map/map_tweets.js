function Map_tweets() {

	var map;
	var markerCluster;
	var markers = [];
	var locations = [];
	var stopped = false;

	function initialize() {
		
		var mapOptions = {
			'zoom'             : 1,
			'center'           : new google.maps.LatLng(0, 0),
			'mapTypeId'        : google.maps.MapTypeId.ROADMAP,
			'disableDefaultUI' : true,
			'styles'           : [{"featureType":"administrative","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"landscape","elementType":"all","stylers":[{"visibility":"simplified"},{"hue":"#0066ff"},{"saturation":74},{"lightness":100}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"off"},{"weight":0.6},{"saturation":-85},{"lightness":61}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"visibility":"on"}]},{"featureType":"road.arterial","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road.local","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"water","elementType":"all","stylers":[{"visibility":"simplified"},{"color":"#5f94ff"},{"lightness":26},{"gamma":5.86}]}]
		}
		
		map = new google.maps.Map(document.getElementById('map'), mapOptions);

		markerCluster = new MarkerClusterer(map, markers);
		refreshMarkers();
	}

	function setMarkers(locations) {

		infowindow = new google.maps.InfoWindow();
	
		for (var i = 0; i < locations.length; i++) {
			var loc = locations[i];
			var myLatLng = new google.maps.LatLng(loc[2], loc[3]);
			
			var marker = new google.maps.Marker({
				position: myLatLng,
				map: map,
				title:'<strong>' + loc[0] + '</strong><br><br>' + urlify(loc[1]),
				'icon'     : 'assets/m0.png'
			});
			
			google.maps.event.addListener(marker, 'click', function () {
				infowindow.setContent(this.title);
				infowindow.open(map, this);
			});

			markers.push(marker);
		
		}
		
		markerCluster = new MarkerClusterer(map, markers);
	}

	function clearMarkers() {
	 
		markerCluster.clearMarkers();
	 
		// Loop through markers and set map to null for each
		for (var i=0; i<markers.length; i++)		 
			markers[i].setMap(null);
		
		// On vide el tableau de marqueurs
		markers = [];
		
	}

	function refreshMarkers() {
		
		// On ne rafraichit les marqueurs que si la campagne est en cours
		if(stopped == false) {
		
			$.ajax({
				url: 'ajax/getMaps.php?id=' + getId(),//document.getElementById('gmaps').className,
				//force to handle it as text
				dataType: "text",
				success: function(data) {
					
					var locations = $.parseJSON(data);				
					clearMarkers()
					setMarkers(locations);	
					
					
				}
			});
			
			if(getState() != 'STARTED')
				stopped = true;
			
		}

	}
	
	// http://stackoverflow.com/questions/1500260/detect-urls-in-text-with-javascript
	function urlify(text) {
		var urlRegex = /(https?:\/\/[^\s]+)/g;
		return text.replace(urlRegex, function(url) {
			return '<a href="' + url + '" target="_blank">' + url + '</a>';
		})
		// or alternatively
		// return text.replace(urlRegex, '<a href="$1">$1</a>')
	}

	initialize();
	setInterval(function(){refreshMarkers()}, 30000);

}