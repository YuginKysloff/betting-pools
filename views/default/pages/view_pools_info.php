<? defined('SW_CONSTANT') or die?>

<? if(isset($data['result']['deposits']) && $data['result']['deposits']):?>
	
	<? foreach($data['result']['deposits'] as $val):?>
		<span class="pull__info-item">
			<span class="pull__username" title="Логин">
				<?=$val['login']?>
			</span>
			<span class="pull__summ pull__summ--up g-currency-usd" title="Инвестировано в POOL">
				<?=$val['amount']?>
			</span>
			<span class="pull__summ pull__summ--down g-currency-usd" title="Выведено из POOLа">
				<?=$val['payout']?>
			</span>
			<span class="pull__summ pull__summ--left g-currency-usd" title="Начислено прибыли на активы">
			&#9668;
				<?=$val['accrued']?>
			</span>
		</span>
	<? endforeach?>
	
<? else:?>
	<div class="g-grey">
		В текущем пуле нет инвесторов
	</div>
<? endif?>