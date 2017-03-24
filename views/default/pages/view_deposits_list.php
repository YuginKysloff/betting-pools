<? defined('SW_CONSTANT') or die?>

<? if(isset($data['result']['list']) && $data['result']['list']):?>
    
    <? foreach($data['result']['list'] as $val):?>
        <div class="deposit__item">
            <div class="deposit__pools">
                <div class="deposit__pools-item">
                    <span class="g-red">
                        POOL
                    </span>
                </div>
                <div class="deposit__pools-item">
                    ID
                </div>
                <div class="deposit__pools-item">
                    <span class="g-red">
                        #<?=$val['pool_id']?>
                    </span>
                </div>
                <div class="deposit__pools-item">
                    Текущий процент пула
                </div>
                <div class="deposit__pools-item">
                    <span class="g-red">
                        <?=$val['percent']?>%
                    </span>
                </div>
                <div class="deposit__pools-item">
                    Сумма в пуле
                </div>
                <div class="deposit__pools-item">
                    <span class="g-currency-usd g-currency-usd--pools">
                        <?=$val['pool_amount']?>
                    </span>
                </div>
            </div>
            <div class="deposit__table">
                <div class="deposit__row">
                    <div class="deposit__cell">
                        ID депозита
                    </div>
                    <div class="deposit__cell">
                        <span class="g-red">
                            #<?=$val['id']?>
                        </span>
                    </div>
                </div>
                <div class="deposit__row">
                    <div class="deposit__cell">
                        Сумма депозита
                    </div>
                    <div class="deposit__cell">
                        <span class="g-currency-usd">
                            <?=$val['amount']?>
                        </span>
                    </div>
                </div>
                <div class="deposit__row">
                    <div class="deposit__cell">
                        Актив (тело депозита)
                    </div>
                    <div class="deposit__cell">
                        <span class="g-currency-usd">
                            <?=$val['rest']?>
                        </span>
                    </div>
                </div>
                <div class="deposit__row">
                    <div class="deposit__cell">
                        Начисленная прибыль
                    </div>
                    <div class="deposit__cell">
                        <span class="g-currency-usd">
                            <?=$val['accrued']?>
                        </span>
                    </div>
                </div>
                <div class="deposit__row">
                    <div class="deposit__cell">
                        Комиссия на вывод
                    </div>
                    <div class="deposit__cell">
                        <span class="g-red">
                            <?=$val['commission_percent']?>%
                        </span>
                    </div>
                </div>
                <div class="deposit__row">
                    <div class="deposit__cell">
                        Дата создания
                    </div>
                    <div class="deposit__cell">
                        <span class="g-grey">
                            <?=$val['datetime']?>
                        </span>
                    </div>
                </div>
                
                <? if($val['amount'] == $val['payout']):?>
                        <div class="deposit__row">
                            <div class="deposit__cell">
                                Дата закрытия
                            </div>
                            <div class="deposit__cell">
                                <span class="g-grey">
                                    <?=$val['next']?>
                                </span>
                            </div>
                        </div>
                <? else:?>
                        <div class="deposit__row">
                            <div class="deposit__cell">
                                Следующее начисление
                            </div>
                            <div class="deposit__cell">
                                
                                <? if(isset($val['timer'])):?>
                                    <span id="timer<?=$val['id']?>" class="g-red">--:--:--</span>
									<script>
										if(typeof refresh_id<?=$val['id']?> !=='undefined') clearTimeout(refresh_id<?=$val['id']?>);
										if(typeof timer_id<?=$val['id']?> !=='undefined') clearInterval(timer_id<?=$val['id']?>);
										var hour<?=$val['id']?> = <?=$val['timer']['hour']?>;
										var minute<?=$val['id']?> = <?=$val['timer']['minute']?>;
										var second<?=$val['id']?> = <?=$val['timer']['second']?>;
										function timer<?=$val['id']?>() {
											$("#timer<?=$val['id']?>").html((hour<?=$val['id']?> < 10 ? "0" + hour<?=$val['id']?> : hour<?=$val['id']?>) + ":" + (minute<?=$val['id']?> < 10 ? "0" + minute<?=$val['id']?> : minute<?=$val['id']?>) + ":" + (second<?=$val['id']?> < 10 ? "0" + second<?=$val['id']?> : second<?=$val['id']?>));
											--second<?=$val['id']?>;
											if(second<?=$val['id']?> < 0) {
												--minute<?=$val['id']?>;
												if(minute<?=$val['id']?> < 0) {
													--hour<?=$val['id']?>;
													if(hour<?=$val['id']?> < 0) {
														refresh_id<?=$val['id']?> = setTimeout(function(){
															process('/deposits/get_list/p/'+$(".pagination .pagination__button--active").html(), 'list', '#deposits__list');
														}, 5000);
													}
													minute<?=$val['id']?> = 59;
												}
												second<?=$val['id']?> = 59;
											}
										}
										timer_id<?=$val['id']?> = setInterval(function(){timer<?=$val['id']?>()}, 1000);
										timer<?=$val['id']?>();
									</script>
                                <? else:
                                
                                        if($val['amount'] > $val['payout']):?>
                                            <span>
                                                00:00:00
                                            </span>
                                        <? else:?>
                                            <span>
                                                Закрыт
                                            </span>
                                        <? endif?>
                                    
                                <? endif?>
                                
                            </div>
                        </div>
                <? endif?>
                
                <? if($val['amount'] > $val['payout']):?>
                        <form method="post" id="deposits__form<?=$val['id']?>" onsubmit="return false;">
                            <div class="deposit__bar">
                                <input type="text" name="amount" data-q data-placeholder="Введите сумму" class="g-input-text">
                                <div id="deposits__form<?=$val['id']?>_error"></div>
                                <button class="g-button g-button--deposit" id="deposits__form<?=$val['id']?>_button" data-id="<?=$val['id']?>">
                                    Вывести тело депозита
                                </button>
                            </div>
                        </form>
                <? endif?>
                
            </div>
        </div>
        <div class="g-hr"></div>
    <? endforeach?>
    
        <?=$data['result']['pagination'] ?? ''?>
<? else:?>
    <div class="deposit__item">
        Нет депозитов
    </div>
    <div class="g-hr"></div>
<? endif?>