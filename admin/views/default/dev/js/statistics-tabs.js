$('.tabs__menu-item').click(function () {
	var thisId = $(this).data('tabs-id');
	$('.tabs-table__wrap').css('display','none');
	$('.tabs__menu-item').removeClass('js-active').removeClass('active');
	$(this).addClass('js-active');
	$('.tabs-table__wrap[data-tabs-table-id="'+thisId+'"]').css('display','block');
})