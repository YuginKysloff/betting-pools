<? defined('SW_CONSTANT') or die?>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/head.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-header.php')?>

<main class="news">
	<div id="news__list"><?=$data['result']['view'] ?? ''?></div>
</main>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/footer.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/script.php')?>