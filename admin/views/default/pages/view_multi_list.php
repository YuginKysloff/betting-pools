<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/load.php'); ?>
<table class="g-table multi-table">
    <caption>Мультиаккаунты (<?=$data['result']['archive'] == 1 ? 'обработанные' : 'необработанные';?>)</caption>
    <? if(isset($data['result']['first']) && $data['result']['first']):?>
		<thead>
			<tr>
				<td class="g-grey">Аватар</td>
				<td class="g-grey">ID<br>Рефералы</td>
				<td class="g-grey">Логин<br>Спонсор</td>
				<td class="g-grey">USD</td>
				<td class="g-grey">RUB</td>
				<td class="g-grey">EUR</td>
				<td class="g-grey">BTC</td>
				<td class="g-grey" colspan=2>Баланс</td>
				<td class="g-grey">Статус</td>
				<td class="g-grey">IP<br>E-mail</td>
				<td class="g-grey">Дата регистрации<br>Дата активности</td>
			</tr>
		</thead>
        <tbody>
        <? foreach($data['result']['first'] as $val):?>
            <tr id="multi__first_<?=$val['id'];?>" class="multi__first multi__first_<?=$val['id'];?>">
                <td class="g-grey g-table__data">
					<div class="user__avatar">
						<img src="/download/images/avatar/min/<?=empty($val['avatar']) ? 'empty.jpg' : $val['avatar'];?>" data-id="<?=$val['id'];?>" class="users_list__avatar" id="users_list__avatar_<?=$val['id'];?>">
						<span class="users_list__avatar_delete" data-id="<?=$val['id'];?>">Удалить</span>
					</div>
				</td>
				<td class="g-grey g-table__data">
					<span class="g-table__child-row">#<?=$val['id'];?></span>
					<span class="g-table__child-row"><i class="fa fa-user-plus user-table__add-user"></i><?=$val['refs'];?></span>
				</td>
				<td class="g-grey">
					<span class="g-table__child-row"><a class="g-link__usr" href="/<?=$data['config']['site']['admin'];?>/log/index/login/<?=$val['login'];?>" target="_blank"><?=$val['login'];?></a></span>
					<span class="g-table__child-row g-grey"><a class="g-link__usr" href="/<?=$data['config']['site']['admin'];?>/log/index/login/<?=$val['sponsor'] ?? '';?>" target="_blank"><?=$val['sponsor'] ?? 'admin';?></a></span>
				</td>
				<td>
					<span class="g-green g-table__child-row arrow-down"><?=$val['usd_fill'] ?? 0;?></span>
					<span class="g-red g-table__child-row arrow-up"><?=$val['usd_payout'] ?? 0;?></span>
				</td>
				<td>
					<span class="g-green g-table__child-row arrow-down"><?=$val['rub_fill'] ?? 0;?></span>
					<span class="g-red g-table__child-row arrow-up"><?=$val['rub_payout'] ?? 0;?></span>
				</td>
				<td>
					<span class="g-green g-table__child-row arrow-down"><?=$val['eur_fill'] ?? 0;?></span>
					<span class="g-red g-table__child-row arrow-up"><?=$val['eur_payout'] ?? 0;?></span>
				</td>
				<td>
					<span class="g-green g-table__child-row arrow-down"><?=$val['btc_fill'] ?? 0;?></span>
					<span class="g-red g-table__child-row arrow-up"><?=$val['btc_payout'] ?? 0;?></span>
				</td>
				<td>
					<span class="g-table__child-row <?=isset($val['usd']) ? 'g-green' : 'g-grey';?>">&#36; <?=$val['usd'] ?? '-';?></span>
					<span class="g-table__child-row <?=isset($val['rub']) ? 'g-green' : 'g-grey';?>">&#8381; <?=$val['rub'] ?? '-';?></span>
				</td>
				<td>
					<span class="g-table__child-row <?=isset($val['eur']) ? 'g-green' : 'g-grey';?>">&euro; <?=$val['eur'] ?? '-';?></span>
					<span class="g-table__child-row <?=isset($val['btc']) ? 'g-green' : 'g-grey';?>">&#579; <?=$val['btc'] ?? '-';?></span>
				</td>
				<td class="g-grey">
					<span class="g-table__child-row">
						<span class="users_list__instant_<?=$val['id'];?> <?=($val['instant'] == '1') ? "g-link__green" : "g-link__red";?> users_list__instant" data-id="<?=$val['id'];?>">Инстант</span>
						<span class="users_list__access_<?=$val['id'];?> <?=($val['access'] == '1') ? "g-link__red" : "g-link__green"; ?> users_list__access" data-id="<?=$val['id'];?>">Блок</span>
					</span>
					<span class="g-table__child-row">
						<? if($val['archive'] == 0):?>
							<span class="users_list__archive_<?=$val['id'];?>"><span class="g-link__orange users_list__archive" data-type="first" data-id="<?=$val['id'];?>">В архив</span></span>
						<? else:?>
							<span class="g-grey">В архиве</span>
						<? endif;?>
						<span class="users_list__delete_<?=$val['id'];?>"><span class="g-link__red users_list__delete" data-type="first" data-id="<?=$val['id'];?>">Удалить</span></span>
					</span>
				</td>
				<td>
					<span class="g-table__child-row g-green"><?=$val['ip'];?></span>
					<span class="g-table__child-row"><?=$val['email'];?></span>
				</td>
				<td>
					<span class="g-table__child-row g-grey"><?=$val['datetime'];?></span>
					<span class="g-table__child-row g-green"><?=$val['activity'];?></span>
				</td>
            </tr>
            <? if(is_array($val['second'])):?>
                <? foreach($val['second'] as $second_val):?>
					<tr id="multi__second_<?=$second_val['id'];?>" class="multi__second multi__first_<?=$val['id'];?>">
						<td class="g-grey g-table__data">
							<div class="user__avatar">
								<img src="/download/images/avatar/min/<?=empty($second_val['avatar']) ? 'empty.jpg' : $second_val['avatar'];?>" data-id="<?=$second_val['id'];?>" class="users_list__avatar" id="users_list__avatar_<?=$second_val['id'];?>">
								<span class="users_list__avatar_delete" data-id="<?=$second_val['id'];?>">Удалить</span>
							</div>
						</td>
						<td class="g-grey g-table__data">
							<span class="g-table__child-row">#<?=$second_val['id'];?></span>
							<span class="g-table__child-row"><i class="fa fa-user-plus user-table__add-user"></i><?=$second_val['refs'];?></span>
						</td>
						<td class="g-grey">
							<span class="g-table__child-row"><a class="g-link__usr" href="/<?=$data['config']['site']['admin'];?>/log/index/login/<?=$second_val['login'];?>" target="_blank"><?=$second_val['login'];?></a></span>
							<span class="g-table__child-row g-grey"><a class="g-link__usr" href="/<?=$data['config']['site']['admin'];?>/log/index/login/<?=$second_val['sponsor'] ?? '';?>" target="_blank"><?=$second_val['sponsor'] ?? 'admin';?></a></span>
						</td>
						<td>
							<span class="g-green g-table__child-row arrow-down"><?=$second_val['usd_fill'] ?? 0;?></span>
							<span class="g-red g-table__child-row arrow-up"><?=$second_val['usd_payout'] ?? 0;?></span>
						</td>
						<td>
							<span class="g-green g-table__child-row arrow-down"><?=$second_val['rub_fill'] ?? 0;?></span>
							<span class="g-red g-table__child-row arrow-up"><?=$second_val['rub_payout'] ?? 0;?></span>
						</td>
						<td>
							<span class="g-green g-table__child-row arrow-down"><?=$second_val['eur_fill'] ?? 0;?></span>
							<span class="g-red g-table__child-row arrow-up"><?=$second_val['eur_payout'] ?? 0;?></span>
						</td>
						<td>
							<span class="g-green g-table__child-row arrow-down"><?=$second_val['btc_fill'] ?? 0;?></span>
							<span class="g-red g-table__child-row arrow-up"><?=$second_val['btc_payout'] ?? 0;?></span>
						</td>
						<td>
							<span class="g-table__child-row <?=isset($second_val['usd']) ? 'g-green' : 'g-grey';?>">&#36; <?=$second_val['usd'] ?? '-';?></span>
							<span class="g-table__child-row <?=isset($second_val['rub']) ? 'g-green' : 'g-grey';?>">&#8381; <?=$second_val['rub'] ?? '-';?></span>
						</td>
						<td>
							<span class="g-table__child-row <?=isset($second_val['eur']) ? 'g-green' : 'g-grey';?>">&euro; <?=$second_val['eur'] ?? '-';?></span>
							<span class="g-table__child-row <?=isset($second_val['btc']) ? 'g-green' : 'g-grey';?>">&#579; <?=$second_val['btc'] ?? '-';?></span>
						</td>
						<td class="g-grey">
					<span class="g-table__child-row">
						<span class="users_list__instant_<?=$second_val['id'];?> <?=($second_val['instant'] == '1') ? "g-link__green" : "g-link__red";?> users_list__instant" data-id="<?=$second_val['id'];?>">Инстант</span>
						<span class="users_list__access_<?=$second_val['id'];?> <?=($second_val['access'] == '1') ? "g-link__red" : "g-link__green"; ?> users_list__access" data-id="<?=$second_val['id'];?>">Блок</span>
					</span>
					<span class="g-table__child-row">
						<? if($second_val['archive'] == 0):?>
							<span class="users_list__archive_<?=$second_val['id'];?>"><span class="g-link__orange users_list__archive" data-type="second" data-id="<?=$second_val['id'];?>">В архив</span></span>
						<? else:?>
							<span class="g-grey">В архиве</span>
						<? endif;?>
						<span class="users_list__delete_<?=$second_val['id'];?>"><span class="g-link__red users_list__delete" data-type="second" data-id="<?=$second_val['id'];?>">Удалить</span></span>
					</span>
						</td>
						<td>
							<span class="g-table__child-row g-green"><?=$second_val['ip'];?></span>
							<span class="g-table__child-row"><?=$second_val['email'];?></span>
						</td>
						<td>
							<span class="g-table__child-row g-grey"><?=$second_val['datetime'];?></span>
							<span class="g-table__child-row g-green"><?=$second_val['activity'];?></span>
						</td>
					</tr>
                <? endforeach;?>
            <? endif;?>
        <? endforeach;?>
        </tbody>
    <? else:?>
        <tr><td class="g-grey">Нет данных</td></tr>
    <?endif;?>
</table>
<?=$data['result']['pagination'] ?? '';?>