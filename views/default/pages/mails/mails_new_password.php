<?php
// Запрет на прямой доступ к файлу
defined('SW_CONSTANT') or die; 
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
	<p>
		<?=$data['lang']['mails--new-password']['pass-mess-1'];?>  : <?=$data['password'];?>
	</p>
	<p>
		<?=$data['lang']['mails--new-password']['pass-mess-3'];?>
	</p>
	<hr>
	<p>
		<?=$data['lang']['mails--new-password']['pass-mess-2'];?>
	</p>
</body>
</html>