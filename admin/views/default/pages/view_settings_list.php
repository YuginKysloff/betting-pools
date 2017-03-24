<? defined('SW_CONSTANT') or die; ?>
<form method="post" id="settings_list__list">
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/load.php'); ?>
<table class="g-table settings-table">
	<caption>Настройки</caption>

	<thead>
		<tr>
			<td class="settings-table__main">Описание</td>
			<td>Значение</td>
			<td>Ключ</td>
			<td class="settings-table__edit"><i data-globalOpen="no" class="fa fa-pencil-square-o global" aria-hidden="true"></i></td>
			<td class="settings-table__sort">Сортировка</td>
			<td class="settings-table__del">X</td>
		</tr>
	</thead>
	<tbody>
	<? 	foreach($data['list'] as $val):
		if(isset($val['parent_id'])) {
			$level = $val['level']; ?>
			<tr id="settings_category_str<?=$val['id'];?>" style="background: #F5F5F5;">
				<td class="g-green" style="padding-left: <?=($level * 30);?>px;"><div class="g-input settings__disabled"><input readonly type="text" name="settings_category[comment][<?=$val['id'];?>]" placeholder="Описание" value="<?=$val['comment'];?>"></div></td>
				<td class="g-green"></td>
				<td class="g-green"><div class="g-input settings__disabled"><input readonly type="text" name="settings_category[name][<?=$val['id'];?>]" placeholder="Ключ" value="<?=$val['name'];?>"></div></td>
				<td class="settings-table__edit g-green"><i data-open="no" class="fa fa-pencil-square-o" aria-hidden="true"></i></td>
				<td class="settings-table__sort g-green"><div class="g-input settings__disabled"><input readonly type="text" name="settings_category[sort][<?=$val['id'];?>]" placeholder="Сортировка" value="<?=$val['sort'];?>"></div></td>
				<td class="settings-table__del g-red "><i class="fa fa-times g-link__red settings_list__delete_category" data-id="<?=$val['id'];?>" aria-hidden="true"></i></td>
			</tr>
		<?
		} else {
		?>
			<tr class="setting__gear" id="settings_str<?=$val['id'];?>">
				<td class="g-green" style="padding-left: <?=(($level + 1) * 25);?>px;"><div class="g-input settings__disabled"><input readonly type="text" name="settings[comment][<?=$val['id'];?>]" placeholder="Описание" value="<?=$val['comment'];?>"></div></td>
				<td class="g-green"><div class="g-input settings__disabled">
					<input readonly type="text" name="settings[value][<?=$val['id'];?>]" placeholder="Значение" value="<?=$val['value'];?>">
				</div></td>
				<td class="g-green"><div class="g-input settings__disabled"><input readonly type="text" name="settings[name][<?=$val['id'];?>]" placeholder="Ключ" value="<?=$val['name'];?>"></div></td>
				<td class="settings-table__edit g-green"><i data-open="no" class="fa fa-pencil-square-o" aria-hidden="true"></i></td>
				<td class="settings-table__sort g-green"><div class="g-input settings__disabled"><input readonly type="text" name="settings[sort][<?=$val['id'];?>]" placeholder="Сортировка" value="<?=$val['sort'];?>"></div></td>
				<td class="settings-table__del g-red "><i class="fa fa-times settings_list__delete" data-id="<?=$val['id'];?>" aria-hidden="true"></i></td>
			</tr>
		<?
		}
		?>
	<?	endforeach;?>
	</tbody>
</table>
</form>
<div class="settinga__save-wrap">
	<span class="g-button blue" id="settings_list__save">Сохранить</span>
</div>

<script>
	settings.editInput();
</script>