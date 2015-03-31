function Data() {

	function initialize() {

	$.ajax({
			url: 'ajax/getData.php?id=' + getId(),
			async : true,
			dataType: "text",
			success: function(data) {
				
				// On parse les données reçues au format JSON
				data = $.parseJSON(data);

				// Mots clés (singulier/pluriel)
				if(data['keywords'].length == 1)
					$('#title_keywords').text('Mot clé');
				else
					$('#title_keywords').text('Mots clés');
				
				// Affichage des mot clés
				for (var i in data['keywords']){
					$('#keywords').append(data['keywords'][i] + '<br>');//alert();
				}

			}
	});

	}

	function update() {

	$.ajax({
			url: 'ajax/getData.php?id=' + getId(),
			async : true,
			dataType: "text",
			success: function(data) {

				// On parse les données reçues au format JSON
				data = $.parseJSON(data);
				
				// Données diverses
				$('#size').text(data['size']);
				$('#total_tweets').text(data['total_tweets']);
				//$('#total_twittos').text(data['total_twittos']);
				$('#progressbar').css({width: data['percentage'] + '%'});	
				$('#c_state').text(data['state']);
				$('#begin').text(data['begin']);
				$('#step').text(data['step']);
				$('#end').text(data['end']);
				$('#percentage_campaign').text(data['percentage'] + '%' + ' (' + data['time_now'] + ')');
				
				// Mise à jour de l'affichage du statut
				switch(data['state']) {
					case 'CANCELLED':
						state = 'Interrompue';
						break;
					case 'ENDED':
						state = 'Terminée';
						break;
					case 'SCHEDULED':
						state = 'Programmée';
						break;
					default:
						state = 'En cours';
				} 
				$('.title_state').text(state);
				
				// Actions effectuées une fois que la campagne est terminée
				if(data['state'] != 'STARTED' || !isConnected()) {
					hideProgressFooter();

                    // Si l'on est connecté
                    if(isConnected())
					    showEndedCancelledZone();
					
					// On interrompt les mise à jours des données quand la campagne est terminée
					clearInterval(timer);
				}
		
			}
	});

	}

	initialize();
	update();
	var timer = setInterval(function(){update()}, 30000);

}