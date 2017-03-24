<? defined('SW_CONSTANT') or die?>

<? if(isset($data['result']['list']) && $data['result']['list']):?>
	
	<? foreach($data['result']['list'] as $val):?>
		<div class="log__item">
		  <span>
			 <?=$val['text']?>
		  </span>
		  <time class="log__datetime">
			  <?=$val['datetime']?>
		  </time>
		</div>
	<? endforeach?>
	
	<?=$data['result']['pagination'] ?? ''?>
<? else:?>
		Нет результатов
<? endif?>