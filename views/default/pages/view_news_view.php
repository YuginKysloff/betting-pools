<? defined('SW_CONSTANT') or die?>

<h1 class="g-h1">
	<?=$data['result']['title']?>
</h1>

<? if(isset($data['result'])):?>
	<time class="news__time">
		<?=$data['result']['datetime']?>
	</time>
	<div class="news__post-full">
		<?=$data['result']['text']?>
	</div>
<? endif?>

<span class="g-grey" id="news_view__all">
	Все новости
</span>