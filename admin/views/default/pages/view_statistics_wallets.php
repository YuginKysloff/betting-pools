<? defined('SW_CONSTANT') or die; ?>	
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/load.php'); ?>
<?	foreach($data['config']['payments'] as $key => $val):?>
		<div class="wallets__cash" style="position:relative;">
			<img src="/admin/views/<?=$template;?>/img/<?=$key;?>.png" title="<?=$val['name'];?>" alt="" class="wallets__logo">
			<span class="wallets__balance">
				<? 	if(isset($data['result']['wallets'][$key])):?>
						<span class="wallets__usd">USD : <span class="g-green"><?=$data['result']['wallets'][$key]['usd'] ?? '<span class="g-grey">--</span>';?></span></span>
						<span class="wallets__eur">EUR : <span class="g-green"><?=$data['result']['wallets'][$key]['eur'] ?? '<span class="g-grey">--</span>';?></span></span>
						<span class="wallets__rub">RUB : <span class="g-green"><?=$data['result']['wallets'][$key]['rub'] ?? '<span class="g-grey">--</span>';?></span></span>
						<span class="wallets__rub">BTC : <span class="g-green"><?=$data['result']['wallets'][$key]['btc'] ?? '<span class="g-grey">--</span>';?></span></span>
				<? 	else:?>
						<span class="g-grey">Необходимо настроить</span>
				<? 	endif;?>
				<img class="wallets__enable statistics_wallets__payment" id="statistics_wallets__payment_<?=$key;?>" src="/admin/views/<?=$template;?>/img/<?=$val['enabled'] == '1' ? 'enabled' : 'disabled'; ?>.png" data-payment="<?=$key;?>" alt="">
			</span>
		</div>
<?	endforeach;?>
		<div class="wallets__cash" style="position:relative;">
			<img src="/admin/views/<?=$template;?>/img/total.png" title="Всего" alt="" class="wallets__logo">
			<span class="wallets__balance">
				<span class="wallets__currency wallets__currency--usd">USD : <span class="g-green"><?=$data['result']['wallets']['total']['usd'] ?? '<span class="g-grey">--</span>';?></span></span>
				<span class="wallets__currency wallets__currency--eur">EUR : <span class="g-green"><?=$data['result']['wallets']['total']['eur'] ?? '<span class="g-grey">--</span>';?></span></span>
				<span class="wallets__currency wallets__currency--rub">RUB : <span class="g-green"><?=$data['result']['wallets']['total']['rub'] ?? '<span class="g-grey">--</span>';?></span></span>
				<span class="wallets__currency wallets__currency--btc">BTC : <span class="g-green"><?=$data['result']['wallets']['total']['btc'] ?? '<span class="g-grey">--</span>';?></span></span>
			</span>
		</div>