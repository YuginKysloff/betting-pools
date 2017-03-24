<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/load.php'); ?>
<table class="g-table blacklist__ip g-table--blacklist">
    <caption>
		IP<span class="g-table__title2">Данным IP запрещен доступ к сайту</span>
	</caption>
        <tbody>
            <? if($data['black_ip']):?>
                <? foreach($data['black_ip'] as $val):?>
                    <tr>
                        <td><span class="blacklist-table__ip g-link__usr"><?=$val['ip'];?></span></td>
                    </tr>
                <? endforeach;?>
            <? else:?>
				<tr>
                    <td><span class="g-grey">Нет результатов</span></td>
                </tr>
            <? endif;?>
        </tbody>
</table>

<table class="g-table blacklist__login g-table--blacklist">
    <caption>
		Логины<br>
		<span class="g-table__title2">Данные логины запрещены к регистрации</span>
	</caption>
        <tbody>
            <? if($data['black_login']):?>
                <? foreach($data['black_login'] as $val):?>
                    <tr>
                        <td><span class="blacklist-table__login g-link__usr"><?=$val['login'];?></span></td>
                    </tr>
                <? endforeach;?>
            <? else:?>
				<tr>
                    <td><span class="g-grey">Нет результатов</span></td>
                </tr>
            <? endif;?>
        </tbody>
</table>