<? defined('SW_CONSTANT') or die; ?>
<? 	if(isset($data['result']['list']) && $data['result']['list']):?>
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
					<span class="g-table__value--left"><?=Lib_Main::beauty_number(($val['fill'][$valute]['real'] ?? 0) + ($val['fill'][$valute]['white'] ?? 0));?></span>
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
<?endif;?>