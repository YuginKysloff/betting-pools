var validation = false;

function validationCheck() {
	$('[data-validation]').each(function() {
		var thisValid = $(this).attr('data-validation');
		if (thisValid == 'true') {
			validation = true;
		}else{
			validation = false;
			return false;
		}
	})
}


//Валидация Email
function emailValidation(thisBlock, value) {
	var email = /^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/.test(value)
	if (email == true) {
		thisBlock.attr('data-validation','true')
	}else{
		thisBlock.attr('data-validation','false')
		thisBlock.attr('data-validation-error','Не правильно указан Email')
	}
}

$('[data-validation-type="email"] input').focus(function() {
	$(this).parent().removeAttr('data-validation-error');
	validationCheck()
})

$('[data-validation-type="email"]').focusout(function() {
	var thisBlock = $(this);
	var value = $(this).find('input').val();
	emailValidation(thisBlock, value)
	validationCheck()
})



//Валидация Пароля
function passValidation(thisBlock, value) {
	var password = /^[a-zA-Z0-9]+$/.test(value)
	if (password == true) {
		thisBlock.attr('data-validation','true')
	}else{
		thisBlock.attr('data-validation','false')
		thisBlock.attr('data-validation-error','Не допустимы символы в пароле')
	}
}

$('[data-validation-type="password"] input').focus(function() {
	$(this).parent().removeAttr('data-validation-error');
	validationCheck()
})

$('[data-validation-type="password"]').focusout(function() {
	var thisBlock = $(this);
	var value = $(this).find('input').val();
	passValidation(thisBlock, value)
	validationCheck()
})


//Валидация логина
// function loginValidation(thisBlock, value) {
// 	var login = /^[a-zA-Z0-9]+$/.test(value)
// 	if (login == true) {
// 		thisBlock.attr('data-validation','true')
// 	}else{
// 		thisBlock.attr('data-validation','false')
// 		thisBlock.attr('data-validation-error','Не допустимы символы в пароле')
// 	}
// }

// $('[data-validation-type="login"] input').focus(function() {
// 	$(this).parent().removeAttr('data-validation-error');
// 	validationCheck()
// })

// $('[data-validation-type="login"]').focusout(function() {
// 	var thisBlock = $(this);
// 	var value = $(this).find('input').val();
// 	loginValidation(thisBlock, value)
// 	validationCheck()
// })