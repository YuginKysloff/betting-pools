<? defined('SW_CONSTANT') or die?>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/head.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-header.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-menu.php')?>

	<main class="referals">
		<h1 class="g-h1">
			Рефералы
		</h1>
		<div>
			<span class="referals__you-link">
				Ваша реферальная ссылка:
			</span>
			<span class="referals__url">
				<?=$data['config']['site']['protocol'].'://'.$data['config']['site']['domain'].'/p='.substr($data['user']['link'], 0, 1).$data['user']['id'].substr($data['user']['link'], -1)?>
			</span>
			<span class="referals__advertising-material">
				Рекламные материалы
			</span>
		</div>
		<div class="referals__info-block">
			<div class="referals__transition">
				Переходов по ссылке:
				<span class="g-darkred">
					<?=$data['user']['visit'] ?? '0'?>
				</span>
				<?  if($data['user']['visit'] != '0'):?>
					<span class="referals__reset-url" id="refsys__reset_button">
						обнулить
					</span>
				<?	endif?>
			</div>
			<div class="referals__procent">
				Реферальный процент: 
				<span class="g-darkred">
					<?=$data['user']['rcb']?>%
				</span>
			</div>
			<div class="referals__referal">
				Рефералы: 
				<span class="g-darkred">
					<?=$data['result']['ref']['count']?>
				</span>
			</div>
			<div class="referals__income">
				Доход:
				<span class="g-darkred">
					<span class="g-currency-usd g-currency-usd--ref">
						<?=$data['result']['referral_income']['usd'] ?? 0?>
					</span>
				</span>
			</div>
		</div>
		<div id="refsys__list"></div>
	</main>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/footer.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/script.php')?>