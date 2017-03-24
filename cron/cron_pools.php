<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

$start_time = time();
$max_execution_time = ini_get('max_execution_time') ?? 60;
$max_execution_time -= 10;

if(time() > $this->data['config']['marketing']['pool_next_create']) {

	// Подключение моделей
	require_once(DIR_ROOT.'/core/core_settings.php');
	require_once(DIR_ROOT.'/models/model_pools.php');
	require_once(DIR_ROOT.'/models/model_settings.php');

	// Определение объектов
	$this->obj['core_settings'] = new Core_Settings();
	$this->obj['model_pools'] = new Model_Pools();
	$this->obj['model_settings'] = new Model_Settings();

	// Создание нового пула
	$this->obj['model_pools']->insert(['amount' => 0, 'datetime' => time(), 'end' => (time() + $this->data['config']['marketing']['pool_new_time'])]);

	// Запись даты создания следующего пула
	$this->obj['model_settings']->update(['set' => ['value' => (time() + $this->data['config']['marketing']['pool_new_time'])], 'where' => 204]);

	// Обновление файла JSON
	$this->obj['core_settings']->upd_json();
}
