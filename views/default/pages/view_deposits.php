<?  defined('SW_CONSTANT') or die?>

<?  require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/head.php')?>
<?  require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-header.php')?>
<?  require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-menu.php')?>

<main class="deposit">
	<h1 class="g-h1">
		Депозиты
	</h1>
	<div class="deposit__all">
		<div class="deposit__all-row">
			Сумма депозитов:
			<span class="g-currency-usd">
				<?=$data['result']['deposits_summary']['amount']?>
			</span>
		</div>
		<div class="deposit__all-row">
			Активы:
			<span class="g-currency-usd">
				<?=$data['result']['deposits_summary']['rest']?>
			</span>
		</div>
		<div class="deposit__all-row">
			Начисленная прибыль:
			<span class="g-currency-usd">
				<?=$data['result']['deposits_summary']['accrued']?>
			</span>
		</div>
	</div>
	<div id="deposits__list"></div>
</main>

<?  require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/footer.php')?>
<?  require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/script.php')?>