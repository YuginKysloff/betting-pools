<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */
 
defined('SW_CONSTANT') or die;

// Настройки БД
define('DB_HOST', '127.0.0.1'); // Хост
define('DB_NAME', 'betting-pools'); 		// Имя базы данных
define('DB_USER', 'root'); 			// Пользователь
define('DB_PASS', ''); 				// Пароль пользователя
define('DB_PREF', 'mtx_'); 			// Префикс таблиц

// Кодировка сайта
header('Content-type: text/html; Charset= UTF- 8');

// Запуск сессии
session_start();

// Уровень отображения ошибок
error_reporting(-1);

// Уровень отображения магических кавычек
ini_set('magic_quotes_runtime', 0);

// Установка временной зоны сервера
date_default_timezone_set('UTC');

// Время выполнения скрипта
set_time_limit(240);

// Подключение библиотеки обработки ошибок интерпретатора
require_once(DIR_ROOT.'/lib/lib_errors.php');

// Установка функции обработки ошибок
set_error_handler('errorHandler');
register_shutdown_function('shutdownHandler');

class Config {
	
	static private $_instance = null;

	private function __construct() {
		
		// Парсинг файла конфигураций
		if(!@file_get_contents(DIR_ROOT.'/json/config.json')) {
			
			// Подключение файла бд
			require_once(DIR_ROOT.'/lib/lib_db.php');
			
			// Подключение моделей
			require_once(DIR_ROOT.'/core/core_model.php');
			
			// Подключение ядра настроек
			require_once(DIR_ROOT.'/core/core_settings.php');
			
			$this->obj['core_settings'] = new Core_Settings();
			
			// Обновление файла настроек
			file_put_contents(DIR_ROOT.'/json/config.json', json_encode($this->obj['core_settings']->get_tree()), LOCK_EX);
		}
		$this->config = json_decode(file_get_contents(DIR_ROOT.'/json/config.json'), true);
	}

	private function __clone() {}
	
	private function __wakeup() {}

	static function instance() {
		if(self::$_instance == null) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
}