<? defined('SW_CONSTANT') or die; ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/head.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/system-block.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/header.php'); ?>
 
<div class="content view__index">
	<div class="desctop">
		<a href="/<?=$data['config']['site']['admin'];?>/statistics" class="desctop__item">
			<div class="desctop__circle">
				<i class="fa fa-list desctop__icon" aria-hidden="true"></i>
				<span class="desctop__title">Статистика</span>
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 300 300" preserveAspectRatio="none" >
					<circle class="green-halo" fill="none" transform="rotate(-90,100,100)" />
				</svg>
			</div>
		</a>
		<a href="/<?=$data['config']['site']['admin'];?>/payout" class="desctop__item">
			<div class="desctop__circle">
				<i class="fa fa-money desctop__icon" aria-hidden="true"></i>
				<span class="desctop__title">Выплаты</span>
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 300 300" preserveAspectRatio="none">
					<circle class="green-halo" fill="none" transform="rotate(-90,100,100)" />
				</svg>
			</div>
		</a>
		<a href="/<?=$data['config']['site']['admin'];?>/users" class="desctop__item">
			<div class="desctop__circle">
				<i class="fa fa-user desctop__icon" aria-hidden="true"></i>
				<span class="desctop__title">Пользователи</span>
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 300 300" preserveAspectRatio="none">
					<circle class="green-halo" fill="none" transform="rotate(-90,100,100)" />
				</svg>
			</div>
		</a>
		<a href="/<?=$data['config']['site']['admin'];?>/log" class="desctop__item">
			<div class="desctop__circle">
				<i class="fa fa-list-alt desctop__icon" aria-hidden="true"></i>
				<span class="desctop__title">Лог событий</span>
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 300 300" preserveAspectRatio="none">
					<circle class="green-halo" fill="none" transform="rotate(-90,100,100)" />
				</svg>
			</div>
		</a>
		<a href="/<?=$data['config']['site']['admin'];?>/white" class="desctop__item">
			<div class="desctop__circle">
				<i class="fa fa-file-o desctop__icon" aria-hidden="true"></i>
				<span class="desctop__title">Белый список</span>
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 300 300" preserveAspectRatio="none">
					<circle class="green-halo" fill="none" transform="rotate(-90,100,100)" />
				</svg>
			</div>
		</a>
		<a href="/<?=$data['config']['site']['admin'];?>/black" class="desctop__item">
			<div class="desctop__circle">
				<i class="fa fa-file desctop__icon" aria-hidden="true"></i>
				<span class="desctop__title">Черный список</span>
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 300 300" preserveAspectRatio="none">
					<circle class="green-halo" fill="none" transform="rotate(-90,100,100)" />
				</svg>
			</div>
		</a>
		<a href="/<?=$data['config']['site']['admin'];?>/multi" class="desctop__item">
			<div class="desctop__circle">
				<i class="fa fa-cubes  desctop__icon" aria-hidden="true"></i>
				<span class="desctop__title">Мультиаккаунты</span>
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 300 300" preserveAspectRatio="none">
					<circle class="green-halo" fill="none" transform="rotate(-90,100,100)" />
				</svg>
			</div>
		</a>
		<a href="/<?=$data['config']['site']['admin'];?>/news" class="desctop__item">
			<div class="desctop__circle">
				<i class="fa fa-newspaper-o  desctop__icon" aria-hidden="true"></i>
				<span class="desctop__title">Новости</span>
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 300 300" preserveAspectRatio="none">
					<circle class="green-halo" fill="none" transform="rotate(-90,100,100)" />
				</svg>
			</div>
		</a>
		<a href="/<?=$data['config']['site']['admin'];?>/security" class="desctop__item">
			<div class="desctop__circle">
				<i class="fa fa-lock  desctop__icon" aria-hidden="true"></i>
				<span class="desctop__title">Безопасность</span>
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 300 300" preserveAspectRatio="none">
					<circle class="green-halo" fill="none" transform="rotate(-90,100,100)" />
				</svg>
			</div>
		</a>
		<a href="/<?=$data['config']['site']['admin'];?>/settings" class="desctop__item">
			<div class="desctop__circle">
				<i class="fa fa-cogs  desctop__icon" aria-hidden="true"></i>
				<span class="desctop__title">Настройки</span>
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 300 300" preserveAspectRatio="none">
					<circle class="green-halo" fill="none" transform="rotate(-90,100,100)" />
				</svg>
			</div>
		</a>
	</div>
</div>

<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/footer.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/script.php'); ?>