<? defined('SW_CONSTANT') or die?>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/head.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-header.php')?>

    <main class="register">
        <h1 class="g-h1">
            Регистация
        </h1>
        
       	<form class="register__block" id="signup__form" onsubmit="return false;">
            <input type="text" name="login" class="g-input-text-nojs g-input-text-nojs--reg" placeholder="Логин">
            <input type="text" name="email" class="g-input-text-nojs g-input-text-nojs--reg" placeholder="e-mail">
            <input type="password" name="password" class="g-input-text-nojs g-input-text-nojs--reg" placeholder="Пароль">
            
            <? foreach($data['config']['payments'] as $key => $val):
                
                if(!$val['payout']) continue?>
                
                <input type="text" name="wallet[<?=$key?>]" class="g-input-text-nojs g-input-text-nojs--reg" placeholder="<?=$val['example'] ?? ''?>">
            <? endforeach?>
            
            <div id="signup__error" class="g-red"></div>
            <input type="submit" id="signup__button" class="g-button auth__button" value="Войти">
       	</form>
        
    </main>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/footer.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/script.php')?>