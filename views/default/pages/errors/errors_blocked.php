<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/head.php'); ?>


	<div class="errors-blocked">
		<img src="/views/default/img/logo-end-text.png" alt="">

		<div class="g-error-message">
			<div class="g-error-message__title">Аккаунт заблокирован</div>
			<div class="g-error-message__description">Вам ограничен доступ, служба поддержки</div>
			<div class="g-error-message__icon g-error-message__icon--blocked"></div>
		</div>

		<a href="mailto:supports@asdd.sd" class="errors-blocked__mail-link">supports@asdd.sd</a>
	</div>



<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/script.php'); ?>