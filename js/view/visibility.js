var id_progressFooter 	  = 'progress-footer';
var id_endedCancelledZone = 'ended_cancelled_zone';

function showProgressFooter() {
	$('#' + id_progressFooter).fadeIn();
}

function hideProgressFooter() {
	$('#' + id_progressFooter).fadeOut();
}

function showEndedCancelledZone() {
	$('#' + id_endedCancelledZone).fadeIn();
}

function hideEndedCancelledZone() {
	$('#' + id_endedCancelledZone).fadeOut();
}