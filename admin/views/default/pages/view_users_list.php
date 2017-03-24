<? defined('SW_CONSTANT') or die; ?>
<div class="cssload-loader">
    <div class="cssload-inner cssload-one"></div>
    <div class="cssload-inner cssload-two"></div>
    <div class="cssload-inner cssload-three"></div>
</div>
<table class="g-table log-table">
	<caption>Пользователи</caption>
	<? 	if(isset($data['result']['list']) && $data['result']['list']):?>
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
				<td class="g-grey user-table__status">Статус</td>
				<td class="g-grey">IP<br>E-mail</td>
				<td class="g-grey">Дата регистрации<br>Дата активности</td>
			</tr>
		</thead>
		<tbody>
		<? 	foreach($data['result']['list'] as $val):?>
			<tr>
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
					<span class="g-green g-table__child-row arrow-down"><?=$val['fill']['usd'] ?? 0;?></span>
					<span class="g-red g-table__child-row arrow-up"><?=$val['payout']['usd'] ?? 0;?></span>
				</td>
				<td>
					<span class="g-green g-table__child-row arrow-down"><?=$val['fill']['rub'] ?? 0;?></span>
					<span class="g-red g-table__child-row arrow-up"><?=$val['payout']['rub'] ?? 0;?></span>
				</td>
				<td>
					<span class="g-green g-table__child-row arrow-down"><?=$val['fill']['eur'] ?? 0;?></span>
					<span class="g-red g-table__child-row arrow-up"><?=$val['payout']['eur'] ?? 0;?></span>
				</td>
				<td>
					<span class="g-green g-table__child-row arrow-down"><?=$val['fill']['btc'] ?? 0;?></span>
					<span class="g-red g-table__child-row arrow-up"><?=$val['payout']['btc'] ?? 0;?></span>
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
						<span id="users_list__instant_<?=$val['id'];?>" class="<?=($val['instant'] == '1') ? "g-link__green" : "g-link__red";?> users_list__instant" data-id="<?=$val['id'];?>">Инстант</span>
						<span id="users_list__access_<?=$val['id'];?>" class="<?=($val['access'] == '1') ? "g-link__red" : "g-link__green"; ?> users_list__access" data-id="<?=$val['id'];?>">Блок</span>
					</span>
					<span class="g-table__child-row">
						<span id="users_list__password_<?=$val['id'];?>"><span class="g-link__orange users_list__password" data-id="<?=$val['id'];?>">Сменить пароль</span></span>
					</span>
				</td>
				<td>
					<span class="g-table__child-row g-green"><?=$val['ip'];?></span>
					<span class="g-table__child-row user-table__email-cell"><?=$val['email'];?></span>
				</td>
				<td>
					<span class="g-table__child-row g-grey"><?=$val['datetime'];?></span>
					<span class="g-table__child-row g-green"><?=$val['activity'];?></span>
				</td>
			</tr>
			<tr class="user-table__child-row">
				<td class="test_td" colspan=12>
					<div class="cont" data-id="<?=$val['id'];?>">
						<form method="post" id="users_list__form_<?=$val['id'];?>">
							<div>
								<select name="access">
									<option value="1" <? if($val['access'] == 1) echo "selected";?>>Заблокирован</option>
									<option value="2" <? if($val['access'] == 2) echo "selected";?>>Пользователь</option>
									<option value="3" <? if($val['access'] == 3) echo "selected";?>>Модератор</option>
									<option value="4" <? if($val['access'] == 4) echo "selected";?>>Администратор</option>
									<option value="5" <? if($val['access'] == 5) echo "selected";?>>Супер администратор</option>
								</select>
								<span class="g-grey">RCB : </span>
								<div class="g-input user-table__procent">
									<input type="text" name="rcb" placeholder="%" value="<?=$val['rcb'] ?? 'empty';?>">
								</div>
								<span class="g-grey">E-mail : </span>
								<div class="g-input user-table__email">
									<input type="text" name="email" placeholder="E-mail" value="<?=$val['email'] ?? 'empty';?>">
								</div>
								<span class="<? if($val['url'] == "") echo "g-grey";?>">
									URL : 
									<? if($val['url'] != ''):?>
										<a class="users__link-in" href="<?=$val['url'];?>" target="_blank"><?=$val['url'];?></a>
									<? else:?>
										Не определен
									<? endif;?>
								</span>
                                <i class="fa fa-floppy-o users_list__save" data-id="<?=$val['id'];?>"></i>
							</div>
							<div>
								<? foreach($data['config']['payments'] as $key => $value):?>
									<span class="g-grey"><?=$value['name'];?> : </span>
									<div class="g-input user-table__email">
										<? if($data['user']['access'] < 5):?>
											<?=$val['wallets'][$key] ?? '<span class="g-grey">Не указан</span>';?>
										<? else:?>
											<input type="text" name="<?=$key;?>" placeholder="<?=$value['example'];?>" value="<?=$val['wallets'][$key] ?? '';?>">
										<? endif;?>
									</div>
								<? endforeach;?>
							</div>
							<div>
								<? 	if($data['user']['access'] > 4):?>
										<? if(isset($val['usd'])):?><span class="g-grey">USD : </span><div class="g-input user-table__email"><input type="text" name="balance[usd]" placeholder="Сумма" value="<?=$val['usd'];?>"></div><? endif;?>
										<? if(isset($val['rub'])):?><span class="g-grey">RUB : </span><div class="g-input user-table__email"><input type="text" name="balance[rub]" placeholder="Сумма" value="<?=$val['rub'];?>"></div><? endif;?>
										<? if(isset($val['eur'])):?><span class="g-grey">EUR : </span><div class="g-input user-table__email"><input type="text" name="balance[eur]" placeholder="Сумма" value="<?=$val['eur'];?>"></div><? endif;?>
										<? if(isset($val['btc'])):?><span class="g-grey">BTC : </span><div class="g-input user-table__email"><input type="text" name="balance[btc]" placeholder="Сумма" value="<?=$val['btc'];?>"></div><? endif;?>
								<? 	endif;?>
							</div>
						</form>
					</div>
				</td>
			</tr>
		<? 	endforeach; ?>
		</tbody>
	<? 	else:?>
		<tr>
			<td class="g-grey">Нет результатов</td>
		</tr>
	<?	endif;?>
</table>
<script>
	$('select').fancySelect();
</script>
<?=$data['result']['pagination'] ?? '';?>