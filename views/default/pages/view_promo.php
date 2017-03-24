<? defined('SW_CONSTANT') or die?>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/head.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-header.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-menu.php')?>

<main class="banners">
    <h1 class="g-h1">
        Рекламные материалы
    </h1>
    <span class="banners__title">
        728x90
    </span>
    <img src="/views/<?=$template?>/img/promo/728<?=$data['current_lang']?>.gif" alt="<?=$data['banners'][3]['link'] ?? '/'?>">
    <span class="banners__row">
        Ссылка на баннер:
        <span class="banners__url">
            <?=$data['config']['site']['protocol'].'://'.$data['config']['site']['domain'].'/'.$template?>/img/promo/728<?=$data['current_lang']?>.gif
        </span>
    </span>
    <span class="banners__row">
      Код для вставки:
      <span class="banners__code">
          &lt;a href="<?=$data['config']['site']['protocol'].'://'.$data['config']['site']['domain'].'/p='.substr($data['user']['link'], 0, 1).$data['user']['id'].substr($data['user']['link'], -1)?>" target="_blank"&gt;&lt;img src="<?=$data['config']['site']['protocol'].'://'.$data['config']['site']['domain'].'/'.$template?>/img/promo/728<?=$data['current_lang']?>.gif"&gt;&lt;/a&gt;
      </span>
    </span>
    <span class="banners__title">
        468x60
    </span>
    <img src="/views/<?=$template?>/img/promo/468<?=$data['current_lang']?>.gif" alt="<?=$data['lang']['dashboard']['banner']?>">
    <span class="banners__row">
        Ссылка на баннер:
        <span class="banners__url">
            <?=$data['config']['site']['protocol'].'://'.$data['config']['site']['domain'].'/'.$template?>/img/promo/468<?=$data['current_lang']?>.gif
        </span>
    </span>
    <span class="banners__row">
        Код для вставки:
        <span class="banners__code">
            &lt;a href="<?=$data['config']['site']['protocol'].'://'.$data['config']['site']['domain'].'/p='.substr($data['user']['link'], 0, 1).$data['user']['id'].substr($data['user']['link'], -1)?>" target="_blank"&gt;&lt;img src="<?=$data['config']['site']['protocol'].'://'.$data['config']['site']['domain'].'/'.$template?>/img/promo/468<?=$data['current_lang']?>.gif"&gt;&lt;/a&gt;
        </span>
    </span>
    <span class="banners__title">
        200x300
    </span>
    <img src="/views/<?=$template?>/img/promo/200<?=$data['current_lang']?>.gif" alt="<?=$data['lang']['dashboard']['banner']?>">
    <span class="banners__row">
        Ссылка на баннер:
        <span class="banners__url">
            <?=$data['config']['site']['protocol'].'://'.$data['config']['site']['domain'].'/'.$template?>/img/promo/200<?=$data['current_lang']?>.gif
        </span>
    </span>
    <span class="banners__row">
        Код для вставки:
        <span class="banners__code">
            &lt;a href="<?=$data['config']['site']['protocol'].'://'.$data['config']['site']['domain'].'/p='.substr($data['user']['link'], 0, 1).$data['user']['id'].substr($data['user']['link'], -1)?>" target="_blank"&gt;&lt;img src="<?=$data['config']['site']['protocol'].'://'.$data['config']['site']['domain'].'/'.$template?>/img/promo/200<?=$data['current_lang']?>.gif"&gt;&lt;/a&gt;
        </span>
    </span>
    <span class="banners__title">
        125x125
    </span>
    <img src="/views/<?=$template?>/img/promo/125<?=$data['current_lang']?>.gif" alt="<?=$data['lang']['dashboard']['banner']?>">
    <span class="banners__row">
        Ссылка на баннер:
        <span class="banners__url">
            <?=$data['config']['site']['protocol'].'://'.$data['config']['site']['domain'].'/'.$template?>/img/promo/125<?=$data['current_lang']?>.gif
        </span>
    </span>
    <span class="banners__row">
        Код для вставки:
        <span class="banners__code">
            &lt;a href="<?=$data['config']['site']['protocol'].'://'.$data['config']['site']['domain'].'/p='.substr($data['user']['link'], 0, 1).$data['user']['id'].substr($data['user']['link'], -1)?>" target="_blank"&gt;&lt;img src="<?=$data['config']['site']['protocol'].'://'.$data['config']['site']['domain'].'/'.$template?>/img/promo/125<?=$data['current_lang']?>.gif"&gt;&lt;/a&gt;
        </span>
    </span>
</main>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/footer.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/script.php')?>