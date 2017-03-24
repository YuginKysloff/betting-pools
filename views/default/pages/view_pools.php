<? defined('SW_CONSTANT') or die?>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/head.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-header.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-menu.php')?>

<main class="pools-usd">
    <h1 class="g-h1">
        Пулы USD
    </h1>
    <div id="pools__list"></div>
</main>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/footer.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/script.php')?>