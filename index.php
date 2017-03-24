<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

// Определение минимальных требований PHP
define('SFERA_MINIMUM_PHP', '7.0.0');

if(version_compare(PHP_VERSION, SFERA_MINIMUM_PHP, '<')) 
	die('Your host needs to use PHP ' . SFERA_MINIMUM_PHP . ' or higher to run this version of SFERA!');

// Определение константы для запрета прямого доступа к файлам
define('SW_CONSTANT', 1);

// Сохранение времени запуска и использования памяти
define('START_TIME', microtime(1));
define('START_MEM', memory_get_usage());

// Путь к корневой директории
define('DIR_ROOT', $_SERVER['DOCUMENT_ROOT']);

// Подключение главной библиотеки
require_once(DIR_ROOT.'/lib/lib_main.php'); 

// Подключение файла конфигурации
require_once(DIR_ROOT.'/config.php');

// Инициализация экземпляра класса конфигурации
Config::instance();

// Подключение файла бд
require_once(DIR_ROOT.'/lib/lib_db.php');

// Подключение языкового файла
require_once(DIR_ROOT.'/lib/lib_lang.php');

// Инициализация экземпляра класса языковой локализации
Lib_Lang::instance();

// Подключение файла валидации
require_once(DIR_ROOT.'/lib/lib_validation.php');

// Подключение ядра
require_once(DIR_ROOT.'/core/core_controller.php');
require_once(DIR_ROOT.'/core/core_model.php');
require_once(DIR_ROOT.'/core/core_route.php');
require_once(DIR_ROOT.'/core/core_security.php');
require_once(DIR_ROOT.'/core/core_view.php');

// Роутинг
Core_Route::init();