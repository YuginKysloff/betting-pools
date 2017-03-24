<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/head.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/system-block.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/header.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/menu.php'); ?>

<div class="content view__settings">

	<form method="post" id="settings__control_panel">
		<div class="controll-panel">
			<div class="controll-panel__head">Панель управления</div>

			<div class="controll-panel__item">
				<input type="checkbox" name="" class="controll-panel__checkbox-hide" id="controll-panel__checkbox-hide-enabled" data-action="enabled" <?=($data['config']['site']['enabled'] != 0) ? 'checked' : '';?>>
				<label for="controll-panel__checkbox-hide-enabled" class="controll-panel__checkbox">on <span class="controll-panel__checkbox-off">off</span></label>
				<span class="controll-panel__title">Доступнось сайта</span>
				<span class="controll-panel__description">0 - отключен, 1 - включен</span>
			</div>
			
			<div class="controll-panel__item">
				<input type="checkbox" name="" class="controll-panel__checkbox-hide" id="controll-panel__checkbox-hide-signup" data-action="signup" <?=($data['config']['site']['signup'] != 0) ? 'checked' : '';?>>
				<label for="controll-panel__checkbox-hide-signup" class="controll-panel__checkbox">on <span class="controll-panel__checkbox-off">off</span></label>
				<span class="controll-panel__title">Возможность регистрации</span>
				<span class="controll-panel__description">0 - отключена, 1 - включена</span>
			</div>

			<!--<div class="controll-panel__item">
				<div class="g-input controll-panel__input">
					<input type="text" name="" value="<?=$data['config']['site']['instant_interval'];?>">
				</div>
				<span class="controll-panel__title">Частота инстанта</span>
				<span class="controll-panel__description">в секундах</span>
			</div>-->
		</div>
	</form>

<? 	if($data['user']['access'] >= 5):?>
		<div class="settings__hr"></div>
		<span class="settings__button-category">Добавить категорию</span>
		<span class="settings__button-settings">Добавить настройку</span>
		<div class="settings__hr"></div>

		<div class="top-bar">
			<div class="top-bar__category">
			<form method="post" id="settings__new_category">
				<select name="parent_id">
					<option value="">Добавить родительскую категорию</option>
					<? foreach($data['list'] as $val):
						if(!isset($val['parent_id'])) continue;
						if(!isset($level) || $val['level'] !== $level) {
							$pref = '';
							for($i=$val['level']; $i>0; $i--){
								$pref .= '--';
							}
							$level = $val['level'];
						} ?>
						<option value="<?=$val['id'];?>"><?=$pref.$val['comment'];?></option>
					<?	endforeach;?>
				</select>
				<div class="g-input security__search"><input type="text" name="name" placeholder="Ключ"></div>
				<div class="g-input security__search"><input type="text" name="comment" placeholder="Описание"></div>
				<div class="g-input security__search"><input type="text" name="sort" placeholder="Сортировка"></div>
				<span class="g-button blue" id="settings__add_category">Добавить</span>
				<div class="settings__hr"></div>
			</form>
			</div>
			<div class="top-bar__settings">
			<form method="post" id="settings__new_setting">
				<select name="category">
					<? foreach($data['list'] as $val):
						if(!isset($val['parent_id'])) continue;
						if(!isset($level) || $val['level'] !== $level) {
							$pref = '';
							for($i=$val['level']; $i>0; $i--){
								$pref .= '--';
							}
							$level = $val['level'];
						} ?>
						<option value="<?=$val['id'];?>"><?=$pref.$val['comment'];?></option>
					<?	endforeach;?>
				</select>
				<div class="g-input security__search"><input type="text" name="name" placeholder="Ключ"></div>
				<div class="g-input security__search"><input type="text" name="value" placeholder="Значение"></div>
				<div class="g-input security__search"><input type="text" name="comment" placeholder="Описание"></div>
				<div class="g-input security__search"><input type="text" name="sort" placeholder="Сортировка"></div>
				<span class="g-button blue" id="settings__add_setting">Добавить</span>
				<div class="settings__hr"></div>
			</form>
			</div>
		</div>
		<div class="settings-table__wrap" id="settings__list"></div>
<? 	endif;?>
</div>

<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/footer.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/script.php'); ?>