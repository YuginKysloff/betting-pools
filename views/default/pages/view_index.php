<? defined('SW_CONSTANT') or die?>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/head.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/main-header.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-menu.php')?>

<main class="index">
    <h1 class="g-h1">
        Заголовок h1
    </h1>
    <div class="index__post">
        <p>
            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Enim sequi, error voluptates consectetur fuga veritatis unde animi sint harum illum. Velit doloremque quos reiciendis explicabo laudantium, earum enim, sed. Necessitatibus.Lorem ipsum dolor sit amet, consectetur adipisicing elit. Enim sequi, error voluptates consectetur fuga veritatis unde animi sint harum illum. Velit doloremque quos reiciendis explicabo laudantium, earum enim, sed. Necessitatibus.
        </p>
        <p>
            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Enim sequi, error voluptates consectetur fuga veritatis unde animi sint harum illum. Velit doloremque quos reiciendis explicabo laudantium, earum enim, sed. Necessitatibus.Lorem ipsum dolor sit amet, consectetur adipisicing elit. Enim sequi, error voluptates consectetur fuga veritatis unde animi sint harum illum. Velit doloremque quos reiciendis explicabo laudantium, earum enim, sed. Necessitatibus.
        </p>
    </div>
    <h2 class="g-h2">
        Активные пулы
    </h2>
    <div id="index__last_pool"></div>
    <h2 class="g-h2">
        Общая статистика
    </h2>
    <div class="general-statistics">
        <div class="general-statistics__item">
            <span class="general-statistics__title">
                Инвесторы
            </span>
            <div class="general-statistics__row-wrap">
                <span class="general-statistics__row">
                    <?=$data['result']['all_users']?>
                </span>
            </div>
            <span class="general-statistics__icon general-statistics__icon--users"></span>
        </div>
        <div class="general-statistics__item">
            <span class="general-statistics__title">
                Пополнено
            </span>
            <div class="general-statistics__row-wrap">
                <span class="general-statistics__row">
                    <span class="g-currency-usd">
                        <?=$data['result']['payouts']['usd']?>
                    </span>
                </span>
                <span class="general-statistics__row"></span>
            </div>
            <span class="general-statistics__icon general-statistics__icon--payin"></span>
        </div>
        <div class="general-statistics__item">
            <span class="general-statistics__title">
                Выплачено
            </span>
            <div class="general-statistics__row-wrap">
                <span class="general-statistics__row">
                    <span class="g-currency-usd">
                        <?=$data['result']['reserve']['usd']?>
                    </span>
                </span>
                <span class="general-statistics__row">
                </span>
            </div>
            <span class="general-statistics__icon general-statistics__icon--payout"></span>
        </div>
    </div>
    <h2 class="g-h2">
        Подробная статистика
    </h2>
    <div class="detalied-statistics">
        <div class="detalied-statistics__payin">
            
            <? if($data['result']['fill']):?>
                
                <? foreach ($data['result']['fill'] as $val):?>
                    <div class="detalied-statistics__item">
                        <span class="detalied-statistics__curryncy">
                            <span class="g-currency-usd g-currency-usd--detalied-statistics">
                                 <?=$val['amount']?>
                            </span>
                        </span>
                        <span class="detalied-statistics__type">
                            Пополнение
                        </span>
                        <span class="detalied-statistics__username">
                            <?=$val['login']?>
                        </span>
                        <span class="detalied-statistics__datetime">
                            <?=$val['datetime']?>
                        </span>
                        <span class="detalied-statistics__icon detalied-statistics__icon--payeer"></span>
                        <span class="detalied-statistics__arrow detalied-statistics__arrow--down"></span>
                    </div>
                <? endforeach?>
                
            <? else:?>
                Нет пополнений
            <? endif?>
            
        </div>
        <div class="detalied-statistics__payout">
            
            <? if($data['result']['payout']):?>
                
                <? foreach ($data['result']['payout'] as $val):?>
                    <div class="detalied-statistics__item">
                        <span class="detalied-statistics__curryncy">
                            <span class="g-currency-usd g-currency-usd--detalied-statistics">
                                <?=$val['amount']?>
                            </span>
                        </span>
                        <span class="detalied-statistics__type">
                            Выплата
                        </span>
                        <span class="detalied-statistics__username">
                             <?=$val['login']?>
                        </span>
                        <span class="detalied-statistics__datetime">
                            <?=$val['datetime']?>
                        </span>
                        <span class="detalied-statistics__icon detalied-statistics__icon--payeer"></span>
                        <span class="detalied-statistics__arrow detalied-statistics__arrow--up"></span>
                    </div>
                <? endforeach?>
                
            <? else:?>
                Нет выплат
            <? endif?>
            
        </div>
    </div>
</main>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/footer.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/script.php')?>