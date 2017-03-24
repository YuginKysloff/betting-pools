<?php
// Запрет на прямой доступ к файлу
defined('SW_CONSTANT') or die; 
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
	<?=$data['lang']['mails--lost']['lost-mess-1'];?> <?=$data['config']['site']['name'];?>. <?=$data['lang']['mails--lost']['lost-mess-2'];?> : <?=$data['activate'];?>
	<p>
		<?=$data['lang']['mails--lost']['lost-mess-3'];?>
	</p>
</body>
</html>