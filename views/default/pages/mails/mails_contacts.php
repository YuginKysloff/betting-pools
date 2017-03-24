<? defined('SW_CONSTANT') or die; ?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
	<h1><?=$data['subject'];?></h1>
	<p><?=$data['message'];?></p>
	<p>
		<b><?=$data['lang']['mails']['date'];?> :</b> <?=date($data['config']['formats']['admin_date']);?><br>
		<b>IP:</b> <?=$data['ip'];?><br>
		<b><?=$data['lang']['mails']['email'];?> :</b> <?=$data['email'];?><br>
	</p>
</body>
</html>