<? defined('SW_CONSTANT') or die?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>
        <?=$data['meta_title'];?>
    </title>
    <meta name="description" content="<?=$data['meta_description'];?>">
    <meta name="keywords" content="<?=$data['meta_keywords'];?>">
    <!-- Фавиконки -->
    <link rel="apple-touch-icon" sizes="180x180" href="/views/<?=$template;?>/img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="/views/<?=$template;?>/img/favicons/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="/views/<?=$template;?>/img/favicons/favicon-194x194.png" sizes="194x194">
    <link rel="icon" type="image/png" href="/views/<?=$template;?>/img/favicons/android-chrome-192x192.png" sizes="192x192">
    <link rel="icon" type="image/png" href="/views/<?=$template;?>/img/favicons/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="/views/<?=$template;?>/img/favicons/manifest.json">
    <link rel="mask-icon" href="/views/<?=$template;?>/img/favicons/safari-pinned-tab.svg">
    <link rel="shortcut icon" href="/views/<?=$template;?>/img/favicons/favicon.ico">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-TileImage" content="/views/<?=$template;?>/img/favicons/mstile-144x144.png">
    <meta name="msapplication-config" content="/views/<?=$template;?>/img/favicons/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
    <!-- конец Фавиконки -->
</head>
<body>
    <div class="preloader" style="position: fixed;width:100%;height:100%;background:#fff;z-index:99999;padding-top:20px;">
        Загрузка..
    </div>
    <div id="handler"></div>