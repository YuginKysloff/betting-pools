$('.show-password').click(function() {
	var inputType = $(this).siblings('input').attr('type');
	if (inputType == 'password'){
		$(this).removeClass('fa-eye-slash').addClass('fa-eye');
		$(this).siblings('input').attr('type','text');
	}else{
		$(this).removeClass('fa-eye').addClass('fa-eye-slash');
		$(this).siblings('input').attr('type','password');
	}
});