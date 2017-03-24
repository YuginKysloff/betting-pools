<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/head.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/system-block.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/header.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/menu.php'); ?>
 
    <div class="content view__statistics">
        <div class="statistics-table__wrap">
			<div>
				<div id="statistics__finance"></div>
			</div>
		</div>
        <div class="statistics__sitebar">
            <div class="statistics__wallets">
                <div class="wallets">
                    <span class="wallets__title">Кошельки</span>
                    <div id="statistics__wallets"></div>
                </div>
            </div>
            <div class="statistics__registration">
                <div class="registration">
                    <span class="registration__title">Регистрации</span>
                    <div id="statistics__signup"></div>
                </div>
            </div>
        </div>
        <div class="statistics__tabs">
            <div class="tabs">
                <div class="tabs__menu">
                    <span id="statistics__last_fill" class="tabs__menu-item active" data-tabs-id="1">Пополнения</span>
                    <span id="statistics__last_payout" class="tabs__menu-item" data-tabs-id="2">Выплаты</span>
                    <span id="statistics__last_signup" class="tabs__menu-item" data-tabs-id="3">Регистрации</span>
                    <span id="statistics__last_url" class="tabs__menu-item" data-tabs-id="4">Источники</span>
                </div>
                <div class="tabs__content" id="statistics__last"></div>
            </div>
        </div>
    </div>

<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/footer.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/script.php'); ?>