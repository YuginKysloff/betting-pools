<? defined('SW_CONSTANT') or die; ?>
	<link rel="stylesheet" href="/admin/views/<?=$template;?>/css/style.css">
	<script>
		var admin_path = '<?=$data['config']['site']['admin'];?>';
		var stime = <?=time();?>;
	</script>
	<script src="/admin/views/<?=$template;?>/js/script.js"></script>
	<script src="/admin/views/<?=$template;?>/js/user_script.js"></script>
</body>
</html>