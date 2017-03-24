$('.post').each(function() {
	var newsRu = $(this).find('post[lang="ru"]').html();
    var newsEn = $(this).find('post[lang="en"]').html();
    var newsTitleRu = $(this).find('.post__title[lang="ru"]').html();
    var newsTitleEn = $(this).find('.post__title[lang="en"]').html();
    var newsDate = $(this).find('.post__data').data('time');
    var postId = $(this).data('id');
    $(this).find('.post__edits').click(function() {
    	CKEDITOR.instances['ck_text_ru'].setData(newsRu);
	    CKEDITOR.instances['ck_text_en'].setData(newsEn);
	    $('.news__title[lang="ru"] input').attr('value',newsTitleRu);
	    $('.news__title[lang="en"] input').attr('value',newsTitleEn);
	    $('[name="edit"]').attr('value',postId);
	    $('.news__title.date input').attr('value',newsDate);
	    console.log(newsDate)
	    $('body').animate({ scrollTop: 0 }, 400);
    })
})


//Открыть редакторы
var newsWysiwyg = 'close';
$('.news__save').click(function() {
	
	if (newsWysiwyg == 'open') {
		save_news();
		// messager('error','sdsds')
	}else{
		newsWysiwyg = 'open';
		$('#news__wysiwyg').slideDown(800);
		$(this).html('Сохранить')
	}
})

function datepicker() {
	$('.news__datepicker').datepicker({
	  altFormat: "yy.mm.dd"
	});
}