<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/load.php'); ?>
<table class="g-table">
    <? if(isset($data['result']['list']) && $data['result']['list']):?>
        <thead>
			<tr>
				<td class="g-grey" colspan=3>Список пользователей</td>
			</tr>
			<tr>
				<td class="g-grey">Логин</td>
				<td class="g-grey">E-mail</td>
				<td class="g-grey">Удалить</td>
			</tr>
		</thead>
        <tbody>
        <? 	foreach($data['result']['list'] as $key => $val): ?>
                <tr>
					<td>
						<?=$val['login'];?>
					</td>
					<td>
						<?=$val['email'];?>
					</td>
					<td>
						<i class="fa fa-times g-link__red delete white_autosignup_list__val" data-line="<?=$key;?>"></i>
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
