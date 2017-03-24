$('tr').each(function() {
	var thisBlock = $(this);
	$(this).find('.fa-angle-double-down').click(function() {
		thisBlock.next().find('.cont').slideToggle(400);
	})
})