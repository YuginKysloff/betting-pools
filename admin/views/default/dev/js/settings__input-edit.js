var settings = {};

settings.editInput = function() {
	$('.settings-table .fa-pencil-square-o').click(function() {

	var all = $(this).hasClass('global');
	var globalOpen = $('.fa-pencil-square-o').attr('data-globalOpen');

	if (all == true){
			console.log(globalOpen);
		if (globalOpen == 'no') {
			$(this).parent().parent().parent().parent().find('.g-input').removeClass('settings__disabled');
			$(this).parent().parent().parent().parent().find('.g-input input').removeAttr('readonly');
			$('.fa-pencil-square-o').attr('data-open','yes');
			$(this).attr('data-globalOpen','yes');
		}else{
			$(this).parent().parent().parent().parent().find('.g-input').addClass('settings__disabled');
			$(this).parent().parent().parent().parent().find('.g-input input').attr('readonly', '');
			$('.fa-pencil-square-o').attr('data-open','no');
			$(this).attr('data-globalOpen','no')
		}
	}else{
		if ($(this).attr('data-open') == 'no') {
			$(this).parent().parent().find('.g-input').removeClass('settings__disabled');
			$(this).parent().parent().find('.g-input input').removeAttr('readonly');
			$(this).attr('data-open','yes');
		}else{
			$(this).parent().parent().find('.g-input').addClass('settings__disabled');
			$(this).parent().parent().find('.g-input input').attr('readonly', '');
			$(this).attr('data-open','no');
		}
	}
});
}


settings.editInput();




// $('.settings-table .fa-pencil-square-o.global').click(function() {
// 	settingsFormsStatus = 'disabled'
	// $(this).parent().parent().parent().parent().find('.g-input').removeClass('settings__disabled');
	// $(this).parent().parent().parent().parent().find('.g-input input').removeAttr('disabled')
// });

