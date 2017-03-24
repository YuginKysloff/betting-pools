<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/load.php'); ?>
<table class="g-table security-table">
    <caption>Безопасность</caption>
    <? if($data['result']['list']):?>
        <thead>
            <tr>
                <td class="g-grey">Дата</td>
                <td class="g-grey">Логин</td>
                <td class="g-grey">IP</td>
                <td class="g-grey security-table__comments">Комментарий</td>
            </tr>
        </thead>
        <tbody>
        <? foreach($data['result']['list'] as $key => $val):?>
            <tr>
                <td class="g-grey g-table__data"><?=$val['datetime'];?></td>
                <td class=""><span class="security-table__login g-link__usr"><?=$val['login'] ?? '';?></span></td>
                <td class=""><span class="security-table__ip g-link__green"><?=$val['ip'] ?? '';?></span></td>
                <td class="security-table__comments <?=($val['category'] == '1') ? 'security-table__comments--warning' : '';?>"><?=$val['text'];?></td>
            </tr>
        <? endforeach;?>
        </tbody>
    <? else:?>
        <tr>
			<td class="g-grey">Нет результатов</td>
		</tr>
    <?endif;?>
</table>
<?=$data['result']['pagination'] ?? '';?>
