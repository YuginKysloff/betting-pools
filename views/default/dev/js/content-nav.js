

global.contentNav = function() {
	$('.content-nav__item').mouseover(function() {
		$(this).find('.content-nav__circle').addClass('js-open');
		$(this).find('.content-nav__circle').removeClass('js-close');
		$(this).addClass('js-open');
		$(this).removeClass('js-close')
	})

	$('.content-nav__item').mouseout(function() {
		$(this).find('.content-nav__circle').addClass('js-close');
		$(this).find('.content-nav__circle').removeClass('js-open');
		$(this).addClass('js-close');
		$(this).removeClass('js-open')
	})
}

global.contentNav()