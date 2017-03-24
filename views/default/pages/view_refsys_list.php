<? defined('SW_CONSTANT') or die?>

<? if($data['result']['list']):?>
	<table class="refelas__table">
		<tr class="refelas__table-tr">
			<th class="refelas__table-th refelas__table-th--login">
				Логин
			</th>
			<th class="refelas__table-th">
				Доход от реферала
			</th>
			<th class="refelas__table-th">
				E-mail
				<span class="refelas__table-sub-row">
					Источник перехода
				</span>
			</th>
			<th class="refelas__table-th refelas__table-th--date">
				Даты выплаты
				<span class="refelas__table-sub-row">
					Последняя активность
				</span>
			</th>
		</tr>
		
		<? foreach($data['result']['list'] as $val):?>
			<tr class="refelas__table-tr">
				<th class="refelas__table-td g-red">
					<?=$val['login']?>
				</th>
				<th class="refelas__table-td">
					<div class="g-currency-usd">
						<?=$val['ref_income']['usd'] ?? '0'?>
					</div>
				</th>
				<th class="refelas__table-td g-red">
					<span class="refelas__table-sub-row">
						
						<? if($val['url']):?>
							<?=$val['url']?>
						<? else:?>
							не определен
						<? endif?>
						
					</span>
				</th>
				<th class="refelas__table-td g-grey">
					<span>
						<?=$val['activity']?>
					</span>
					<span class="refelas__table-sub-row refelas__table-sub-row--red">
						<span>
							<?=$val['datetime']?>
						</span>
					</span>
				</th>
			</tr>
		<? endforeach?>
		
	</table>
<? else:?>
	Нет рефералов
<? endif?>