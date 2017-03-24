<? defined('SW_CONSTANT') or die?>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/head.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-header.php')?>

<main class="lost">
    <h1 class="g-h1">
        Валидация IP
    </h1>
    <form class="auth__block" id="check__form" onsubmit="return false;">
        <input type="text" name="email" class="g-input-text-nojs" value="<?=$data['result']['email']?>" readonly>
        <input type="text" name="code" class="g-input-text-nojs" placeholder="Введите PIN-код">
        <div class="lost__error" id="check__form_error"></div>
        <button type="submit" class="g-button auth__button" id="check__form_button">
            Отправить
        </button>
    </form>
</main>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/footer.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/script.php')?>