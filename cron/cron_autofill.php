<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

// Подключение моделей
require_once(DIR_ROOT.'/core/core_settings.php');
require_once(DIR_ROOT.'/models/model_fill.php');
require_once(DIR_ROOT.'/models/model_users.php');
require_once(DIR_ROOT.'/models/model_white.php');

// Определение объектов
$obj['core_settings'] = new Core_Settings();
$obj['model_fill'] = new Model_Fill();
$obj['model_users'] = new Model_Users();
$obj['model_white'] = new Model_White();

// Установка максимального времени выполнения скрипта (в секундах)
$max_execution_time = ini_get('max_execution_time') ?? 60;
$max_execution_time -= 10;

// Проверка включенности модуля и времени последней записи
if(!$data['config']['automatization']['autofill']['enabled'] || $data['config']['automatization']['autofill']['last'] > time()) return;

// Обновление времени
$category = $obj['core_settings']->obj['model_settings_category']->check_key(['name' => 'autofill'])[0]['id'];
$setting = $obj['core_settings']->obj['model_settings']->get_id_by_name(['name' => 'last', 'category' => $category]);
$last = strtotime(date("Y-m-d H:i")) + 60 + ((mt_rand($data['config']['automatization']['autofill']['min'], $data['config']['automatization']['autofill']['max'])) * 60);
$obj['core_settings']->obj['model_settings']->update(['set' => ['value' => $last], 'where' => $setting]);

// Обновление файла настроек
file_put_contents(DIR_ROOT.'/json/config.json', json_encode($obj['core_settings']->get_tree()), LOCK_EX);

// Создание файл-лога
$path['log'] = DIR_ROOT.'/admin/txt/autofill-log.txt';
if(!file_exists($path['log'])) {
	file_put_contents($path['log'], '', LOCK_EX);
	return;
}

// Выбор валюты
foreach($this->data['config']['valutes'] as $key => $val) {
	if(!$val['fill']) continue;
	if(!isset($this->data['config']['automatization']['autofill'][$key]) || !$this->data['config']['automatization']['autofill'][$key]) continue;
	if(!isset($num)) $num = 0;
	$num += $this->data['config']['automatization']['autofill'][$key];
	$param[$key] = $num;
}
if(!isset($param)) {
	file_put_contents($path['log'], time().'::Валюты не корректно настроены или отключены'.PHP_EOL.file_get_contents($path['log']), LOCK_EX);
	return;
}
$rand = mt_rand(1, $num);
foreach($param as $key => $val) {
	if($val >= $rand) {
		$valute = $key;
		break;
	}
}
unset($num);
unset($param);

// Выбор платежной системы
foreach($this->data['config']['payments'] as $key => $val) {
	if(!$val['enabled']) continue;
	if(!isset($this->data['config']['automatization']['autofill'][$key]) || !$this->data['config']['automatization']['autofill'][$key] || !isset($this->data['config']['payments'][$key][$valute]) || !$this->data['config']['payments'][$key][$valute]) continue;
	if(!isset($num)) $num = 0;
	$num += $this->data['config']['automatization']['autofill'][$key];
	$param[$key] = $num;
}
if(!isset($param)) {
	file_put_contents($path['log'], time().'::Платежные системы не корректно настроены или отключены'.PHP_EOL.file_get_contents($path['log']), LOCK_EX);
	return;
}
$rand = mt_rand(1, $num);
foreach($param as $key => $val) {
	if($val >= $rand) {
		$payment = $key;
		break;
	}
}
unset($num);
unset($param);

// Выбор категории
if($this->data['config']['automatization']['autofill']['category1']) $param[1] = $this->data['config']['automatization']['autofill']['category1'];
if($this->data['config']['automatization']['autofill']['category2']) $param[2] = $this->data['config']['automatization']['autofill']['category2'];
if($this->data['config']['automatization']['autofill']['category3']) $param[3] = $this->data['config']['automatization']['autofill']['category3'];
if($this->data['config']['automatization']['autofill']['category4']) $param[4] = $this->data['config']['automatization']['autofill']['category4'];
if(!isset($param)) {
	file_put_contents($path['log'], time().'::Категории не корректно настроены'.PHP_EOL.file_get_contents($path['log']), LOCK_EX);
	return;
}
foreach($param as $key => $val) {
	if(!isset($num)) $num = 0;
	$num += $this->data['config']['automatization']['autofill']['category'.$key];
	$param[$key] = $num;
}
$rand = mt_rand(1, $num);
foreach($param as $key => $val) {
	if($val >= $rand) {
		$category = $key;
		break;
	}
}
unset($num);
unset($param);

// Выбор значения категории
$path['category-list'] = DIR_ROOT.'/admin/txt/autofill-category-'.$category.'.txt';
if(!file_exists($path['category-list'])) {
	file_put_contents($path['log'], time().'::Список категории '.$category.' отсутствует'.PHP_EOL.file_get_contents($path['log']), LOCK_EX);
	return;
}
$file = file($path['category-list']);
$count = count($file);
if($count < 1) {
	file_put_contents($path['log'], time().'::Список категории '.$category.' пуст'.PHP_EOL.file_get_contents($path['log']), LOCK_EX);
	return;
}
foreach($file as $val) {
	$value = @explode('::', $val);
	$amount = Lib_Main::clear_num($val[0], 0);
	$sum = Lib_Main::clear_num($val[1], 0);
	if(!$value || !$amount || !$sum) continue;
	if(!isset($num)) $num = 0;
	$num += $sum;
	$param[$amount] = $num;
}
if(!isset($param)) {
	file_put_contents($path['log'], time().'::Ошибка формирования массива при обработке списка категории'.PHP_EOL.file_get_contents($path['log']), LOCK_EX);
	return;
}
$rand = mt_rand(1, $num);
foreach($param as $key => $val) {
	if($val >= $rand) {
		$amount = $key;
		break;
	}
}
unset($num);
unset($param);

// Выбор пользователя
$count = $obj['model_white']->cnt();
if(!$count) {
	file_put_contents($path['log'], time().'::В белом списке нет пользователей'.PHP_EOL.file_get_contents($path['log']), LOCK_EX);
	return;
}
$user = $obj['model_white']->get(['start_row' => mt_rand(0, $count - 1), 'per_page' => 1]);

// Добавление пополнения
$fill_id = $obj['model_fill']->insert(['user_id' => $user['id'], 'valute' => $valute, 'amount' => $amount, 'payment' => $payment, 'status' => 1, 'sort' => time(), 'wl' => 1, 'datetime' => time()]);
file_put_contents($path['log'], time().'::Успешное пополнение #'.$fill_id.' : '.$user['login'].' (#'.$user['id'].') '.$amount.' '.strtoupper($valute).' ('.$this->data['config']['payments'][$payment]['name'].')'.PHP_EOL.file_get_contents($path['log']), LOCK_EX);