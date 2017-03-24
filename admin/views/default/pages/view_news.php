<? defined('SW_CONSTANT') or die; ?>

<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/head.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/system-block.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/header.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/menu.php'); ?>

<div class="content view__news">
    <div id="news__wysiwyg" style="display:block;">
		<span class="g-button blue" id="news__add_button">Добавить</span>
	</div>
    <div class="editor__text-wrap" id="news__list"></div>

</div>
</div>

<script src="/plugins/ckeditor/ckeditor.js"></script>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/footer.php'); ?>
<? require_once(DIR_ROOT.'/admin/views/'.$template.'/pages/layouts/script.php'); ?>
