<? defined('SW_CONSTANT') or die;

foreach($data as $key => $val):

	if(is_array($val)):
		foreach($val as $error):?>
			<span class="g-<?=$key;?>">
				<?=$error;?>
			</span><br>
		<?  endforeach;
	else:?>
		<span class="g-<?=$key;?>">
			<?=$val;?>
		</span>
	<?  endif;

endforeach;