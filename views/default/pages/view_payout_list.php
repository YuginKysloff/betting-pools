<?	defined('SW_CONSTANT') or die?>

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
						<?=$val['payment']?>
					</td>
					<td class="g-paid">
						
						<? if(!$val['status']):?>
							<span class="g-wait">
								Ожидание
							</span>
						<? elseif($val['status'] == '1'):?>
							<span class="g-success">
								Выведено
							</span>
						<? elseif($val['status'] == '2'):?>
							<span class="g-danied">
								Отказ
							</span>
						<? endif?>
						
					</td>
					<td>
						<time>
							<?=$val['datetime']?>
						</time>
					</td>
				</tr>
			<? endforeach?>
		
		</tbody>
	</table>
	<?=$data['result']['pagination'] ?? ''?>
<?	else:?>
	Нет выплат
<?	endif?>