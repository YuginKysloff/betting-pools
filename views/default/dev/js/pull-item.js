
index.pull = function() {
	

	$('.pull').each(function() {
		var thisBlock = $(this);
		var itemFull = $(this).find('.pull__item[data-procent="100"]').length;
		console.log(itemFull);
		$(this).find('.pull__trigger').animate({'margin-left': itemFull*117},itemFull*3200, 'linear');

		$(this).find('.pull__item').each(function() {
			var thisItem = $(this);
			var delay = thisItem.index()*3000;
			var procent = 1.89;
			var progress = parseFloat(thisItem.attr('data-procent'));
			
			setTimeout(function() {
				if(progress > 0 && progress <= 100){
					progress =  progress * procent;
					thisItem.find('.pull__block--2').show();
				}else{
					progress = 0;
				}
				thisItem.find('.pull__block--2').height(progress+'px');

			},delay)
		})


		thisBlock.find('.pull__open-info').click(function() {
			
			thisBlock.find('.pull__info').slideToggle();
			thisBlock.toggleClass('js-open');
		})
	})

}
index.pull();