<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/head.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/system-block.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/header.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/menu.php'); ?>
 
    <div class="content view__log">
		<div class="user__search-bar">
			<div class="search-bar">
                <form method="post" id="users__form" onsubmit="return false;">
                    <select name="regexp">
                        <option value="0">!ABc</option>
                        <option value="1">ABc</option>
                    </select>
                    <select name="like">
                        <option value="">LIKE</option>
                        <option value="%val">% LIKE</option>
                        <option value="val%">LIKE %</option>
                        <option value="%val%">% LIKE %</option>
                    </select>
                    <select name="field">
                        <option value="login">Логин</option>
                        <option value="email">E-mail</option>
                        <option value="sponsor">Спонсор</option>
                        <option value="ip">IP</option>
                    </select>
                    <div class="g-input search-bar__input">
                        <input type="text" name="value" value="<?=$data['route']['param']['login'] ?? '';?>" placeholder="Найти">
                    </div>
                    <button class="g-button blue" id="users__search_button">Поиск</button>
					<span id="users__emails_block"><span class="g-button blue" id="users__download_emails_button">Скачать базу</span></span>
                </form>
			</div>
		</div>
        <div class="log-table__wrap" id="users__list"></div>
    </div>

<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/footer.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/script.php'); ?>
