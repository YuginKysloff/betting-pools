<? defined('SW_CONSTANT') or die?>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/head.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-header.php')?>

    <main class="lost">
        <h1 class="g-h1">
            Востановить доступ
        </h1>
		
       	<form class="auth__block" id="lost__form" onsubmit="return false;">
       		<input type="text" name="email" class="g-input-text-nojs" placeholder="Введите логин">
            <div class="lost__error" id="lost__error"></div>
       		<button type="submit" class="g-button auth__button" id="lost__button">
                Восстановииь
            </button>
       	</form>
		
    </main>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/footer.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/script.php')?>