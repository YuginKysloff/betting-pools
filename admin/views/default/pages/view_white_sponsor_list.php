<? defined('SW_CONSTANT') or die; ?>
<?  if(isset($data['result']['list']) && $data['result']['list']):?>
	<?  foreach($data['result']['list'] as $val):?>
		<div class="controll-panel__item">
			<div class="g-input controll-panel__input">
				<input type="text" name="sponsor[]" value="<?=$val;?>" placeholder="Логин" class="white__autosignup_sponsor">
			</div>
			<span class="controll-panel__title">
				Спонсор
			</span>
			<span class="controll-panel__description">
				логин спонсора
			</span>
		</div>
	<?  endforeach;?>
<?  endif;?>