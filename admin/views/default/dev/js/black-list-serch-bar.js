$('#black__list').on('click', '.blacklist-table__login', function() {
	var login = $(this).html();
	console.log(login)
	$('.blacklist__search-bar select option').removeAttr('selected');
	$('.blacklist__search-bar select option[value="login"]').attr('selected','selected');
	$('select').trigger('update');
	$('.blacklist__search input').val(login);
})

$('#black__list').on('click', '.blacklist-table__ip', function() {
	var ip = $(this).html();
	//Меняем Select
	console.log(ip)
	$('.blacklist__search-bar select option').removeAttr('selected');
	$('.blacklist__search-bar select option[value="ip"]').attr('selected','selected');
	$('select').trigger('update');
	//Добавляем значение в форму
	$('.blacklist__search input').val(ip);
})