<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/load.php'); ?>
<? 	if($data['result']['list']):?>						
	<div class="registration__list">
		<span class="registration__hr-line"></span>
		<?	foreach($data['result']['list'] as $key => $val):?>
				<div class="registration__item">
					<span class="registration__value"><?=Lib_Main::beauty_number($val);?></span>
					<span class="registration__data"><?=date($data['config']['formats']['admin_date'], strtotime($key));?></span>
				</div>
		<?	endforeach;?>
		<div id="statistics__all_signup"></div>
	</div>
	<? if($data['result']['more']):?>
		<span class="g-button blue" id="statistics__show_all_signup">Все Регистрации</span>
	<?endif;?>
<? 	else:?>
		<div class="registration__item-line">
			<span class="g-grey">Нет результатов</span>
		</div>
<?	endif;?>