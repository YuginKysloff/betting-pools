var st_process = new Object();
function process(url, ident, before_id, form_id) {
	if(st_process[ident] == false) return;
	if(form_id == '') form_id = 'undefined';
	jQuery.ajax({
		url:     url,
		type:     "POST",
		dataType: "json",
		data: jQuery("#"+form_id).serialize(),

		beforeSend: function () {
			st_process[ident] = false;
			if(before_id) {
				$(before_id).addClass('js-load');
			}
		},
		success: function(response) {
			$.each(response, function(i, val) {
				$('#'+i).html(val);
			});
		},
		error: function(response) {
			messager('error', 'Unknown error');
		},
		complete: function () {
			st_process[ident] = true;
			if(before_id) {
				$(before_id).removeClass('js-load');
			}
		}
	});
}

// layouts/header
$('#header__logout').on('click', function() {
	process('/users/logout', 'logout', '#header__logout');
});

// view_black
if($('#black__list').length != 0) {
	process('/'+admin_path+'/black/get_list', 'black', '#black__list', 'black__form');
}
$('#black__add_button').click(function() {
	process('/'+admin_path+'/black/action/method/add', 'add', '#black__add_button', 'black__form');
});
$('#black__delete_button').click(function() {
	process('/'+admin_path+'/black/action/method/delete', 'delete', '#black__delete_button', 'black__form');
});

// view_log
if($('#log__list').length != 0) {
    process('/'+admin_path+'/log/get_list', 'list', '#log__list', 'log__form');
}
$('#log__button').on('click', function() {
    process('/'+admin_path+'/log/get_list', 'list', '#log__button, #log__list', 'log__form');
});
$('#log__list').on('click', '.log-login', function() {
    $('input[name=login]').val($(this).text());
    process('/'+admin_path+'/log/get_list', 'list', '#log__button, #log__list', 'log__form');
});

// view_login
$('#login__button').on('click', function() {
    process('/'+admin_path+'/users/login', 'login', '#login__button', 'login__form');
});

// view_multi
if($('#multi__list').length != 0) {
	process('/'+admin_path+'/multi/get_list/archive/0', 'list', '#multi__list');
}
$('#multi__unprocess_button').on('click', function() {
	process('/'+admin_path+'/multi/get_list/archive/0', 'unprocess', '#multi__unprocess_button, #multi__list');
});
$('#multi__process_button').on('click', function() {
	process('/'+admin_path+'/multi/get_list/archive/1', 'process', '#multi__process_button, #multi__list');
});

// view_multi_list
$('#multi__list').on('click', '.users_list__instant', function() {
	process('/'+admin_path+'/users/change/action/instant/result/multi__list .users_list__instant_'+$(this).data('id')+'/user_id/'+$(this).data('id'), 'instant'+$(this).data('id'), '#users_list__instant_'+$(this).data('id'));
});
$('#multi__list').on('click', '.users_list__access', function() {
	process('/'+admin_path+'/users/change/action/access/result/multi__list .users_list__access_'+$(this).data('id')+'/user_id/'+$(this).data('id'), '#users_list__access_'+$(this).data('id'));
});
$('#multi__list').on('click', '.users_list__archive', function() {
	process('/'+admin_path+'/multi/action/type/'+$(this).data('type')+'/action/archive/result/multi__list .users_list__archive_'+$(this).data('id')+'/user_id/'+$(this).data('id'), 'archive'+$(this).data('id'), '#users_list__archive_'+$(this).data('id'));
});
$('#multi__list').on('click', '.users_list__delete', function() {
	process('/'+admin_path+'/multi/action/type/'+$(this).data('type')+'/action/delete/result/multi__list .users_list__delete_'+$(this).data('id')+'/user_id/'+$(this).data('id'), 'delete'+$(this).data('id'), '#users_list__delete_'+$(this).data('id'));
});
$('#multi__list').on('click', '.users_list__avatar_delete', function() {
	process('/'+admin_path+'/users/change/action/avatar/result/users_list__avatar_'+$(this).data('id')+'/user_id/'+$(this).data('id'), 'avatar'+$(this).data('id'), '#users_list__avatar_'+$(this).data('id'));
});

