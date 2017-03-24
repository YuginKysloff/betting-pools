

global.gInputText = function() {

	$('.g-input-text').each(function() {
		
		var title = $(this).attr('data-placeholder');
		$(this).parent().attr('data-placeholder',title);

		if ( $(this).val() == '' ) {}else{
			$(this).parent().addClass('js-active');
		}

		$(this).focus(function() {
			$(this).parent().addClass('js-active');
		})
		$(this).blur(function() {
			if ( $(this).val() == '' ) {
				$(this).parent().removeClass('js-active');
			}else{
				
			}
		})

	})

};

global.gInputText();