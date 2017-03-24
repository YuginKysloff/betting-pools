function indexPage() {
	var desktop = '/admin/';
	if (window.location.pathname == desktop) {
		$('body').css('minHeight','100vh');
	}

	if ($('div').hasClass('auth')) {
		$('body').css('minHeight','100vh');
	}
}

indexPage();