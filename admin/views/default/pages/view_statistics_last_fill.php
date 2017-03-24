<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/load.php'); ?>
<table class="g-table tabs-table">
<? 	if($data['result']['list']):?>
        <thead>
            <tr>
                <td class="g-grey">Дата</td>
                <td class="g-grey">Логин</td>
                <td class="g-grey">Платежная система</td>
                <td class="g-grey">Валюта</td>
                <td class="g-grey">Сумма</td>
            </tr>
        </thead>
        <tbody>
        <? 	foreach($data['result']['list'] as $val):?>
                <tr>
                    <td class="g-grey g-table__data"><?=$val['sort'];?></td>
                    <td class="g-blue">
                        <a class="g-link__usr" href="/<?=$data['config']['site']['admin'];?>/users/index/login/<?=$val['login'];?>" target="_blank"><?=$val['login'];?></a>
                    </td>
                    <td class="g-black"><?=$val['payment'];?></td>
                    <td class="g-black"><?=$val['valute'];?></td>
                    <td class="g-green"><?=$val['amount'];?></td>
                </tr>
        <?	endforeach;?>
        </tbody>
<? 	else:?>
        <tr>
			<td class="g-grey">Нет результатов</td>
		</tr>
<?	endif;?>
</table>
<?=$data['result']['pagination'] ?? '';?>