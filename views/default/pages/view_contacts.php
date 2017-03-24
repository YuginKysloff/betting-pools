<? defined('SW_CONSTANT') or die?>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/head.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-header.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/content-menu.php')?>

<main class="contact">
    <h1 class="g-h1">
        Контакты
    </h1>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consectetur accusantium corporis laudantium delectus eligendi optio, adipisci ab saepe consequatur unde ratione fugiat, eveniet voluptate? Repellat et debitis velit impedit consectetur.Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consectetur accusantium corporis laudantium delectus eligendi optio, adipisci ab saepe consequatur unde ratione fugiat, eveniet voluptate? Repellat et debitis velit impedit consectetur.Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consectetur accusantium corporis laudantium delectus eligendi optio, adipisci ab saepe consequatur unde ratione fugiat, eveniet voluptate? Repellat et debitis velit impedit consectetur.
    </p>
    <form id="contacts__form" onsubmit="return false;" class="contact__form">
        <textarea name="message" cols="30" rows="10" class="g-input-text-nojs g-input-text-nojs--textarea" placeholder="Текст сообщения"></textarea>
        <input type="text" name="name" value="<?=$data['user']['login']?>" class="g-input-text-nojs g-input-text-nojs--contact" maxlength="50" placeholder="Имя">
        <input type="text" name="email" value="<?=$data['user']['email']?>" class="g-input-text-nojs g-input-text-nojs--contact" maxlength="100" placeholder="E-mail">
        <div id='contacts__form_error' class="g-red"></div>
        <button type="submit" class="g-button auth__button" id="contacts__send_button">
            Отправить
        </button>
    </form>
</main>

<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/footer.php')?>
<? require_once(DIR_ROOT.'/views/'.$template.'/pages/layouts/script.php')?>