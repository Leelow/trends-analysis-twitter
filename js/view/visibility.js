var id_progressFooter 	  = 'progress-footer';
var id_endedCancelledZone = 'ended_cancelled_zone';
var id_btnCancel          = 'cancel_button';
var id_btnDelete          = 'delete_button_started';
var id_colProgressbar     = 'col-progressbar';

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

function guestEndedCancelledZone() {
    $('#' + id_btnCancel).hide();
    $('#' + id_btnDelete).hide();
    $('#' + id_colProgressbar).attr('class', 'col-lg-10');
}