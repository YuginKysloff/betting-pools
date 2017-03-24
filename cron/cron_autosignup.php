<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

// Подключение моделей
require_once(DIR_ROOT.'/core/core_settings.php');
require_once(DIR_ROOT.'/models/model_users.php');
require_once(DIR_ROOT.'/models/model_white.php');

// Определение объектов
$obj['core_settings'] = new Core_Settings();
$obj['model_users'] = new Model_Users();
$obj['model_white'] = new Model_White();

// Установка максимального времени выполнения скрипта (в секундах)
$max_execution_time = ini_get('max_execution_time') ?? 60;
$max_execution_time -= 10;


// Проверка доступности регистраций
if(!$data['config']['site']['signup']) return;

// Проверка включенности модуля и времени последней записи
if(!$data['config']['automatization']['autosignup']['enabled'] || $data['config']['automatization']['autosignup']['last'] > time()) return;

// Проверка существовая файла
$path = DIR_ROOT.'/admin/txt/autosignup.txt';
if(!file_exists($path)) return;

// Формирование массива
$file = file($path);
if(!is_array($file)) return;

// Обновление времени
$category = $obj['core_settings']->obj['model_settings_category']->check_key(['name' => 'autosignup'])[0]['id'];
$setting = $obj['core_settings']->obj['model_settings']->get_id_by_name(['name' => 'last', 'category' => $category]);
$last = strtotime(date("Y-m-d H:i")) + 60 + ((mt_rand($data['config']['automatization']['autosignup']['min'], $data['config']['automatization']['autosignup']['max'])) * 60);
$obj['core_settings']->obj['model_settings']->update(['set' => ['value' => $last], 'where' => $setting]);

// Перебор в цикле и проверка на соответствие всех данных
for($i = 0; $i < sizeof($file); $i++) {
	
	if(time() > ($start_time + $max_execution_time)) break;
	
	// Формируем массив
	$line = @explode('::', $file);
	
	// Удаляем строку
	unset($file[$i]);
	
	// Проверка ошибки массива
	if(!$line) continue;
	
	// Формирование списка
	@list($login, $email) = $line;
	if(!$login || !$email) continue;
	
	// Валмдация логина
	$err = Lib_Validation::login($login);
	if($login != $err) continue;
	
	// Валидация E-mail
	$err = Lib_Validation::email($email);
	if($email != $err) continue;
	
	// Проверка логина на занятость
	if($obj['model_users']->id_by_login(['login' => $login])) continue;
					
	// Проверка E-mail на занятость
	if($obj['model_users']->id_by_email(['email' => $email])) continue;
	
	// Кодирование пароля
	$password = hash('sha512', $data['config']['automatization']['autosignup']['password']);
	$password = strrev($password);
	$password .= $this->data['config']['site']['pass_key'];
	
	// Определение спонсора
	$sponsor_id = 0;
	if(isset($data['config']['automatization']['autosignup']['sponsor'])) {
		if(is_array($data['config']['automatization']['autosignup']['sponsor'])) {
			$sponsor = $data['config']['automatization']['autosignup']['sponsor'][mt_rand(0, count($data['config']['automatization']['autosignup']['sponsor']) - 1)];
		} else {
			$sponsor = $data['config']['automatization']['autosignup']['sponsor'];
		}
		$sponsor_id = $obj['model_users']->id_by_login(['login' => $sponsor]) ?? 0;
	}
	
	// Регистрация пользователя
	$user_id = $obj['model_users']->insert(['login' => $login, 'password' => $password, 'master_key' => Lib_Main::generate_num(3), 'email' => $email, 'sponsor_id' => $sponsor_id, 'birthday' => (time() - 746496000), 'gender' => mt_rand(1, 2), 'country' => 643, 'rcb' => $data['config']['marketing']['rcb'], 'instant' => 2, 'link' => Lib_Main::generate_num(2), 'url' => '', 'ip' => Lib_Main::get_ip(), 'last_ip' => Lib_Main::get_ip(), 'timezone' => 0, 'lang' => 'ru', 'activity' => time(), 'datetime' => time()]);
	
	// Добавление в белый список
	$obj['model_white']->insert(['user_id' => $user_id]);
}

// Отключение модуля
if(sizeof($file) < 1) {
	$category = $obj['core_settings']->obj['model_settings_category']->check_key(['name' => 'autosignup'])[0]['id'];
	$setting = $obj['core_settings']->obj['model_settings']->get_id_by_name(['name' => 'enabled', 'category' => $category]);
	$obj['core_settings']->obj['model_settings']->update(['set' => ['value' => 0], 'where' => $setting]);
	
	unset($path);
} else {
	
	// Перезапись файла
	file_put_contents($path, implode("", $file), LOCK_EX);
}

// Обновление файла настроек
file_put_contents(DIR_ROOT.'/json/config.json', json_encode($obj['core_settings']->get_tree()), LOCK_EX);