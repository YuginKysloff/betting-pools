<? defined('SW_CONSTANT') or die?>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/head.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-header.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-menu.php')?>

<main class="account">
    <h1 class="g-h1">
        Аккаунт
    </h1>
    
    <form id="account__form" onsubmit="return false;">
        <div class="account__left-sitebar">
            <div class="account__row account__row--one">
                <span class="account__key">
                    Ваш логин
                </span>
                <div class="account__value">
                    <span class="account__login">
                        <span class="g-red">
                            <?=$data['user']['login']?>
                        </span>
                    </span>
                </div>
            </div>
            <div class="account__row">
                <span class="account__key">
                    Дата регистрации
                </span>
                <div class="account__value">
                    <span class="account__login">
                        <span class="g-grey">
                            <?=$data['user']['datetime']?>
                        </span>
                    </span>
                </div>
            </div>
            <div class="account__row account__row--two">
                <span class="account__key">
                    Пополнено
                </span>
                <div class="account__value">
                    <span class="account__login">
                        <span class="g-currency-usd">
                            <?=$data['result']['fill']['usd'] ?? 0?>
                        </span>
                    </span>
                </div>
            </div>
            <div class="account__row">
                <span class="account__key">
                    Выведено
                </span>
                <div class="account__value">
                    <span class="account__login">
                        <span class="g-currency-usd">
                            <?=$data['result']['payout']['usd'] ?? 0?>
                        </span>
                    </span>
                </div>
            </div>
            <div class="account__row account__row--three">
                <span class="account__key">
                    Реферальный процент
                </span>
                <div class="account__value">
                    <span class="account__login">
                        <span class="g-red">
                            <?=$data['user']['rcb']?>%
                        </span>
                    </span>
                </div>
            </div>
            <div class="account__row">
                <span class="account__key">
                    Рефералы
                </span>
                <div class="account__value">
                    <span class="account__login">
                        <?=$data['result']['ref']['count']?>
                        <span class="g-grey">
                            чел.
                        </span>
                    </span>
                </div>
            </div>
            <div class="account__row">
                <span class="account__key">
                    Доход от рефералов
                </span>
                <div class="account__value">
                    <span class="account__login">
                        <span class="g-currency-usd">
                            <?=$data['result']['referral_income']['usd'] ?? 0?>
                        </span>
                    </span>
                </div>
            </div>
            <div class="account__row account__row--four">
                <span class="account__key">
                    Спонсор
                </span>
                <div class="account__value">
                    <span class="account__login">
                        <? if($data['result']['sponsor']['login']):?>
                            <span class="g-red">
                                <?=$data['result']['sponsor']['login']?>
                            </span>
                        <? else:?>
                            <span class="g-grey">
                                отсутствует
                            </span>
                        <? endif?>
                    </span>
                </div>
            </div>
            <div class="account__row">
                <span class="account__key">
                    Ваш email
                </span>
                <div class="account__value">
                    <span class="account__login">
                        <span class="g-grey">
                            <?=$data['user']['email']?>
                        </span>
                    </span>
                </div>
            </div>
        </div>
        
        <? foreach($data['config']['payments'] as $key => $val):
            
            if(!$val['payout']) continue?>
            
            <div class="account__right-sitebar">
                <div class="cash">
                    <div class="cash__title">
                       <?=$val['name']?>
                    </div>
                    <div class="cash__value">
                        
                        <? if(isset($data['result']['wallets'][$key])):?>
                            <?=$data['result']['wallets'][$key]?>
                        <? else:?>
                            <input type="text" name="<?=$key?>" class="g-input-text" data-q data-placeholder="<?=$val['example']?>" value="" maxlength="100">
                        <? endif?>
                        
                    </div>
                    
                    <span class="cash__icon cash__icon--<?=$key?>"></span>
                </div>
            </div>
        <? endforeach?>
        
        <div class="account__new-password">
            <span class="account__new-password-title">
                Сменить пароль
            </span>
            <input type="password" name="password_old" class="g-input-text g-input-text--new-password"  data-q  data-placeholder="Текущий пароль" value="" maxlength="50">
            <input type="password" name="password_new" class="g-input-text g-input-text--new-password"  data-q  data-placeholder="Новый пароль" value="" maxlength="50">
            <div class="account__error-message" id="account__form_error"></div>
            <button class="g-button" id="account__button">
                Сохранить
            </button>
        </div>
    </form>
    
</main>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/footer.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/script.php')?>