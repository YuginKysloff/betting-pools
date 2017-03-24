<?php
// Запрет на прямой доступ к файлу
defined('SW_CONSTANT') or die; 
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
	<h1><?=$data['lang']['mails--check-ip']['check-mess-1'];?> <?=$data['config']['site']['name'];?>!</h1>
	<p>
		<?=$data['lang']['mails--check-ip']['check-mess-2'];?> <strong><?=$data['pin_code'];?></strong>
	</p>
	<p>
		<?=$data['lang']['mails--check-ip']['check-mess-4'];?>
	</p>
	<hr>
	<p>
		<?=$data['lang']['mails--check-ip']['check-mess-3'];?>
	</p>
</body>
</html>