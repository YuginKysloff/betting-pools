<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/head.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/system-block.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/header.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/menu.php'); ?>
 
    <div class="content view__blacklist">
        <form method="post" id="white__form" onsubmit="return false;">
            <div class="whille__search-bar">
                <div class="g-input security__search"><input type="text" name="login" placeholder="Логин"></div>
                <span class="g-button blue whillelis"  id="white__add_to_list">В список</span>
                <select name="payment" data-class="whillelist__select">
                    <? foreach ($data['config']['payments'] as $key => $val):
                        if($val['enabled'] != '1') continue;?>
                        <option value="<?=$key;?>"><?=$val['name'];?></option>
                    <? endforeach;?>
                </select>
                <select name="valute" data-class="whillelist__select">
                    <? foreach($data['config']['valutes'] as $key => $val):?>
                        <option value="<?=$key;?>"><?=strtoupper($key);?></option>
                    <? endforeach;?>
                </select>
                <div class="g-input security__search"><input type="text" name="amount" placeholder="Сумма"></div>
                <span class="g-button blue whillelis" id="white__fill">Пополнение</span>
                <span class="g-button blue whillelis" id="white__payout">Выплата</span>
            </div>
        </form>
        <span id="error__block"></span>
        <div class="log-table__wrap" id="white__list"></div>
		
		<form method="post" id="white__autosignup" enctype="multipart/form-data" onsubmit="return false;">
			<div class="controll-panel">
				<div class="controll-panel__head">Автоматические регистрации</div>

				<div class="controll-panel__item">
					<input type="checkbox" name="enabled" class="controll-panel__checkbox-hide" id="white__autosignup_enabled" <?=($data['config']['automatization']['autosignup']['enabled'] != 0) ? 'checked' : '';?>>
					<label for="white__autosignup_enabled" class="controll-panel__checkbox">on <span class="controll-panel__checkbox-off">off</span></label>
					<span class="controll-panel__title">Модуль авторегистраций</span>
					<span class="controll-panel__description g-red" id="white__autosignup_timer"></span>
				</div>

				<div class="controll-panel__item">
					<div class="g-input controll-panel__input">
						<input type="text" name="min" value="<?=$data['config']['automatization']['autosignup']['min'];?>" data-value="<?=$data['config']['automatization']['autosignup']['max'];?>" placeholder="Минут" class="white__autosignup_action" data-action="min" data-type="number">
					</div>
					<span class="controll-panel__title">Минимальное время</span>
					<span class="controll-panel__description">в минутах</span>
				</div>

				<div class="controll-panel__item">
					<div class="g-input controll-panel__input">
						<input type="text" name="max" value="<?=$data['config']['automatization']['autosignup']['max'];?>" data-value="<?=$data['config']['automatization']['autosignup']['max'];?>" placeholder="Минут" class="white__autosignup_action" data-action="max" data-type="number">
					</div>
					<span class="controll-panel__title">Максимальное время</span>
					<span class="controll-panel__description">в минутах</span>
				</div>
				
				<div class="controll-panel__item">
					<div class="g-input controll-panel__input">
						<input type="text" name="password" value="<?=$data['config']['automatization']['autosignup']['password'];?>" data-value="<?=$data['config']['automatization']['autosignup']['password'];?>" placeholder="Минут" class="white__autosignup_action" data-action="password">
					</div>
					<span class="controll-panel__title">Пароль</span>
					<span class="controll-panel__description">для авторизации</span>
				</div>
				
				<?=$data['result']['sponsor'] ?? '';?>
				
				<div class="controll-panel__button-wrap">
					<span class="g-button blue" id="white__delete_sponsor">Удалить спонсора</span>
					<span class="g-button blue" id="white__add_new_sponsor">Добавить спонсора</span>
				</div>
				
				<div class="log-table__wrap" id="white__autosignup_list"></div>
				
				<div class="settinga__save-wrap settinga__save-wrap--new-list">
					<input type="file" name="list" id="white__autosignup_file" accept="text/plain">

					<div class="controll-panel__button-wrap controll-panel__button-wrap--autoreg">
						<span class="g-button blue" id="white__autosignup_download">Загрузить список</span>
						<span class="g-button blue" id="white__autosignup_delete_file">Удалить список</span>
					</div>
				</div>
			</div>
		</form>
		
		<form method="post" id="white__autofill_form" onsubmit="return false;">
			<div class="controll-panel">
				<div class="controll-panel__head">Автоматические пополнения</div>
				
				<div class="controll-panel__item">
					<input type="checkbox" name="enabled" class="controll-panel__checkbox-hide" id="white__autofill_enabled" <?=($data['config']['automatization']['autofill']['enabled'] != 0) ? 'checked' : '';?>>
					<label for="white__autofill_enabled" class="controll-panel__checkbox">on <span class="controll-panel__checkbox-off">off</span></label>
					<span class="controll-panel__title">Модуль автопополнений</span>
					<span class="controll-panel__description g-red" id="white__autofill_timer"></span>
				</div>
				
				<div class="controll-panel__item">
					<div class="g-input controll-panel__input">
						<input type="text" name="min" value="<?=$data['config']['automatization']['autofill']['min'];?>" placeholder="Минут" class="white__autofill_action" data-type="number">
					</div>
					<span class="controll-panel__title">Минимальное время</span>
					<span class="controll-panel__description">в минутах</span>
				</div>

				<div class="controll-panel__item">
					<div class="g-input controll-panel__input">
						<input type="text" name="max" value="<?=$data['config']['automatization']['autofill']['max'];?>" placeholder="Минут" class="white__autofill_action" data-type="number">
					</div>
					<span class="controll-panel__title">Максимальное время</span>
					<span class="controll-panel__description">в минутах</span>
				</div>
				
				<? 	foreach($data['config']['valutes'] as $key => $val):
						if(!$val['fill']) continue; ?>
						<div class="controll-panel__item">
							<div class="g-input controll-panel__input">
								<input type="text" name="<?=$key;?>" value="<?=$data['config']['automatization']['autofill'][$key];?>" placeholder="Число" class="white__autofill_action" data-type="number">
							</div>
							<span class="controll-panel__title">Вероятность выбора <?=strtoupper($key);?></span>
							<span class="controll-panel__description">цифры, относительно друг друга</span>
						</div>
				<? 	endforeach;?>
			
				<? 	foreach($data['config']['payments'] as $key => $val):
						if(!$val['enabled']) continue; ?>
						<div class="controll-panel__item">
							<div class="g-input controll-panel__input">
								<input type="text" name="<?=$key;?>" value="<?=$data['config']['automatization']['autofill'][$key];?>" placeholder="Число" class="white__autofill_action" data-type="number">
							</div>
							<span class="controll-panel__title">Вероятность выбора <?=$val['name'];?></span>
							<span class="controll-panel__description">цифры, относительно друг друга</span>
						</div>
				<? 	endforeach;?>
			
				<div class="controll-panel__item">
					<div class="g-input controll-panel__input">
						<input type="text" name="category1" value="<?=$data['config']['automatization']['autofill']['category1'];?>" placeholder="Число" class="white__autofill_action" data-type="number">
					</div>
					<span class="controll-panel__title">Вероятность выбора категории Small</span>
					<span class="controll-panel__description">цифры, относительно друг друга</span>
				</div>
				
				<div class="controll-panel__item">
					<div class="g-input controll-panel__input">
						<input type="text" name="category2" value="<?=$data['config']['automatization']['autofill']['category2'];?>" placeholder="Число" class="white__autofill_action" data-type="number">
					</div>
					<span class="controll-panel__title">Вероятность выбора категории Medium</span>
					<span class="controll-panel__description">цифры, относительно друг друга</span>
				</div>
				
				<div class="controll-panel__item">
					<div class="g-input controll-panel__input">
						<input type="text" name="category3" value="<?=$data['config']['automatization']['autofill']['category3'];?>" placeholder="Число" class="white__autofill_action" data-type="number">
					</div>
					<span class="controll-panel__title">Вероятность выбора категории High</span>
					<span class="controll-panel__description">цифры, относительно друг друга</span>
				</div>
				
				<div class="controll-panel__item">
					<div class="g-input controll-panel__input">
						<input type="text" name="category4" value="<?=$data['config']['automatization']['autofill']['category4'];?>" placeholder="Число" class="white__autofill_action" data-type="number">
					</div>
					<span class="controll-panel__title">Вероятность выбора категории Top</span>
					<span class="controll-panel__description">цифры, относительно друг друга</span>
				</div>
				
				<div class="controll-panel__item">
					<span class="controll-panel__title">Список категории Small</span>
					<span class="controll-panel__description">сумма::вероятность выбора (цифры)</span>
					<textarea class="controll-panel__textarea" name="category1list" placeholder="Список пуст"><?=$data['result']['autofill']['category-list-1'] ?? '';?></textarea>
				</div>
				
				<div class="controll-panel__item">
					<span class="controll-panel__title">Список категории Medium</span>
					<span class="controll-panel__description">сумма::вероятность выбора (цифры)</span>
					<textarea class="controll-panel__textarea" name="category2list" placeholder="Список пуст"><?=$data['result']['autofill']['category-list-2'] ?? '';?></textarea>
				</div>
				
				<div class="controll-panel__item">
					<span class="controll-panel__title">Список категории High</span>
					<span class="controll-panel__description">сумма::вероятность выбора (цифры)</span>
					<textarea class="controll-panel__textarea" name="category3list" placeholder="Список пуст"><?=$data['result']['autofill']['category-list-3'] ?? '';?></textarea>
				</div>
				
				<div class="controll-panel__item">
					<span class="controll-panel__title">Список категории Top</span>
					<span class="controll-panel__description">сумма::вероятность выбора (цифры)</span>
					<textarea class="controll-panel__textarea" name="category4list" placeholder="Список пуст"><?=$data['result']['autofill']['category-list-4'] ?? '';?></textarea>
				</div>
				
				<div class="settinga__save-wrap settinga__save-wrap--new-list">
					<div class="controll-panel__button-wrap">
						<span class="g-button blue" id="white__autofill_save_settings">Сохранить настройки</span>
					</div>
				</div>
				
				<div class="log-table__wrap" id="white__autofill_list"></div>
				
				<div class="settinga__save-wrap settinga__save-wrap--new-list">
					<span class="g-button blue" id="white__autofill_delete_file">Очистить лог</span>
				</div>
			</div>
		</form>
		
		<form method="post" id="white__autopayout_form" onsubmit="return false;">
			<div class="controll-panel">
				<div class="controll-panel__head">Автоматические выплаты</div>
				
				<div class="controll-panel__item">
					<input type="checkbox" name="enabled" class="controll-panel__checkbox-hide" id="white__autopayout_enabled">
					<label for="white__autopayout_enabled" class="controll-panel__checkbox">on <span class="controll-panel__checkbox-off">off</span></label>
					<span class="controll-panel__title">Модуль автовыплат</span>
					<span class="controll-panel__description g-red" id="white__autopayout_timer"></span>
				</div>
				
				<div class="controll-panel__item">
					<div class="g-input controll-panel__input">
						<input type="text" name="min" value="<?=$data['config']['automatization']['autopayout']['min'];?>" placeholder="Минут" class="white__autopayout_action" data-type="number">
					</div>
					<span class="controll-panel__title">Минимальное время</span>
					<span class="controll-panel__description">в минутах</span>
				</div>

				<div class="controll-panel__item">
					<div class="g-input controll-panel__input">
						<input type="text" name="max" value="<?=$data['config']['automatization']['autopayout']['max'];?>" placeholder="Минут" class="white__autopayout_action" data-type="number">
					</div>
					<span class="controll-panel__title">Максимальное время</span>
					<span class="controll-panel__description">в минутах</span>
				</div>
				
				<div class="controll-panel__item">
					<div class="g-input controll-panel__input">
						<input type="text" name="percent_min" value="<?=$data['config']['automatization']['autopayout']['percent_min'];?>" placeholder="Минут" class="white__autopayout_action" data-type="number">
					</div>
					<span class="controll-panel__title">Минимальная выплата</span>
					<span class="controll-panel__description">в процентах, от суммы пополнения</span>
				</div>
				
				<div class="controll-panel__item">
					<div class="g-input controll-panel__input">
						<input type="text" name="percent_max" value="<?=$data['config']['automatization']['autopayout']['percent_max'];?>" placeholder="Минут" class="white__autopayout_action" data-type="number">
					</div>
					<span class="controll-panel__title">Максимальная выплата</span>
					<span class="controll-panel__description">в процентах, от суммы пополнения</span>
				</div>
				
				<div class="settinga__save-wrap settinga__save-wrap--new-list">
					<div class="controll-panel__button-wrap">
						<span class="g-button blue" id="white__autopayout_save_settings">Сохранить настройки</span>
					</div>
				</div>
				
				<div class="log-table__wrap" id="white__autopayout_list"></div>
				
				<div class="settinga__save-wrap settinga__save-wrap--new-list">
					<span class="g-button blue" id="white__autopayout_delete_file">Очистить лог</span>
				</div>
			</div>
		</form>
    </div>

<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/footer.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/script.php'); ?>