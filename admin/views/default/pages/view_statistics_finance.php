<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/load.php'); ?>
<table class="g-table statistics-table">
<? if($data['result']['list']):?>
	<caption>Финансы</caption>
	<thead>
		<tr>
			<td class="g-grey">Дата</td>
			<td class="g-grey">Валюта</td>
			<td class="g-grey">Пополнено</td>
			<td class="g-grey">Выведено</td>
			<td class="g-grey">Разница</td>
		</tr>
	</thead>
	<tbody>
	<? foreach($data['result']['list'] as $key => $val):?>
		<? 	$i = 0;
			foreach($data['config']['valutes'] as $valute => $v):
			++$i;?>
			<tr>
				<? if($i == 1):?>
					<td class="g-grey" rowspan="<?=count($data['config']['valutes'])?>"><?=date($data['config']['formats']['admin_date'], strtotime($key));?></td>
				<? endif;?>
				<td>
					<span class=""><?=strtoupper($valute);?></span>
				</td>
				<td class="g-green">
					<span class=" g-table__value--left"><?=Lib_Main::beauty_number(($val['fill'][$valute]['real'] ?? 0) + ($val['fill'][$valute]['white'] ?? 0));?></span>
					<span class="g-grey g-table__value--center">&equiv;</span>
					<span class="g-table__value--right"><?=Lib_Main::beauty_number($val['fill'][$valute]['real'] ?? 0);?></span>
				</td>
				<td class="g-red">
					<span class=" g-table__value--left"><?=Lib_Main::beauty_number(($val['payout'][$valute]['real'] ?? 0) + ($val['payout'][$valute]['white'] ?? 0));?></span>
					<span class="g-grey g-table__value--center">&equiv;</span>
					<span class="g-table__value--right"><?=Lib_Main::beauty_number($val['payout'][$valute]['real'] ?? 0);?></span>
				</td>
				<td class="g-red">
					<span class=" g-table__value--left <? if(((($val['fill'][$valute]['real'] ?? 0) + ($val['fill'][$valute]['white'] ?? 0)) - (($val['payout'][$valute]['real'] ?? 0) + ($val['payout'][$valute]['white'] ?? 0))) > 0):?>g-green<?else:?>g-red<?endif;?>"><?=Lib_Main::beauty_number((($val['fill'][$valute]['real'] ?? 0) + ($val['fill'][$valute]['white'] ?? 0)) - (($val['payout'][$valute]['real'] ?? 0) + ($val['payout'][$valute]['white'] ?? 0)));?></span>
					<span class="g-grey g-table__value--center">&equiv;</span>
					<span class="g-table__value--right <? if(($val['fill'][$valute]['real'] ?? 0) - ($val['payout'][$valute]['real'] ?? 0) > 0):?>g-green<?else:?>g-red<?endif;?>"><?=Lib_Main::beauty_number(($val['fill'][$valute]['real'] ?? 0) - ($val['payout'][$valute]['real'] ?? 0));?></span>
				</td>
			</tr>
		<?	endforeach;?>
	<?endforeach;?>
	<? if($data['result']['more']):?>
		<tr id="statistics__all_finance">
			<td class="table-button" colspan="5">
				<span class="g-button blue" id="statistics__show_all_finance">Все записи</span>
			</td>
		</tr>
	<?endif;?>
	<?	$i = 0;
		foreach($data['config']['valutes'] as $valute => $v):
		++$i;?>
		<tr>
			<? if($i == 1):?>
				<td class="g-grey" rowspan="<?=count($data['config']['valutes'])?>">Всего</td>
			<? endif;?>
			<td>
				<span class=""><?=strtoupper($valute);?></span>
			</td>
			<td class="g-green">
				<span class="g-table__value--left"><?=Lib_Main::beauty_number(($data['result']['general']['fill'][$valute]['real'] ?? 0) + ($data['result']['general']['fill'][$valute]['white'] ?? 0));?></span>
				<span class="g-grey g-table__value--center">&equiv;</span>
				<span class="g-table__value--right"><?=Lib_Main::beauty_number($data['result']['general']['fill'][$valute]['real'] ?? 0);?></span>
			</td>
			<td class="g-red">
				<span class="g-table__value--left"><?=Lib_Main::beauty_number(($data['result']['general']['payout'][$valute]['real'] ?? 0) + ($data['result']['general']['payout'][$valute]['white'] ?? 0));?></span>
				<span class="g-grey g-table__value--center">&equiv;</span>
				<span class="g-table__value--right"><?=Lib_Main::beauty_number($data['result']['general']['payout'][$valute]['real'] ?? 0);?></span>
			</td>
			<td class="g-red">
				<span class="g-table__value--left <? if(((($data['result']['general']['fill'][$valute]['real'] ?? 0) + ($data['result']['general']['fill'][$valute]['white'] ?? 0)) - (($data['result']['general']['payout'][$valute]['real'] ?? 0) + ($data['result']['general']['payout'][$valute]['white'] ?? 0))) > 0):?>g-green<?else:?>g-red<?endif;?>"><?=Lib_Main::beauty_number((($data['result']['general']['fill'][$valute]['real'] ?? 0) + ($data['result']['general']['fill'][$valute]['white'] ?? 0)) - (($data['result']['general']['payout'][$valute]['real'] ?? 0) + ($data['result']['general']['payout'][$valute]['white'] ?? 0)));?></span>
				<span class="g-grey g-table__value--center">&equiv;</span>
				<span class="g-table__value--right <? if(($data['result']['general']['fill'][$valute]['real'] ?? 0) - ($data['result']['general']['payout'][$valute]['real'] ?? 0) > 0):?>g-green<?else:?>g-red<?endif;?>"><?=Lib_Main::beauty_number(($data['result']['general']['fill'][$valute]['real'] ?? 0) - ($data['result']['general']['payout'][$valute]['real'] ?? 0));?></span>
			</td>
		</tr>
	<?	endforeach;?>
	</tbody>
<? else:?>
	<tr>
		<td class="g-grey">Нет результатов</td>
	</tr>
<?endif;?>
</table>