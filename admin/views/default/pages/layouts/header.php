<header class="header">
	<a href="" class="logo"><img src="/admin/views/default/img/logo.png" alt="" class="logo__img"></a>
	<div class="header__right-bar">
		<span class="right-bar__user"><?=$data['user']['login'];?></span>
		<span class="right-bar__url"><a href="/" target="_blank" class="right-bar__link"><?=$_SERVER['HTTP_HOST'];?></a></span>
		<span class="right-bar__exit g-url" id="header__logout">Выйти</span>
	</div>
</header>