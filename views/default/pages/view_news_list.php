<? defined('SW_CONSTANT') or die?>
	<h1 class="g-h1">
		Новости
	</h1>

<? if(isset($data['result']['list']) && $data['result']['list']):?>
	
	<? foreach($data['result']['list'] as $val):?>
		<div class="news__block view__news news_list__item" data-id="<?=$val['id']?>">
			<div class="news__item">
				<div class="news__description">
					<?=$val['title']?>
				</div>
				<span class="news__year">
					<?=$val['year']?>
				</span>
				<span class="news__month">
					<?=$val['month']?>
				</span>
				<span class="news__day">
					<?=$val['day']?>
				</span>
			</div>
		</div>
	<? endforeach?>
	
	<?=$data['result']['pagination'] ?? ''?>
<? else:?>
	<span class="g-grey">
		<p>
			Нет новостей
		</p>
	</span>
<? endif?>