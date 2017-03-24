
$('.system-massage').on('click', '.system-massage__item',  function() {
	$(this).slideUp(400)
	thisBlock = $(this);

	function remove(thisBlock) {
		thisBlock.remove()
	}
	setTimeout(function() {
		thisBlock.remove();
	},500);
})

var messageId = 1;

function messager (type, message, timeout) {
	messageId += 1;
	(timeout == 0)? timeout = 99999:'';
	(timeout == undefined)? timeout = 5:'';

	if ($('.system-massage__item').length != 0){
		$('.system-massage__item').last().after('<span class="system-massage__item messageId'+messageId+'" style="animation-delay: 0s,'+timeout+'s" data-type="'+type+'">'+message+'</span>');
	}else{
		$('.system-massage').html('<span class="system-massage__item messageId'+messageId+'" style="animation-delay: 0s,'+timeout+'s" data-type="'+type+'">'+message+'</span>');
	}

	$('.system-massage__item').animate({
		'padding': '15px 35px 15px 16px',
		'border-width': '1px'
	},500);
	var removeElement = $('.messageId'+messageId);
	
	setTimeout(function() {
		removeElement.remove();
	},(timeout+1)*1000);
}
