<? defined('SW_CONSTANT') or die; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title><?=$data['config']['site']['name'];?></title>
    <!-- Фавиконки -->
	<link rel="apple-touch-icon" sizes="180x180" href="/admin/views/<?=$template;?>/img/favicons/apple-touch-icon.png">
	<link rel="icon" type="image/png" href="/admin/views/<?=$template;?>/img/favicons/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/admin/views/<?=$template;?>/img/favicons/favicon-194x194.png" sizes="194x194">
	<link rel="icon" type="image/png" href="/admin/views/<?=$template;?>/img/favicons/android-chrome-192x192.png" sizes="192x192">
	<link rel="icon" type="image/png" href="/admin/views/<?=$template;?>/img/favicons/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="/admin/views/<?=$template;?>/img/favicons/manifest.json">
	<link rel="mask-icon" href="/admin/views/<?=$template;?>/img/favicons/safari-pinned-tab.svg">
	<link rel="shortcut icon" href="/admin/views/<?=$template;?>/img/favicons/favicon.ico">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="msapplication-TileImage" content="/admin/views/<?=$template;?>/img/favicons/mstile-144x144.png">
	<meta name="msapplication-config" content="/admin/views/<?=$template;?>/img/favicons/browserconfig.xml">
	<meta name="theme-color" content="#ffffff">
    <!-- конец Фавиконки -->
    <style>.preloader{position: fixed;width:100%;height:100%;background:#e2eaec;z-index:99999;padding-top:20px;}html,body{margin:0;background-color:#e2eaec;}.cssload-loader{position:relative;left:calc(50% - 31px);margin-top:-20px;top:calc(50% - 31px);width:62px;height:62px;border-radius:50%;-o-border-radius:50%;-ms-border-radius:50%;-webkit-border-radius:50%;-moz-border-radius:50%;perspective:780px}.cssload-inner{position:absolute;width:100%;height:100%;box-sizing:border-box;-o-box-sizing:border-box;-ms-box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;border-radius:50%;-o-border-radius:50%;-ms-border-radius:50%;-webkit-border-radius:50%;-moz-border-radius:50%}.cssload-inner.cssload-one{left:0;top:0;animation:cssload-rotate-one 1.15s linear infinite;-o-animation:cssload-rotate-one 1.15s linear infinite;-ms-animation:cssload-rotate-one 1.15s linear infinite;-webkit-animation:cssload-rotate-one 1.15s linear infinite;-moz-animation:cssload-rotate-one 1.15s linear infinite;border-bottom:3px solid #000}.cssload-inner.cssload-two{right:0;top:0;animation:cssload-rotate-two 1.15s linear infinite;-o-animation:cssload-rotate-two 1.15s linear infinite;-ms-animation:cssload-rotate-two 1.15s linear infinite;-webkit-animation:cssload-rotate-two 1.15s linear infinite;-moz-animation:cssload-rotate-two 1.15s linear infinite;border-right:3px solid #000}.cssload-inner.cssload-three{right:0;bottom:0;animation:cssload-rotate-three 1.15s linear infinite;-o-animation:cssload-rotate-three 1.15s linear infinite;-ms-animation:cssload-rotate-three 1.15s linear infinite;-webkit-animation:cssload-rotate-three 1.15s linear infinite;-moz-animation:cssload-rotate-three 1.15s linear infinite;border-top:3px solid #000}@keyframes cssload-rotate-one{0%{transform:rotateX(35deg) rotateY(-45deg) rotateZ(0deg)}100%{transform:rotateX(35deg) rotateY(-45deg) rotateZ(360deg)}}@-o-keyframes cssload-rotate-one{0%{-o-transform:rotateX(35deg) rotateY(-45deg) rotateZ(0deg)}100%{-o-transform:rotateX(35deg) rotateY(-45deg) rotateZ(360deg)}}@-ms-keyframes cssload-rotate-one{0%{-ms-transform:rotateX(35deg) rotateY(-45deg) rotateZ(0deg)}100%{-ms-transform:rotateX(35deg) rotateY(-45deg) rotateZ(360deg)}}@-webkit-keyframes cssload-rotate-one{0%{-webkit-transform:rotateX(35deg) rotateY(-45deg) rotateZ(0deg)}100%{-webkit-transform:rotateX(35deg) rotateY(-45deg) rotateZ(360deg)}}@-moz-keyframes cssload-rotate-one{0%{-moz-transform:rotateX(35deg) rotateY(-45deg) rotateZ(0deg)}100%{-moz-transform:rotateX(35deg) rotateY(-45deg) rotateZ(360deg)}}@keyframes cssload-rotate-two{0%{transform:rotateX(50deg) rotateY(10deg) rotateZ(0deg)}100%{transform:rotateX(50deg) rotateY(10deg) rotateZ(360deg)}}@-o-keyframes cssload-rotate-two{0%{-o-transform:rotateX(50deg) rotateY(10deg) rotateZ(0deg)}100%{-o-transform:rotateX(50deg) rotateY(10deg) rotateZ(360deg)}}@-ms-keyframes cssload-rotate-two{0%{-ms-transform:rotateX(50deg) rotateY(10deg) rotateZ(0deg)}100%{-ms-transform:rotateX(50deg) rotateY(10deg) rotateZ(360deg)}}@-webkit-keyframes cssload-rotate-two{0%{-webkit-transform:rotateX(50deg) rotateY(10deg) rotateZ(0deg)}100%{-webkit-transform:rotateX(50deg) rotateY(10deg) rotateZ(360deg)}}@-moz-keyframes cssload-rotate-two{0%{-moz-transform:rotateX(50deg) rotateY(10deg) rotateZ(0deg)}100%{-moz-transform:rotateX(50deg) rotateY(10deg) rotateZ(360deg)}}@keyframes cssload-rotate-three{0%{transform:rotateX(35deg) rotateY(55deg) rotateZ(0deg)}100%{transform:rotateX(35deg) rotateY(55deg) rotateZ(360deg)}}@-o-keyframes cssload-rotate-three{0%{-o-transform:rotateX(35deg) rotateY(55deg) rotateZ(0deg)}100%{-o-transform:rotateX(35deg) rotateY(55deg) rotateZ(360deg)}}@-ms-keyframes cssload-rotate-three{0%{-ms-transform:rotateX(35deg) rotateY(55deg) rotateZ(0deg)}100%{-ms-transform:rotateX(35deg) rotateY(55deg) rotateZ(360deg)}}@-webkit-keyframes cssload-rotate-three{0%{-webkit-transform:rotateX(35deg) rotateY(55deg) rotateZ(0deg)}100%{-webkit-transform:rotateX(35deg) rotateY(55deg) rotateZ(360deg)}}@-moz-keyframes cssload-rotate-three{0%{-moz-transform:rotateX(35deg) rotateY(55deg) rotateZ(0deg)}100%{-moz-transform:rotateX(35deg) rotateY(55deg) rotateZ(360deg)}}</style>
</head>
<body>