// views_news
if($('#news__list').length != 0){
	process('/'+admin_path+'/news/get_list', 'list', '#news__list');
}
function wysiwyg_instance() {
	$.each($('.news__wysiwyg'), function(i, val) {
		CKEDITOR.replace('ck_text_'+$(this).data('lang'));
	});
}
$('#news__add_button').on('click', function() {
	process('/'+admin_path+'/news/add', 'add', '#news__wysiwyg');
});
$('#news__wysiwyg').on('click', '#news__save_button', function() {
	for(instance in CKEDITOR.instances) 
		CKEDITOR.instances[instance].updateElement();
	process('/'+admin_path+'/news/save', 'save', '#news__save_button', 'news__form');
});
$('#news__list').on('click', '.news__edit', function() {
	process('/'+admin_path+'/news/edit/datetime/'+$(this).parent().data('datetime'), 'add', '#news__wysiwyg');
});
$('#news__list').on('click', '.news__delete', function() {
	process('/'+admin_path+'/news/delete/datetime/'+$(this).parent().data('datetime'), 'delete', '.news__delete');
});

// view_payout
$('#payout__list').on('click', '.pay__success', function(){
	process('/'+admin_path+'/payout/confirm/ident/'+$(this).parent().data('id'), 'success', '#payout__str'+$(this).data('id'));
});
$('#payout__list').on('click', '.pay__fail', function(){
	process('/'+admin_path+'/payout/denial/ident/'+$(this).parent().data('id'), 'denial', '#payout__str'+$(this).parent().data('id'));
});

// view_security
if($('#security__list').length != 0) {
	process('/'+admin_path+'/security/get_list', 'list', '#security__list', 'security__form');
}
$('#security__button').on('click', function() {
	process('/'+admin_path+'/security/get_list', 'list', '#security__button, #security__list', 'security__form');
});

// view_settings
if($('#settings__list').length != 0) {
	process('/'+admin_path+'/settings/get_list', 'list', '#settings__list');
}
$('#settings__control_panel').on('change', '.controll-panel__checkbox-hide', function() {
	process('/'+admin_path+'/settings/panel/action/'+$(this).data('action'), 'panel', '', 'settings__control_panel');
});

// view_settings_list
$('#settings__add_category').on('click', function() {
	process('/'+admin_path+'/settings/add_setting_category', 'add', '#settings__add_category', 'settings__new_category');
});
$('#settings__add_setting').on('click', function() {
	process('/'+admin_path+'/settings/add_setting', 'add', '#settings__add_setting', 'settings__new_setting');
});
$('#settings_list__save').on('click', function() {
	process('/'+admin_path+'/settings/save', 'save', '#settings_list__save', 'settings_list__list');
});
$('.settings_list__delete_category').on('click', function() {
	process('/'+admin_path+'/settings/delete_settings_category/id/'+$(this).data('id'), 'delete'+$(this).data('id'), '.settings_list__delete_category');
});
$('.settings_list__delete').on('click', function() {
	process('/'+admin_path+'/settings/delete_settings/id/'+$(this).data('id'), 'delete'+$(this).data('id'), '.settings_list__delete');
});

// view_statistics
if($('#statistics__finance').length != 0) {
	process('/'+admin_path+'/statistics/finance', 'finance', '#statistics__finance');
}
if($('#statistics__wallets').length != 0) {
	process('/'+admin_path+'/statistics/wallets', 'wallets', '#statistics__wallets');
}
if($('#statistics__signup').length != 0) {
	process('/'+admin_path+'/statistics/signup', 'signup', '#statistics__signup');
}
$('#statistics__finance').on('click', '#statistics__show_all_finance', function() {
	process('/'+admin_path+'/statistics/finance_all', 'finance_all', '#statistics__all_finance');
});
$('#statistics__signup').on('click', '#statistics__show_all_signup', function() {
	process('/'+admin_path+'/statistics/signup_all', 'signup_all', '#statistics__show_all_signup');
});
$('#statistics__wallets').on('click', '.statistics_wallets__payment', function() {
	process('/'+admin_path+'/statistics/wallets_enabled/payment/'+$(this).data('payment'), 'wallets');
});
if($('#statistics__last').length != 0) {
	process('/'+admin_path+'/statistics/last/dir/fill', 'last', '#statistics__last');
}
$('#statistics__last_fill').on('click', function() {
	process('/'+admin_path+'/statistics/last/dir/fill', 'last', '#statistics__last');
});
$('#statistics__last_payout').on('click', function() {
	process('/'+admin_path+'/statistics/last/dir/payout', 'last', '#statistics__last');
});
$('#statistics__last_signup').on('click', function() {
	process('/'+admin_path+'/statistics/last/dir/signup', 'last', '#statistics__last');
});
$('#statistics__last_url').on('click', function() {
	process('/'+admin_path+'/statistics/last/dir/url', 'last', '#statistics__last');
});

