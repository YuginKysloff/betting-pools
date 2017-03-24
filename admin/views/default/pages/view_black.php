<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/head.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/system-block.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/header.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/menu.php'); ?>
 
<div class="content view__blacklist">
	<form method="post" id="black__form" onsubmit="return false">
	<div class="blacklist__search-bar">
		<select name="category">
			<option value="ip">IP</option>
			<option value="login">Логин</option>
		</select>
		<div class="g-input security__search blacklist__search"><input type="text" name="value" id="black__input" placeholder="Введите значение"></div>
		<button class="g-button blue" id="black__add_button">Добавить</button>
		<span class="g-button blue" id="black__delete_button">Удалить</span>
	</div>
	</form>
	<div id="black__list"></div>
</div>

<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/footer.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/script.php'); ?>