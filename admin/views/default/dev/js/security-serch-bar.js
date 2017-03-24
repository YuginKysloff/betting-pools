$('.security-table__wrap').on('click', '.security-table__login', function() {
	var login = $(this).html();
	//Меняем Select
	$('.security__search-bar select option').removeAttr('selected');
	$('.security__search-bar select option[value="login"]').attr('selected','selected');
	//Меняем отрисовку FancySelect
	$('select').trigger('update');
	//Добавляем значение в форму
	$('.security__search input').val(login);
	$('body').animate({scrollTop:0}, '500');

})

$('.security-table__wrap').on('click', '.security-table__ip', function() {
	var ip = $(this).html();
	//Меняем Select
	$('.security__search-bar select option').removeAttr('selected');
	$('.security__search-bar select option[value="ip"]').attr('selected','selected');
	//Меняем отрисовку FancySelect
	$('select').trigger('update');
	//Добавляем значение в форму
	$('.security__search input').val(ip);
	$('body').animate({scrollTop:0}, '500');
})