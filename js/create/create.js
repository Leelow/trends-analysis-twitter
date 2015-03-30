// On s'assure que l'on ne rentre que des nombres dans le champ length
$(document).ready(function () {
	$("#campaign_length").keypress(function (e) {
		if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57))
			return false;
	});					   
});

// Verrouille certains composants
function lock() {
	$('#cancel').attr('class', 'btn btn-danger col-lg-2 disabled');
	$('#create').attr('class', 'btn btn-success col-lg-2 disabled');
	$('#campaign_name').attr('readonly', true);
	$('#campaign_length').attr('readonly', true);
	$('#campaign_keywords').attr('readonly', true);
}

// Deverrouille certains composants
function unlock() {
	$('#campaign_name').attr('readonly', false);
	$('#campaign_length').attr('readonly', false);
	$('#campaign_keywords').attr('readonly', false);
	$('#cancel').attr('class', 'btn btn-danger col-lg-2');
	$('#create').attr('class', 'btn btn-success col-lg-2');
}

// Affiche un message					
function showMessage(message, type) {
	$('#message_box').attr('class', 'alert alert-dismissible alert-' + type);
	$('#message').html(message);
	$('#message_box').slideDown('slow');
}

// Desactive les warnings de tous les inputs
function resetWarningInput() {
	$('#input_name').attr('class', 'form-group');
	$('#input_begin').attr('class', 'form-group');
	$('#input_length').attr('class', 'form-group');
	$('#input_keywords').attr('class', 'form-group');
}				  

$('#create').click(function() {

	// On recupère les données du formulaire
	var c_name     = $('#campaign_name').val();
	var c_begin    = $('#campaign_begin').val();
	var c_length   = $('#campaign_length').val();
	var c_keywords = $('#campaign_keywords').val();
	var valid    = true;

	// On supprime tous les warnings
	resetWarningInput();

	// On affiche des warnings si nécessaire
	if(c_name == '') {
		$('#input_name').attr('class', 'form-group has-warning');
		valid = false;
	}
	if(c_begin == '') {
		$('#input_begin').attr('class', 'form-group has-warning');
		valid = false;
	}
	if(c_length == '') {
		$('#input_length').attr('class', 'form-group has-warning');
		valid = false;
	}
	if(c_keywords == '') {
		$('#input_keywords').attr('class', 'form-group has-warning');
		valid = false;								
	}

	if(valid == true) {

		// Si le champ length est invalide
		if(c_length > 600) {
			$('#input_length').attr('class', 'form-group has-warning');
			showMessage('La duée d\'une campagne ne peut pas dépasser 600 minutes.', 'warning');
		} else if(c_length < 3) {
			$('#input_length').attr('class', 'form-group has-warning');
			showMessage('La durée d\'une campagne ne peut être infèrieure à 3 minutes.', 'warning');
		} else {
		
			// On verrouille le formulaire
			lock();

			$.ajax({
				type: 'POST',
				url:  'ajax/createCampaign.php',
				data: {name:     c_name,
					   begin:    c_begin,
					   length:   c_length,
					   keywords: c_keywords},
				dataType: "text",
				success: function(data) {

					var parse_data = $.parseJSON(data);	

					if(parse_data['response'] == 'SUCCESS') {
						showMessage('La campagne <strong>' + c_name + '</strong> a bien été programmée au ' + c_begin +'.', 'success');				
					} else {
						var conflict = parse_data['conflict'];
						showMessage('Conflit avec la campagne <strong>' + conflict['name'] + '</strong> programmée du ' + conflict['begin'] + ' jusqu\'au ' + conflict['end'] + '.', 'warning');
						unlock();					
					}
				}
			});
			
		}
		
	} else {
		showMessage('Veuillez remplir tous les champs.', 'warning');
	}
});

// Affichage du calendrier
$('.form_time').datetimepicker({
	language:  'fr',
	weekStart: 1,
	todayBtn:  1,
	autoclose: 1,
	todayHighlight: 1,
	startView: 2,
	minView: 0,
	maxView: 1,
	forceParse: 0,
	minuteStep: 1,
	startDate: new Date((new Date).getTime() + 2 * 60000)
});