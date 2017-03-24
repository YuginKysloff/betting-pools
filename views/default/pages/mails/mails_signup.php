<? defined('SW_CONSTANT') or die; ?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
	<?=$data['lang']['mails--signup']['congratulations'];?> <?=$data['config']['site']['name'];?>
	<p>
		<b>
			<?=$data['lang']['mails--signup']['your-login'];?>:
		</b>
		<?=$data['login'];?><br>
		<b>
			<?=$data['lang']['mails--signup']['your-pass'];?>:
		</b>
		<?=$data['pass'];?><br>
		<b>
			<?=$data['lang']['mails--signup']['site'];?>:
		</b>
		<a href="<?=$data['config']['site']['protocol'].'://'.$data['config']['site']['domain'];?>">
			<?=$data['config']['site']['protocol'].'://'.$data['config']['site']['domain'];?>
		</a>
	</p>
	<p>
		<?=$data['lang']['mails--signup']['technical-sup'];?>:
		<a href="mailto:<?=$data['config']['site']['support'];?>">
			<?=$data['config']['site']['support'];?>
		</a>
	</p>
</body>
</html>