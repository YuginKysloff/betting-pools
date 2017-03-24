
faq.dropdown = function() {
	$('.faq__item').each(function() {
		
		var thisItem = $(this);

		thisItem.click(function() {
			var opening = (thisItem.attr('data-openin') === 'yes')?true:false;
			if (opening) {
				thisItem.removeClass('open').removeClass('js-open');
				thisItem.removeAttr('data-openin');
				thisItem.find('.faq__the-answer').slideUp();
			}else{
				$('.faq__item').removeClass('open').removeClass('js-open');
				$('.faq__item').removeAttr('data-openin');
				$('.faq__the-answer').slideUp();
				thisItem.addClass('js-open');
				thisItem.attr('data-openin','yes');
				thisItem.find('.faq__the-answer').slideDown();
			}
			
		})
	})
}

faq.dropdown();