<? defined('SW_CONSTANT') or die?>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/head.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-header.php')?>

    <main class="auth">
        <h1 class="g-h1">
			<?=$data['lang']['login']['auth'];?>
		</h1>
		
       	<form class="auth__block" id="login__form" onsubmit="return false;">
       		<input type="text" name="login" class="g-input-text-nojs" placeholder="<?=$data['lang']['login']['login'];?>">
       		<input type="password" name="password" class="g-input-text-nojs" placeholder="<?=$data['lang']['login']['password'];?>">
       		<input type="checkbox" name="remember" class="g-checkbox" data-q data-title="<?=$data['lang']['login']['remember'];?>" id="save-me">
			<a href="/lost" class="auth__lost">
				<?=$data['lang']['login']['lost-pass'];?>
			</a>
       		<input type="submit" id ="login__button" class="g-button auth__button" value="<?=$data['lang']['login']['enter'];?>">
			<div class="g-red" id="auth__error"></div>
			<div class="auth__message-for-registr">
				<?=$data['lang']['login']['not-reg'];?>?
				<a href="/signup" class="g-red">
					<?=$data['lang']['login']['reg'];?>
				</a>
			</div>
       	</form>
		
    </main>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/footer.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/script.php')?>