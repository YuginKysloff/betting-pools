<? defined('SW_CONSTANT') or die?>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/head.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-header.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-menu.php')?>

<main class="payout">
    <h1 class="g-h1">
        Вывести средства
    </h1>
    
    <? if($data['config']['site']['payout'] && $data['config']['valutes']['usd']['payout']) :?>

        <form id="payout__form" onsubmit="return false;">
            <div class="pay-system">
                
                <?foreach($data['config']['payments'] as $key => $val):
                    
                    if(!$val['payout']) continue?>
                    
                    <div class="pay-system__item" data-system="<?=$key?>">
                        <span class="pay-system__icon"></span>
                        <div class="pay-system__title pay-system__title--<?=$key?>">
                            <?=$val['name']?>
                        </div>
                    </div>
                <? endforeach?>
                
                <input type="hidden" name="valute" value="usd">
                <input type="text" class="pay-system__hidden" name="payment" hidden value="">
            </div>
            <div class="payout__moneybar">
                <input type="text" name="amount" data-q data-placeholder="Сумма" value="" class="g-input-text" maxlength="8">
            </div>
            <button id='payout__form_button'>
                Вывести
            </button>
            <div id='payout__form_error' class="g-red"></div>
        </form>

    <?	else:?>
        <span class="g-error">
            <p>
                Вывод средств отключен
            </p>
        </span>
    <?	endif?>
    
    <div id="payout__list"></div>
</main>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/footer.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/script.php')?>