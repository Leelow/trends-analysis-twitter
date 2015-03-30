// Lien de suppression
function delete_link() {
	$.ajax({
		url: 'ajax/deleteCampaign.php?id=' + getId(),
		async : true,
		dataType: "text",
		success: function(data) {
			
			document.location = 'index.php';

		}
	});
};

function Interaction() {

	// Bouton de suppression (campagne en cours)
	$('#delete_button_started').click(function() {
		// Si c'est la première fois que l'on clique sur le boutton
		if($('#delete_button_started').text() == 'Supprimer la campagne')
			$('#delete_button_started').text('Confirmer la suppression ?');
		else {
			$.ajax({
				url: 'ajax/deleteCampaign.php?id=' + getId(),
				async : true,
				dataType: "text",
				success: function(data) {
					
					document.location = 'index.php';

				}
			});
		}
	});

	// Bouton de suppression (campagne terminée ou annulée)
	$('#delete_button_ended_cancelled').click(function() {

		// Si c'est la première fois que l'on clique sur le boutton
		if($('#delete_button_ended_cancelled').text() == 'Supprimer la campagne')
			$('#delete_button_ended_cancelled').text('Confirmer la suppression ?');
		else {
			$.ajax({
				url: 'ajax/deleteCampaign.php?id=' + getId(),
				async : true,
				dataType: "text",
				success: function(data) {
					
					document.location = 'index.php';

				}
			});
		}
	});

	// Bouton d'annulation
	$('#cancel_button').click(function() {

		// Si c'est la première fois que l'on clique sur le boutton
		if($('#cancel_button').text() == 'Arreter la campagne')
			$('#cancel_button').text('Confirmer l\'arrêt ?');
		else {
			$.ajax({
				url: 'ajax/cancelCampaign.php?id=' + getId(),
				async : true,
				dataType: "text",
				success: function(data) {
					
					$('#cancel_button').fadeOut(function() {
						$('#cancel_button').remove();
						$('#col-progressbar').attr('class', 'col-lg-8');
						Data();
					});

				}
			});
		}
	});
	
}