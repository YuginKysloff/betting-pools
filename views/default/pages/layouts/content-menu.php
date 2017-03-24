<nav class="content-nav">
    
<? if($data['user']['id']):?>
    <a href="/profile" class="content-nav__item">
        <div class="content-nav__circle">
            <div class="content-nav__icon content-nav__icon--profile"></div>
            <div class="content-nav__title">
                Мой профиль
            </div>
        </div>
    </a>
    <a href="/pools" class="content-nav__item">
        <div class="content-nav__circle">
            <div class="content-nav__icon content-nav__icon--pull-usd"></div>
            <div class="content-nav__title">
                Пулы
            </div>
        </div>
    </a>
    <a href="/deposits" class="content-nav__item">
        <div class="content-nav__circle">
            <div class="content-nav__icon content-nav__icon--deposit-usd"></div>
            <div class="content-nav__title">
                Депозиты
            </div>
        </div>
    </a>
    <a href="/fill" class="content-nav__item">
        <div class="content-nav__circle">
            <div class="content-nav__icon content-nav__icon--pay-in"></div>
            <div class="content-nav__title">
                Пополнить баланс
            </div>
        </div>
    </a>
    <a href="/withdrawals" class="content-nav__item">
        <div class="content-nav__circle">
            <div class="content-nav__icon content-nav__icon--pay-out"></div>
            <div class="content-nav__title">
                Вывести средства
            </div>
        </div>
    </a>
    <a href="/log" class="content-nav__item">
        <div class="content-nav__circle">
            <div class="content-nav__icon content-nav__icon--log"></div>
            <div class="content-nav__title">
                Лог событий
            </div>
        </div>
    </a>
    <a href="/referrals" class="content-nav__item">
        <div class="content-nav__circle">
            <div class="content-nav__icon content-nav__icon--referals"></div>
            <div class="content-nav__title">
                Рефералы
            </div>
        </div>
    </a>
    <a href="/promo" class="content-nav__item">
        <div class="content-nav__circle">
            <div class="content-nav__icon content-nav__icon--referals"></div>
            <div class="content-nav__title">
                Рекламные материалы
            </div>
        </div>
    </a>
    <span id="logout__button" class="content-nav__item">
        <div class="content-nav__circle">
            <div class="content-nav__icon content-nav__icon--signup"></div>
            <div class="content-nav__title">
                Выход
            </div>
        </div>
    </span>
<? endif;?>
    
</nav>