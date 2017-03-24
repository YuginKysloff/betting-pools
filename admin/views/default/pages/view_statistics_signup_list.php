<? defined('SW_CONSTANT') or die; ?>

<? 	if($data['result']['list']):?>						
	<?	foreach($data['result']['list'] as $key => $val):?>
			<div class="registration__item">
				<span class="registration__value"><?=Lib_Main::beauty_number($val);?></span>
				<span class="registration__data"><?=date($data['config']['formats']['admin_date'], strtotime($key));?></span>
			</div>
	<?	endforeach;?>
<? 	else:?>
		<span class="g-grey">Нет результатов</span>
<?	endif;?>