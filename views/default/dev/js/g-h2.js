
index.h2 = function() {
	$('.g-h2').each(function() {
		var text = $(this).html()
		$(this).html('<span class="g-h2__line">'+text+'</span>')
	})
};

index.h2()