// view_users
if($('#users__list').length != 0) {
	process('/'+admin_path+'/users/get_list', 'search', '#users__list', 'users__form');
}
$('#users__search_button').on('click', function() {
	process('/'+admin_path+'/users/get_list', 'search', '#users__list', 'users__form');
});
$('#users__download_emails_button').on('click', function() {
	process('/'+admin_path+'/users/download_base', 'base', '#users__download_emails_button');
});

// view_user_list
$('#users__list').on('click', '.users_list__avatar', function() {
	$('.cont[data-id="'+$(this).data('id')+'"]').slideToggle();
});
$('#users__list').on('click', '.users_list__instant', function() {
	process('/'+admin_path+'/users/change/action/instant/result/users_list__instant_'+$(this).data('id')+'/user_id/'+$(this).data('id'), 'instant'+$(this).data('id'), '#users_list__instant_'+$(this).data('id'));
});
$('#users__list').on('click', '.users_list__access', function() {
	process('/'+admin_path+'/users/change/action/access/result/users_list__access_'+$(this).data('id')+'/user_id/'+$(this).data('id'), '#users_list__access_'+$(this).data('id'));
});
$('#users__list').on('click', '.users_list__password', function() {
	process('/'+admin_path+'/users/change/action/password/result/users_list__password_'+$(this).data('id')+'/user_id/'+$(this).data('id'), 'password'+$(this).data('id'), '#users_list__password_'+$(this).data('id'));
});
$('#users__list').on('click', '.users_list__save', function() {
	process('/'+admin_path+'/users/save/user_id/'+$(this).data('id'), 'save', '.users_list__save', 'users_list__form_'+$(this).data('id'));
});
$('#users__list').on('click', '.users_list__avatar_delete', function() {
	process('/'+admin_path+'/users/change/action/avatar/result/users_list__avatar_'+$(this).data('id')+'/user_id/'+$(this).data('id'), 'avatar'+$(this).data('id'), '#users_list__avatar_'+$(this).data('id'));
});

