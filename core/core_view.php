<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */
 
defined('SW_CONSTANT') or die;

class Core_View {
	
	// Возврат вида
	public static function load($file, $data = null) {
		if(isset($data['route']['admin'])) {
			$admin = '/admin';
			$template = $data['config']['site']['template_admin'];
		} else {
			$admin = '';
			$template = $data['config']['site']['template'];
		}
		
		$file = preg_match("/layouts/", $file) ? $file : 'view_'.$file;

		// Путь к файлу
		$path = DIR_ROOT.$admin.'/views/'.$template.'/pages/'.$file.'.php';

		// Проверка наличия файла
		if(!file_exists($path)) {
			return false;
		}
		
		// Результат в переменную
		ob_start();
		require($path);
		$result = ob_get_contents();
		ob_end_clean();
		
		$result = Lib_Main::view_page($result);
		return $result;
	}

	// Стилизация и возврат сообщений
	public static function message($data){
		
		// Путь к файлу
		$path = DIR_ROOT.'/views/'.Config::instance()->config['site']['template'].'/pages/layouts/show__message.php';
		
		// Проверка наличия файла
		if(!file_exists($path)) {
			return false;
		}
		
		// Результат в переменную
		ob_start();
		require($path);
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}
	
	// Вывод писем
	public static function load_mail($file, $data = null) {
		
		// Путь к файлу
		$path = DIR_ROOT.'/views/'.$data['config']['site']['template'].'/pages/mails/mails_'.$file.'.php';
		
		// Проверка наличия файла
		if(!file_exists($path)) {
			return false;
		}
		
		// Результат в переменную
		ob_start();
		require($path);
		$result = ob_get_contents();
		ob_end_clean();
		
		return $result;
	}
}