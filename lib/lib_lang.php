<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */
 
defined('SW_CONSTANT') or die;

class Lib_Lang {
	
	static private $_instance = null;

	private function __construct() {
		$this->config = Config::instance()->config;
	}

	private function __clone() {}
	
	private function __wakeup() {}

	static function instance() {
		if(self::$_instance == null) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	
	// Определение языка
	public static function detection($lang = null) {
		
		// Создание объекта класса
        $obj = self::$_instance;
		
		if(!isset($lang)) {
			if (isset($_COOKIE['lang'])) {
				$lang = $_COOKIE['lang'];
			} 
			else {
				$lang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : $obj->config['lang']['default'];
			}
		}
		if(is_array($obj->config['lang']['list'])) {
			if(!in_array($lang, $obj->config['lang']['list'])) {
				$lang = $obj->config['lang']['default'];
			}
		} else {
			if($lang != $obj->config['lang']['list']) {
				$lang = $obj->config['lang']['default'];
			}
		}
		if(!isset($_COOKIE['lang']) || $_COOKIE['lang'] !== $lang) {
			setcookie("lang", $lang, time() + 9999999, "/");
		}
		$obj->current_lang = $lang;
	}
	
	// Парсинг файла языковой локализации
	public static function get_parse($lang = null) {
		
		// Создание объекта класса
        $obj = self::$_instance;
		
		$lang = $lang ?? $obj->current_lang;

		if(!isset($obj->parse[$lang])) {
			$path = DIR_ROOT.'/lang/'.$lang.'.ini';
			if(!file_exists($path)) return self::get_parse($obj->config['lang']['default']);
			$obj->parse[$lang] = parse_ini_file($path, true);
		}
		return $obj->parse[$lang];
	}
	
	public static function get() {
		
		// Создание объекта класса
        $obj = self::$_instance;

		return $obj->current_lang ?? $obj->config['lang']['default'];
	}
	
	public static function set($lang) {		
		Lib_Lang::detection($lang);
		return Lib_Main::refresh();
	}
}