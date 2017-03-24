<? defined('SW_CONSTANT') or die; ?>

<div class="menu__wrapper">
	<nav class="menu">
		<a href="/<?=$data['config']['site']['admin'];?>/" class="menu__item menu__home"><i class="menu__icon"></i>Рабочий стол</a>
		<a href="/<?=$data['config']['site']['admin'];?>/statistics" class="menu__item menu__static"><i class="menu__icon"></i>Статистика</a>
		<a href="/<?=$data['config']['site']['admin'];?>/payout" class="menu__item menu__pay-out"><i class="menu__icon"></i>Выплаты</a>
		<a href="/<?=$data['config']['site']['admin'];?>/users" class="menu__item menu__user"><i class="menu__icon"></i>Пользователи</a>
		<a href="/<?=$data['config']['site']['admin'];?>/log" class="menu__item menu__log"><i class="menu__icon"></i>Лог событий</a>
		<a href="/<?=$data['config']['site']['admin'];?>/white" class="menu__item menu__whille-list"><i class="menu__icon"></i>Белый список</a>
		<a href="/<?=$data['config']['site']['admin'];?>/black" class="menu__item menu__black-list"><i class="menu__icon"></i>Черный список</a>
		<a href="/<?=$data['config']['site']['admin'];?>/multi" class="menu__item menu__multi"><i class="menu__icon"></i>Мульти аккаунты</a>
		<a href="/<?=$data['config']['site']['admin'];?>/news" class="menu__item menu__news"><i class="menu__icon"></i>Новости</a>
		<a href="/<?=$data['config']['site']['admin'];?>/security" class="menu__item menu__security"><i class="menu__icon"></i>Безопасность</a>
		<a href="/<?=$data['config']['site']['admin'];?>/settings" class="menu__item menu__setting"><i class="menu__icon"></i>Настройки</a>
	</nav>
</div>
<div class="arrow"></div>