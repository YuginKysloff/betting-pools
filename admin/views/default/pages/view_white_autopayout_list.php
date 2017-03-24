<? defined('SW_CONSTANT') or die; ?>
<table class="g-table">
    <? if(isset($data['result']['list']) && $data['result']['list']):?>
        <thead>
			<tr>
				<td class="g-grey" colspan=2>Лог событий</td>
			</tr>
			<tr>
				<td class="g-grey">Дата</td>
				<td class="g-grey">Текст</td>
			</tr>
		</thead>
        <tbody>
        <? 	foreach($data['result']['list'] as $key => $val): ?>
                <tr>
					<td>
						<span class="g-table__child-row"><?=$val['datetime'];?></span>
					</td>
					<td>
						<span class="g-table__child-row user-table__email-cell"><?=$val['text'];?></span>
					</td>
				</tr>
        <? 	endforeach; ?>
        </tbody>
    <? else:?>
        <tr>
			<td class="g-grey">Список пуст</td>
		</tr>
    <?endif;?>
</table>
<?=$data['result']['pagination'] ?? '';?>
