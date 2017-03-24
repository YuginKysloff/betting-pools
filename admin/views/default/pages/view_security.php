<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/head.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/system-block.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/header.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/menu.php'); ?>
 
    <div class="content view__security">
		<div class="security__search-bar">
			<form method="post" id="security__form" onsubmit="return false;">
				<select name="category">
					<option value="login">Логин</option>
					<option value="ip">IP</option>
				</select>
				<div class="g-input security__search"><input type="text" name="value" placeholder="Найти"></div>
				<button class="g-button blue" id="security__button">Поиск</button>
			</form>
		</div>
        <div class="security-table__wrap" id="security__list"></div>
    </div>

<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/footer.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/script.php'); ?>