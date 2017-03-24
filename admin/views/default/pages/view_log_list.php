<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/load.php'); ?>
<div class="log-table__wrap">
    <table class="g-table log-table">
        <caption>Логи</caption>

		<? if(!isset($data['result']['list'])):?>
			<tr>
				<td class="g-grey">Пользователь не найден</td>
			</tr>
		<? else:?>
			<? if($data['result']['list']):?>
			<thead>
			<tr>
				<td class="g-grey">Дата</td>
				<td class="g-grey">Логин</td>
				<td class="g-grey log-table__comments">Комментарий</td>
				<td class="g-grey">USD</td>
			</tr>
			</thead>
			<tbody>
			<? foreach($data['result']['list'] as $val):?>
				<tr>
					<td class="g-grey g-table__data"><?=$val['datetime'];?></td>
					<td class="g-blue"><span class="g-link__usr log-login"><?=$val['login'];?></span></td>
					<td class="log-table__comments"><?=$val['text'];?></td>
					<td class="g-green"><?=$val['usd'];?></td>
				</tr>
			<? endforeach;?>
			</tbody>
			<? else:?>
				<tr>
					<td class="g-grey">Нет результатов</td>
				</tr>
			<? endif;?>
		<? endif;?>
    </table>
    <?=$data['result']['pagination'] ?? '';?>
</div>
