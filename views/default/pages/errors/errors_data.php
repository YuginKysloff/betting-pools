<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/head.php'); ?>


	<div class="errors-blocked">
		<img src="/views/default/img/logo-end-text.png" alt="">

		<div class="g-error-message">
			<div class="g-error-message__title">Ошибка данных</div>
			<div class="g-error-message__description">Произошла ошибка при передачи данных, попробуйте снова</div>
			<div class="g-error-message__icon g-error-message__icon--data"></div>
		</div>

		<a href="/" class="g-button">На главную</a>
	</div>



<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/script.php'); ?>