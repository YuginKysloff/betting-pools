<? defined('SW_CONSTANT') or die; ?>
<form id="news__form" name="news__form" onsubmit="return false;">      
	<input type="hidden" name="edit" value="">
	<?	foreach($data['config']['lang']['list'] as $val):
			$i = isset($i) ? ++$i : 0;
			if($i == 1):?>
				<div class="g-input news__title date"><input  class="news__datepicker" type="text" name="datetime" value="<?=$data['result']['current_date'] ?? '';?>">
					<i class="fa fa-calendar"></i>
				</div>
			<? endif;?>
		<h3 class="news__h3"><img src="/download/images/flags/<?=strtoupper($val);?>.png" height="20"> <?=$data['lang']['lang--list'][$val];?></h3>
		<div class="g-input news__title" lang="<?=$val;?>">
			<input type="text" name="title[<?=$val;?>]" placeholder="<?=$data['lang']['lang--list'][$val];?> заголовок" value="<?=$data['result']['news'][$val]['title'] ?? '';?>">
		</div>
		<div class="news__wysiwyg" data-lang="<?=$val;?>">
			<textarea id="ck_text_<?=$val;?>" name="text[<?=$val;?>]"><?=$data['result']['news'][$val]['text'] ?? '';?></textarea>
		</div>
	<? 	endforeach;?>
</form>
<div id="news__error"></div>
<span class="g-button blue" id="news__save_button">Сохранить</span>
<script>wysiwyg_instance();datepicker();</script>