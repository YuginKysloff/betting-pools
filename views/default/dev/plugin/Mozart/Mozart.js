if(!Cookies.get('timezone')) {
	var loc = Date.now();
	var time_zone = ((loc/1000 - stime)/60).toFixed(0);
	Cookies.set('timezone', time_zone);
}



var st_process = new Object();
function process(url, ident, before_id, form_id) {
	if(st_process[ident] == false) return;
	if(form_id == '') form_id = 'undefined';
	jQuery.ajax({
		url:     url,
		type:     "POST",
		dataType: "json",
		data: jQuery("#"+form_id).serialize(),

		beforeSend: function () {
			st_process[ident] = false;
			if(before_id) {
				$(before_id).addClass('js-process');
			}
		},
		success: function(response) {
			$.each(response, function(i, val) {
				$('#'+i).html(val);
			});
		},
		error: function(response) {
			messager('error', 'Unknown error');
		},
		complete: function () {
			st_process[ident] = true;
			if(before_id) {
				$(before_id).removeClass('js-process');
			}
		}
	});
}






$('.system-message').on('click', '.system-message__item',  function() {
	$(this).slideUp(400)
	thisBlock = $(this);
	function remove(thisBlock) {
		thisBlock.remove()
	}
	setTimeout(function() {
		remove(thisBlock);
	},500);
})


function messager (type, message, timeout) {
	(timeout == 0)? timeout = 99999:'';
	if ($('.system-message__item').length != 0){
		$('.system-message__item').last().after('<span class="system-message__item" style="animation-delay: 0s,'+timeout+'s" data-type="'+type+'">'+message+'</span>');
	}else{
		$('.system-message').html('<span class="system-message__item" style="animation-delay: 0s,'+timeout+'s" data-type="'+type+'">'+message+'</span>');
	}
}