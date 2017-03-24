<? defined('SW_CONSTANT') or die; ?>
<? if($data['config']['automatization'][$data['result']['timer_name']]['last'] > time()):?>
	<script>
		var hour<?=$data['result']['timer_name'];?> = <?=(int)(($data['config']['automatization'][$data['result']['timer_name']]['last'] - time()) / 3600);?>;
		var minute<?=$data['result']['timer_name'];?> = <?=(int)(($data['config']['automatization'][$data['result']['timer_name']]['last'] - time()) / 60 % 60);?>;
		var second<?=$data['result']['timer_name'];?> = <?=(int)(($data['config']['automatization'][$data['result']['timer_name']]['last'] - time()) % 60);?>;

		function timer_<?=$data['result']['timer_name'];?>_id() {
			$("#white__<?=$data['result']['timer_name'];?>_timer").html((hour<?=$data['result']['timer_name'];?> < 10 ? "0" + hour<?=$data['result']['timer_name'];?> : hour<?=$data['result']['timer_name'];?>) + ":" + (minute<?=$data['result']['timer_name'];?> < 10 ? "0" + minute<?=$data['result']['timer_name'];?> : minute<?=$data['result']['timer_name'];?>) + ":" + (second<?=$data['result']['timer_name'];?> < 10 ? "0" + second<?=$data['result']['timer_name'];?> : second<?=$data['result']['timer_name'];?>));
			--second<?=$data['result']['timer_name'];?>;
			if (second<?=$data['result']['timer_name'];?> < 0) {
				--minute<?=$data['result']['timer_name'];?>;
				if (minute<?=$data['result']['timer_name'];?> < 0) {
					--hour<?=$data['result']['timer_name'];?>;
					if (hour<?=$data['result']['timer_name'];?> < 0) {
						process('/<?=$data['config']['site']['admin'];?>/white/<?=$data['result']['timer_name'];?>/action/check_enabled', 'enabled', '', 'settings__<?=$data['result']['timer_name'];?>');
						process('/<?=$data['config']['site']['admin'];?>/white/<?=$data['result']['timer_name'];?>/action/list', '<?=$data['result']['timer_name'];?>_list', '#white__<?=$data['result']['timer_name'];?>_list');
					}
					minute<?=$data['result']['timer_name'];?> = 59;
				}
				second<?=$data['result']['timer_name'];?> = 59;
			}
		}
		if(typeof <?=$data['result']['timer_name'];?>_timer_id !=="undefined") clearInterval(<?=$data['result']['timer_name'];?>_timer_id);
		<?=$data['result']['timer_name'];?>_timer_id = setInterval(function(){timer_<?=$data['result']['timer_name'];?>_id()}, 1000);
		timer_<?=$data['result']['timer_name'];?>_id();
	</script>
<? else:?>
	00:00:00
	<script>
	if(typeof <?=$data['result']['timer_name'];?>_timer_id !=="undefined") clearInterval(<?=$data['result']['timer_name'];?>_timer_id);
	if(typeof refresh_timer_<?=$data['result']['timer_name'];?>_id !=="undefined") clearTimeout(refresh_timer_<?=$data['result']['timer_name'];?>_id);
	refresh_timer_<?=$data['result']['timer_name'];?>_id = setTimeout(function() {
		process('/<?=$data['config']['site']['admin'];?>/white/<?=$data['result']['timer_name'];?>/action/check_enabled', 'enabled', '', 'settings__<?=$data['result']['timer_name'];?>');
		process('/<?=$data['config']['site']['admin'];?>/white/<?=$data['result']['timer_name'];?>/action/list', '<?=$data['result']['timer_name'];?>_list', '#white__<?=$data['result']['timer_name'];?>_list');
	}, 5000);
	</script>
<? endif;?>