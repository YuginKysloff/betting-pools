<? defined('SW_CONSTANT') or die?>

<? if(isset($data['result']['list']) && $data['result']['list']):?>
	<table class="g-table g-table--payout">
		<thead>
			<tr>
				<th>
					Сумма
				</th>
				<th>
					Платежная система
				</th>
				<th>
					Статус
				</th>
				<th>
					Дата выплаты
				</th>
			</tr>
		</thead>
		<tbody>
		
			<? foreach($data['result']['list'] as $val):?>
				<tr>
					<td>
						<span class="g-currency-usd">
							<?=$val['amount']?>
						</span>
					</td>
					<td>
						<?=$data['config']['payments'][$val['payment']]['name']?>
					</td>
					<td class="g-paid">
						<span class="g-success">
							Пополнено
						<span>
					</td>
					<td>
						<time>
							<?=$val['datetime']?>
						</time>
					</td>
				</tr>
			<? 	endforeach?>
		
		</tbody>
	</table>
	<?=$data['result']['pagination'] ?? ''?>
<? else:?>
	Нет платежей
<? endif?>