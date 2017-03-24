<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/load.php'); ?>
<table class="g-table tabs-table">
<? 	if($data['result']['list']):?>
        <thead>
            <tr>
                <td class="g-grey">Дата</td>
                <td class="g-grey">Логин</td>
                <td class="g-grey">E-mail</td>
                <td class="g-grey">URL</td>
                <td class="g-grey">Спонсор</td>
            </tr>
        </thead>
        <tbody>
        <? 	foreach($data['result']['list'] as $val):?>
                <tr>
					<td class="g-grey g-table__data"><?=$val['datetime'];?></td>
					<td class="g-blue">
                        <a class="g-link__usr" href="/<?=$data['config']['site']['admin'];?>/users/index/login/<?=$val['login'];?>" target="_blank"><?=$val['login'];?></a>
                    </td>
					<td class="g-green"><?=$val['email'];?></td>
					<td class="g-grey">
						<? if($val['url']):?>
							<a class="g-link" href="<?=$val['url'];?>" target="_blank"><?=$val['url'];?></a>
						<? else:?>
							Не определен
						<? endif;?>
					</td>
					<td class="g-green">
						<? if($val['sponsor']):?>
							<a class="g-link__usr" href="/<?=$data['config']['site']['admin'];?>/users/index/login/<?=$val['sponsor'];?>" target="_blank"><?=$val['sponsor'];?></a>
						<? else:?>
							Не определен
						<? endif;?>
					</td>
                </tr>
        <?	endforeach;?>
        </tbody>
<? 	else:?>
        <tr>
			<td class="g-grey">Нет результатов</td>
		</tr>
<?	endif;?>
</table>
<?=$data['result']['pagination'] ?? '';?>