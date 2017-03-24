<? defined('SW_CONSTANT') or die?>

<header class="content-header">
    <a href="/">
        <img src="/views/<?=$template;?>/img/logo-end-text.png" alt="" class="logo-text">
    </a>
    <nav class="base-menu">
        <a href="/" class="base-menu__link">
            Главная
        </a>
        <a href="/news" class="base-menu__link">
            Новости
        </a>
        <a href="/marketing" class="base-menu__link">
            Маркетинг
        </a>
        <a href="/faq" class="base-menu__link">
            FAQ
        </a>
        <a href="/rules" class="base-menu__link">
            Правила
        </a>
        <a href="/recomend" class="base-menu__link">
            Наc рекомендуют
        </a>
        <a href="/contacts" class="base-menu__link">
            Контакты
        </a>
        <div class="user-menu">
            <span class="user-menu__icon"></span>
            <div class="user-menu__sub-menu">
                <span class="user-menu__item user-menu__item--ru"></span>
                <span class="user-menu__item user-menu__item--en"></span>
                <span class="user-menu__item user-menu__item--red"></span>
                
                <? if(!$data['user']):?>
                        <a href="/login" class="base-menu__link">
                            <span class="user-menu__item user-menu__item--signup"></span>
                        </a>
                        <a href="/signup" class="base-menu__link">
                            <span class="user-menu__item user-menu__item--add-user"></span>
                        </a>
                <? endif?>
                
            </div>
        </div>
    </nav>
</header>