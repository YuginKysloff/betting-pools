

index.userMenu = function() {
	$('.user-menu__icon').click(function() {
		$('.user-menu__sub-menu').toggleClass('js-active');
	});
};

index.userMenu();