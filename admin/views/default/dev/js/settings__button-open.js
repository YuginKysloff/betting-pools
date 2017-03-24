var topBarSettings = 'close';
var topBarCategory = 'close';

$('.settings__button-settings').click(function() {
	if(topBarSettings == 'open'){
		$('.top-bar__settings').slideUp();
		topBarSettings = 'close';
	}else{
		$('.top-bar__settings').slideDown();
		topBarSettings = 'open';
		$('.top-bar__category').slideUp();
		topBarCategory = 'close';
	}
});

$('.settings__button-category').click(function() {
	if(topBarCategory == 'open'){
		$('.top-bar__category').slideUp();
		topBarCategory = 'close';
	}else{
		$('.top-bar__category').slideDown();
		topBarCategory = 'open';
		$('.top-bar__settings').slideUp();
		topBarSettings = 'close';
	}
})