// view_white
if($('#white__list').length != 0) {
	process('/'+admin_path+'/white/get_list', 'list', '#white__list');
}
$('#white__add_to_list').on('click', function() {
	process('/'+admin_path+'/white/add', 'add', '#white__add_to_list', 'white__form');
});
$('#white__fill').on('click', function() {
	process('/'+admin_path+'/white/inout/action/fill', 'inout', '#white__fill', 'white__form');
});
$('#white__payout').on('click', function() {
	process('/'+admin_path+'/white/inout/action/payout', 'inout', '#white__payout', 'white__form');
});
$('#white__list').on('click', '.white_list__avatar_delete', function() {
	process('/'+admin_path+'/users/change/action/avatar/result/white_list__avatar_'+$(this).data('id')+'/user_id/'+$(this).data('id'), 'avatar'+$(this).data('id'), '#users_list__avatar_'+$(this).data('id'));
});
$('#white__list').on('click', '.insert_login', function() {
	$('input[name=login]').val($(this).text());
	$("body,html").animate({scrollTop: 0}, 800);
});
$('#white__list').on('click', '.delete', function() {
	process('/'+admin_path+'/white/delete/id/'+$(this).data('id'), 'delete'+$(this).data('id'), '#white_list__str_'+$(this).data('id')+' .delete');
});
if($('#white__autosignup_list').length != 0) {
	process('/'+admin_path+'/white/autosignup/action/list', 'autosignup_list', '#white__autosignup_list');
}
if($('#white__autosignup_timer').length != 0) {
	process('/'+admin_path+'/white/autosignup/action/check_enabled', 'enabled');
}
$('#white__autosignup_enabled').on('change', function() {
	process('/'+admin_path+'/white/autosignup/action/enabled/', 'enabled', '', 'white__autosignup');
});
var autosignup_action_id = Array();
$('.white__autosignup_action').on('keyup', function() {
	if($(this).data('type') == 'number') {
		if($(this).val().match(/[^0-9\.\,]/g)) {
			$(this).val($(this).val().replace(/[^0-9\.\,]/g, ''));
			if($(this).val() == $(this).data('value')) return;
		}
	}
	var action = $(this).data('action');
	if(typeof autosignup_action_id[action] !=="undefined") clearTimeout(autosignup_action_id[action]);
	autosignup_action_id[action] = setTimeout(function() {
		process('/'+admin_path+'/white/autosignup/action/'+action, 'enabled', '', 'white__autosignup');
	}, 1000);
});
$('.white__autosignup_sponsor').on('keyup', function() {
	if(typeof autosignup_action_sponsor_id !=="undefined") clearTimeout(autosignup_action_sponsor_id);
	autosignup_action_sponsor_id = setTimeout(function() {
		process('/'+admin_path+'/white/autosignup/action/sponsor', 'sponsor', '', 'white__autosignup');
	}, 1000);
});
$('#white__add_new_sponsor').on('click', function() {
	process('/'+admin_path+'/white/autosignup/action/add_sponsor', 'add_sponsor', '#white__add_new_sponsor');
});
$('#white__delete_sponsor').on('click', function() {
	process('/'+admin_path+'/white/autosignup/action/delete_sponsor', 'delete_sponsor', '#white__delete_sponsor');
});
$('#white__autosignup_list').on('click', '.white_autosignup_list__val', function() {
	process('/'+admin_path+'/white/autosignup/action/delete_list_line/line/'+$(this).data('line')+'/p/'+$('#white__autosignup_list .pagination__button--active').html(), 'delete_list_line', '#white__autosignup_list');
});
$('#white__autosignup_download').on('click', function() {
	$('#white__autosignup_file').click();
});
var white__autosignup_file_id = true;
$('#white__autosignup_file').on('change', function() {
	if(white__autosignup_file_id != true) return;
	var $that = $('#white__autosignup'),
	formData = new FormData($that.get(0));
	$.ajax({
		url: '/'+admin_path+'/white/autosignup/action/add_file',
		type: 'POST',
		contentType: false,
		processData: false,
		data: formData,
		dataType: 'json',
		beforeSend: function() {
			white__autosignup_file_id = false;
			$('#white__autosignup_download').addClass('js-process');
		},
		success: function(response) {
			$.each(response, function(i, val) {
				$('#'+i).html(val);
			});
		},
		error: function(response) {
			messager('error', 'Unknown error');
		},
		complete: function() {
			white__autosignup_file_id = true;
			$('#white__autosignup_download').removeClass('js-process');
		}
	});
});
$('#white__autosignup_delete_file').on('click', function() {
	process('/'+admin_path+'/white/autosignup/action/delete_file', 'delete_file', '#white__autosignup_delete_file');
});
if($('#white__autofill_list').length != 0) {
	process('/'+admin_path+'/white/autofill/action/list', 'autofill_list', '#white__autofill_list');
}
$('#white__autofill_enabled').on('change', function() {
	process('/'+admin_path+'/white/autofill/action/enabled/', 'enabled', '', 'white__autofill_form');
});
if($('#white__autofill_timer').length != 0) {
	setTimeout(function(){process('/'+admin_path+'/white/autofill/action/check_enabled', 'enabled');}, 100);
}
$('#white__autofill_save_settings').on('click', function() {
	process('/'+admin_path+'/white/autofill/action/save', 'save_settings', '#white__autofill_save_settings', 'white__autofill_form');
});
$('#white__autofill_delete_file').on('click', function() {
	process('/'+admin_path+'/white/autofill/action/delete_file', 'delete_file', '#white__autofill_delete_file');
});
if($('#white__autopayout_list').length != 0) {
	process('/'+admin_path+'/white/autopayout/action/list', 'autopayout_list', '#white__autopayout_list');
}
$('#white__autofill_enabled').on('change', function() {
	process('/'+admin_path+'/white/autopayout/action/enabled/', 'enabled', '', 'white__autopayout_form');
});
if($('#white__autofill_timer').length != 0) {
	setTimeout(function(){process('/'+admin_path+'/white/autopayout/action/check_enabled', 'enabled');}, 100);
}
$('#white__autopayout_save_settings').on('click', function() {
	process('/'+admin_path+'/white/autopayout/action/save', 'save_settings', '#white__autopayout_save_settings', 'white__autopayout_form');
});
$('#white__autopayout_delete_file').on('click', function() {
	process('/'+admin_path+'/white/autopayout/action/delete_file', 'delete_file', '#white__autopayout_delete_file');
});
$('.white__autofill_action, .white__autopayout_action').on('keyup', function() {
	if($(this).data('type') == 'number') {
		if($(this).val().match(/[^0-9]/g)) {
			$(this).val($(this).val().replace(/[^0-9]/g, ''));
		}
	}
});