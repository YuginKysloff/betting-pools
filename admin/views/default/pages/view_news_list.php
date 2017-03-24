<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/load.php'); ?>
<? if($data['result']['list']):?>
	<? foreach($data['result']['list'] as $val): ?>
		<div class="post">
			<div class="post__top-bar">
				<span class="post__title"><?=$val['title'];?></span>
				<div class="post__right-bar" data-datetime="<?=$val['datetime_base'];?>">
					<span class="news__edit g-link__edit">Редактировать</span>
					<span class="news__delete g-link__red">Удалить</span>
					<span class="g-grey" data-time="<?=$val['datetime_input'];?>"><?=$val['datetime'];?></span>
				</div>
			</div>
			<post class="" lang="ru">
			<?=$val['text'];?>
			<? if(isset($val['others'])):?>
				<? foreach($val['others'] as $item): ?>
					<div class="post__error"><?=$item;?></div>
				<? endforeach;?>
			<? endif;?>
			</post>
		</div>
	<? endforeach;?>
	<?=$data['result']['pagination'] ?? '';?>
<? else:?>
	<span class="g-grey">Новостей нет</span>
<? endif;?>