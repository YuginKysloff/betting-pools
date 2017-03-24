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
                $(before_id).addClass('js-process');
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
                $(before_id).removeClass('js-process');
            }
        }
    });
}

// layouts/content-menu
$('#logout__button').on('click', function() {
    process('/users/logout', 'logout');
});

// view_account
$('#account__button').on('click', function() {
    process('/account/save', 'save', '#account__button', 'account__form');
});

// view_check
$('#check__form_button').on('click', function() {
    process('/users/confirm_ip/result_id/check__form_error', 'check', '#check__form_button', 'check__form');
});

// view_contacts
$('#contacts__send_button').on('click', function() {
    process('/contacts/send', 'send', '#contacts__send_button', 'contacts__form');
});

// view_deposits
if($('#deposits__list').length != 0) {
    process('/deposits/get_list', 'list', '#deposits__list');
}
$('#deposits__list').on('click', '.g-button--deposit', function() {
    process('/deposits/payout/ident/'+$(this).data('id')+'/p/'+$(".pagination .pagination__button--active").html(), 'payout', '#deposits__list', 'deposits__form'+$(this).data('id'));
});

// view_fill
if($('#fill__list').length != 0) {
    process('/fill/get_list', 'list', '#fill__list');
}
$('#fill__form_button').on('click', function() {
    process('/fill/fill_in/result_id/fill__form_error', 'fill', '#fill__form_button', 'fill__form');
});

// view_index
if($('#index__last_pool').length != 0) {
    process('/index/get_last', 'last', '#index__last_pool');
}
$('#index__last_pool').on('click', '.pools_list__button', function() {
    process('/index/buy/id/'+$(this).data("id"), 'list', '.pools_list__button', 'pool__form'+$(this).data("id"));
});

// view_log
if($('#log__list').length != 0) {
    process('/log/get_list', 'list', '#log__list');
};

// view_login
$('#login__button').on('click', function() {
    process('/users/login/result_id/auth__error', 'login', '#login__button', 'login__form');
});

// view_lost
$('#lost__button').on('click', function() {
    process('/users/lost_pass/result_id/lost__error', 'lost', '#lost__button', 'lost__form');
});

// view_news
if($('#news__list').length != 0) {
	if($('#news__list').html() == '') {
		process('/news/get_list', 'list', '#news__list');
	} else {
		var news_id = $('#news__list').html();
		$('#news__list').html('');
		process('/news/view/id/'+news_id, 'view', '#news__list');
	}
};
$('#news__list').on('click', '.news_list__item', function() {
    process('/news/view/id/'+$(this).data("id"), 'view', '#news__list');
	history.pushState(null, null, '/news/'+$(this).data("id"));
});
$('#news__list').on('click', '#news_view__all', function() {
    process('/news/get_list', 'list', '#news__list');
	history.pushState(null, null, '/news');
});

// view_payout
if($('#payout__list').length != 0) {
    process('/payout/get_list', 'list', '#payout__list');
}
$('#payout__form_button').on('click', function() {
    process('/payout/payout_in/result_id/payout__form_error', 'payout', '#payout__form_button', 'payout__form');
});

// view_pools
if($('#pools__list').length != 0) {
    process('/pools/get_list', 'list', '#pools__list');
}
$('#pools__list').on('click', '.pools_list__button', function() {
    process('/pools/buy/id/'+$(this).data("id")+'/p/'+$(".pagination .pagination__button--active").html(), 'buy', '#pool__form'+$(this).data("id")+' .pools_list__button', 'pool__form'+$(this).data("id"));
});
$('#pools__list, #index__last_pool').on('click', '.pools__deposit_info', function() {
	var pool = $('#pools_list__info'+$(this).data('id'));
	if(pool.hasClass('pull__deposit_info--open')) {
		pool.removeClass('pull__deposit_info--open');
	} else {
		process('/pools/get_deposits/id/'+$(this).data('id'), 'get_deposits', pool);
		pool.addClass('pull__deposit_info--open');
	}
});

// view_refsys
if($('#refsys__list').length != 0) {
    process('/refsys/get_list', 'list', '#refsys__list');
}
$('#refsys__reset_button').on('click', function() {
    process('/refsys/reset', 'reset', '#refsys__reset_button');
});

// view_signup
$('#signup__button').on('click', function() {
    process('/users/signup/result_id/signup__error', 'signup', '#signup__button', 'signup__form');
});