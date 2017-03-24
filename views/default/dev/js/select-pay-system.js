pay.paySystem = function () {
	$('.pay-system__item').click(function() {
		var paySystemName = $(this).attr('data-system');

		$('.pay-system__item').removeClass('js-active')
		$(this).addClass('js-active');

		$('.pay-system__hidden').val(paySystemName);

	})
}

pay.paySystem();