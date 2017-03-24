<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/head.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/system-block.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/header.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/menu.php'); ?>
 
    <div class="content view__pay-out">
        <div class="pay-out-table__wrap">
            <table class="g-table pay-out-table">
                <caption>Выплаты</caption>
				<? if($data['result']['payouts']):?>
                <thead>
                    <tr>
                        <td class="g-grey">Дата</td>
                        <td class="g-grey">Логин</td>
                        <td class="g-grey">Валюта</td>
                        <td class="g-grey">Выведено</td>
                        <td class="g-grey">ЭПС</td>
                        <td class="g-grey">Подтвердить</td>
                        <td class="g-grey">Отказать</td>
                        <td class="g-grey pay-out-table__reason">Причина</td>
                    </tr>
                </thead>
                    <tbody id="payout__list">
                        <? foreach($data['result']['payouts'] as $val):?>
                            <tr id="payout__str<?=$val['id'];?>" data-id="<?=$val['id'];?>">
                                <td class="g-grey g-table__data"><?=$val['datetime'];?></td>
                                <td>
                                    <a class="g-link__usr" target="_blank" href="/<?=$data['config']['site']['admin'];?>/users/index/login/<?=$val['login'];?>"><?=$val['login'];?></a>
                                </td>
                                <td><?=$val['valute'];?></td>
                                <td class="g-red arrow-up"><?=$val['amount'];?></td>
                                <td><?=$val['payment'];?></td>
                                <td class="pay__success"><span class="g-link__confirm">Подтвердить</span></td>
                                <td class="pay__fail"><span class="g-link__to-refuse">Отказать</span></td>
                                <td class="g-orange"><?=$val['reason'];?></td>
                            </tr>
                        <?endforeach;?>
                    </tbody>
                <? else:?>
                    <tr>
						<td class="g-grey">Нет заявок</td>
					</tr>
                <?endif;?>
            </table>
            <?=$data['result']['pagination'] ?? '';?>
        </div>
    </div>

<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/footer.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/script.php'); ?>