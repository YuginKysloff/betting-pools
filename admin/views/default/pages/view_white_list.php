<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/load.php'); ?>
<table class="g-table log-table">
    <caption>Белый лист</caption>
    <? if(isset($data['result']['list']) && $data['result']['list']):?>
        <thead>
            <tr>
                <td class="g-grey">Аватар</td>
				<td class="g-grey">ID пользователя<br>Рефералы</td>
				<td class="g-grey">Логин<br>Спонсор</td>
				<td class="g-grey">USD</td>
				<td class="g-grey">RUB</td>
				<td class="g-grey">EUR</td>
				<td class="g-grey">BTC</td>
                <td class="g-grey">Удалить</td>
            </tr>
        </thead>
        <tbody>
        <? 	foreach($data['result']['list'] as $val): ?>
                <tr id="white_list__str_<?=$val['id'];?>" <? if($val['user_id'] == '0'):?>class="???"<? endif;?>>
					<td class="g-grey g-table__data">
						<img src="/download/images/avatar/min/<?=empty($val['avatar']) ? 'empty.jpg' : $val['avatar'];?>" data-id="<?=$val['user_id'];?>" id="white_list__avatar_<?=$val['user_id'];?>">
						<span class="white_list__avatar_delete" data-id="<?=$val['user_id'];?>">Удалить</span>
					</td>
					<td class="g-grey g-table__data">
						<span class="g-table__child-row">#<?=$val['user_id'];?></span>
						<span class="g-table__child-row"><i class="fa fa-user-plus user-table__add-user"></i><?=$val['refs'] ?? '?';?></span>
					</td>
                    <td class="g-grey">
						<span class="g-table__child-row"><span class="g-link__usr insert_login"><?=$val['login'];?></span></span>
						<span class="g-table__child-row g-grey"><span class="g-link__usr insert_login"><?=$val['sponsor'] ?? 'admin';?></span></span>
					</td>
                    <td>
						<span class="g-green g-table__child-row arrow-down fill-usd"><?=$val['fill']['usd'] ?? 0;?></span>
						<span class="g-red g-table__child-row arrow-up payout-usd"><?=$val['payout']['usd'] ?? 0;?></span>
					</td>
					<td>
						<span class="g-green g-table__child-row arrow-down fill-rub"><?=$val['fill']['rub'] ?? 0;?></span>
						<span class="g-red g-table__child-row arrow-up payout-rub"><?=$val['payout']['rub'] ?? 0;?></span>
					</td>
					<td>
						<span class="g-green g-table__child-row arrow-down fill-eur"><?=$val['fill']['eur'] ?? 0;?></span>
						<span class="g-red g-table__child-row arrow-up payout-eur"><?=$val['payout']['eur'] ?? 0;?></span>
					</td>
					<td>
						<span class="g-green g-table__child-row arrow-down fill-btc"><?=$val['fill']['btc'] ?? 0;?></span>
						<span class="g-red g-table__child-row arrow-up payout-btc"><?=$val['payout']['btc'] ?? 0;?></span>
					</td>
                    <td class="g-red">
                        <i class="fa fa-times g-link__red delete" data-id="<?=$val['id'];?>"></i>
                    </td>
                </tr>
        <? 	endforeach; ?>
        </tbody>
    <? else:?>
        <tr>
			<td class="g-grey">Список пуст</td>
		</tr>
    <?endif;?>
</table>
<?=$data['result']['pagination'] ?? '';?>
