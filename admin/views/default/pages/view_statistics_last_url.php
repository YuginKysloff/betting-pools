<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/load.php'); ?>				
<table class="g-table tabs-table">
<? if($data['result']['list']):?>
	<thead>
		<tr>
			<td class="g-grey">Пользователи</td>
			<td class="g-grey">Источник перехода</td>
			<td class="g-grey">%</td>
		</tr>
	</thead>
	<tbody>
		<?foreach($data['result']['list'] as $key => $val):?>
			<tr>
				<td class="g-grey"><?=$val['count'];?></td>
				<td class="g-grey"><? if($key != ''):?><a class="g-link__usr" href="http://<?=$key;?>" target="_blank"><?=$key;?></a><? else:?>Не определен<? endif;?></td>
				<td class="g-green"><?=$val['percent'];?>%</td>
			</tr>
		<?endforeach;?>
	</tbody>    
<? else:?>
	<tr>
		<td class="g-grey">Нет результатов</td>
	</tr>
<?endif;?>
</table>