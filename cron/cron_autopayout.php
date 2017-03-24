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
require_once(DIR_ROOT.'/models/model_payout.php');
require_once(DIR_ROOT.'/models/model_users.php');
require_once(DIR_ROOT.'/models/model_white.php');

// Определение объектов
$obj['core_settings'] = new Core_Settings();
$obj['model_fill'] = new Model_Fill();
$obj['model_payout'] = new Model_Payout();
$obj['model_users'] = new Model_Users();
$obj['model_white'] = new Model_White();

// Установка максимального времени выполнения скрипта (в секундах)
$max_execution_time = ini_get('max_execution_time') ?? 60;
$max_execution_time -= 10;

// Проверка включенности модуля и времени последней записи
if(!$data['config']['automatization']['autopayout']['enabled'] || $data['config']['automatization']['autopayout']['last'] > time()) return;

// Обновление времени
$category = $obj['core_settings']->obj['model_settings_category']->check_key(['name' => 'autopayout'])[0]['id'];
$setting = $obj['core_settings']->obj['model_settings']->get_id_by_name(['name' => 'last', 'category' => $category]);
$last = strtotime(date("Y-m-d H:i")) + 60 + ((mt_rand($data['config']['automatization']['autopayout']['min'], $data['config']['automatization']['autopayout']['max'])) * 60);
$obj['core_settings']->obj['model_settings']->update(['set' => ['value' => $last], 'where' => $setting]);

// Обновление файла настроек
file_put_contents(DIR_ROOT.'/json/config.json', json_encode($obj['core_settings']->get_tree()), LOCK_EX);

// Создание файл-лога
$path['log'] = DIR_ROOT.'/admin/txt/autopayout-log.txt';
if(!file_exists($path['log'])) {
	file_put_contents($path['log'], '', LOCK_EX);
	return;
}

// Выбор процента
if(!isset($data['config']['automatization']['autopayout']['percent_min']) || !$data['config']['automatization']['autopayout']['percent_min'] || !isset($data['config']['automatization']['autopayout']['percent_max']) || !$data['config']['automatization']['autopayout']['percent_max']) {
	file_put_contents($path['log'], time().'::Проценты не корректно настроены или отключены'.PHP_EOL.file_get_contents($path['log']), LOCK_EX);
	return;
}
$percent = mt_rand($data['config']['automatization']['autopayout']['percent_min'], $data['config']['automatization']['autopayout']['percent_max']);
if(!$percent) {
	file_put_contents($path['log'], time().'::Ошибка при фоормировании процентов'.PHP_EOL.file_get_contents($path['log']), LOCK_EX);
	return;
}

// Выбор пользователя
$count = $obj['model_white']->active_cnt();
if(!$count) {
	file_put_contents($path['log'], time().'::В белом списке нет активных пользователей'.PHP_EOL.file_get_contents($path['log']), LOCK_EX);
	return;
}
$user_id = $obj['model_white']->get_active_rand(['rand' => mt_rand(0, $count - 1)]);
$fill = $obj['model_fill']->user_all(['user_id' => $user_id]);
if(!$fill) {
	file_put_contents($path['log'], time().'::Ошибка при формировании списка пополнений'.PHP_EOL.file_get_contents($path['log']), LOCK_EX);
	return;
}
$payout = $fill[mt_rand(0, count($fill) - 1)];
$amount = Lib_Main::clear_num($payout['amount'] * ($percent / 100));
$user = $obj['model_users']->get_by_id(['id' => $user_id]);

// Добавление выплаты
$payout_id = $obj['model_payout']->insert(['user_id' => $user['id'], 'valute' => $payout['valute'], 'amount' => $amount, 'payment' => $payout['payment'], 'status' => 1, 'reason' => 0, 'sort' => time(), 'wl' => 1, 'datetime' => time()]);
file_put_contents($path['log'], time().'::Успешная выплата #'.$fill_id.' : '.$user['login'].' (#'.$user['id'].') '.$amount.' '.strtoupper($payout['valute']).' ('.$this->data['config']['payments'][$payout['payment']]['name'].')'.PHP_EOL.file_get_contents($path['log']), LOCK_EX);