$('[data-q]').each(function() {
	
	var type = $(this).attr('type');
	var thisClass = $(this).attr('class');
	var regular = /.+?\s/g;

	//Проверяем на наличие болие одного класса, если 1 класс - оставляем если больше забираем только первый.
	var doubleCalss = /\s/g;
		doubleCalss = doubleCalss.test(thisClass);
		(doubleCalss) ? thisClass = $.trim( regular.exec($(this).attr('class')) ) : '';
	
	//Генератор рандомной строки
	function randomStr() {
	    var result       = '';
	    var words        = 'qwertyuiopasdfghjklzxcvbnm';
	    var max_position = words.length - 1;
	        for( i = 0; i < 8; ++i ) {
	            position = Math.floor ( Math.random() * max_position );
	            result = result + words.substring(position, position + 1);
	        }
	    return result;
	}

	//Условия для input:text
	if (type == 'text' || type == 'password') {

		//Проверяем указан ли fontAwesome
		var fontAwesome = $(this).attr('data-fa');
			(fontAwesome == undefined || fontAwesome == '') 
				? fontAwesome = false 
				: fontAwesome = fontAwesome;

		var wrapper = '<div class="'+thisClass+'__wrap"></div>';
		var icon = '<i class="fa '+fontAwesome+'" aria-hidden="true"></i>';
		
		//Собераем обертку, с fontAwesome или без
		if (fontAwesome) {
			$(this).before(icon);
			$(this).prev().addClass(thisClass+'__icon')
			$(this).prev().addBack().wrapAll(wrapper)
		}else{
			$(this).wrapAll(wrapper)
		}
	}

	//Условия для radio & checkbox
	if (type == 'checkbox' || type == 'radio') {

		//Проверяем указан ли ID, если нет, то генерируем рандомный и записываем (Для корректного срабатывания тега label)
		var thisId = $(this).attr('id');
		if (thisId == undefined || thisId == ''){
				thisId = randomStr()
				$(this).attr('id', thisId);
		}else{
			thisId = thisId;
		}

		//Проверяем указан ли title
		var thisTitle = $(this).attr('data-title');
			(thisTitle == undefined || thisTitle == '') 
				? thisTitle = '' 
				: thisTitle = thisTitle;

		var wrapper = '<label for="'+thisId+'" class="'+thisClass+'__wrap"></label>';
		var title = '<span class="'+thisClass+'__title">'+thisTitle+'</span>';
		
		//Собераем обертку
		$(this).after(title);
		$(this).next().addBack().wrapAll(wrapper)
		
	}

})