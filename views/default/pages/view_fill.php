<? defined('SW_CONSTANT') or die?>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/head.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-header.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-menu.php')?>

    <main class="payout">
        <h1 class="g-h1">
            Пополнить баланс
        </h1>
        
        <? if($data['config']['site']['fill'] && $data['config']['valutes']['usd']['fill']):?>
            
            <form id="fill__form" onsubmit="return false;">
                <div class="pay-system">
                    
                    <? foreach($data['config']['payments'] as $key => $val):
                        
                        if(!$val['fill']) continue;
                        
                        $enabled = true?>
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
                
                <? if(isset($enabled)):?>
                    <div class="payout__moneybar">
                        <input type="text" name="amount" data-q placeholder="Сумма" value="" maxlength="8" class="g-input-text">
                    </div>
                    <button id='fill__form_button'>
                        Пополнить
                    </button>
                <? else:?>
                    <span class="g-error">
                        Платежные системы не доступны
                    </span>
                <? endif?>
                
                <div id='fill__form_error' class="g-red"></div>
            </form>
            
        <?else:?>
            <span class="g-error">
                На данный момент возможность пополнений закрыта
            </span>
        <?endif?>
        
        <div id="fill__list"></div>
    </main>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/footer.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/script.php')?>