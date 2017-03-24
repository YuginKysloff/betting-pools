<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/head.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/system-block.php'); ?>
 
    <div class="view__login">
		<img src="/admin/views/<?=$template;?>/img/login__logo.png" class="login__logo">
		<div class="auth">
			<div class="login__wrap">
				<form method="post" id="login__form" onsubmit="return false;">
					<div class="g-input login" data-validation-type="login"><input type="text" name="login" placeholder="Логин"></div>
					<div class="g-input login" data-validation-type="password"><input type="password" name="password" placeholder="Пароль"><i class="fa fa-eye-slash show-password"></i></span></div>
					<label for="login__check" class="g-checkbox login">
						<input type="checkbox" name="remember" id="login__check" checked>
						<span class="g-checkbox__title">Запомнить меня</span>
					</label>
					<div id="auth__error"></div>
					<button class="g-button login" id="login__button">Войти</button>
				</form>
			</div>
		</div>
	</div>

<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/script.php'); ?>