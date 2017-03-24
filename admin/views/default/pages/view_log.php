<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/head.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/system-block.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/header.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/menu.php'); ?>
 
<div class="content view__log">
	<div class="security__search-bar">
		<form method="post" id="log__form" onsubmit="return false">
			<div class="g-input log__search">
				<input type="text" class="log__input" name="login" value="<?=$data['route']['param']['login'] ?? ''?>" placeholder="Логин">
				<button class="g-button blue" id="log__button">Поиск</button>
			</div>
		</form>
	</div>
	<div id="log__list"></div>
</div>

<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/footer.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/script.php'); ?>