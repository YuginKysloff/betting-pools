<? defined('SW_CONSTANT') or die?>
<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
		<?=$data['lang']['mails--change-password']['desc']?> <?=$data['config']['site']['name']?> : <?=$data['config']['site']['domain']?>
		<p>
			<b><?=$data['lang']['mails--change-password']['new-password']?>:</b> <?=$data['password']?>
		</p>
		<p>
			<?=$data['lang']['mails--change-password']['technical-sup']?>: <a href="mailto:<?=$data['config']['site']['support']?>"><?=$data['config']['site']['support']?></a>
		</p>
	</body>
</html>