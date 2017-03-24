<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

// Подключение библиотек
require_once(DIR_ROOT . '/lib/lib_payout.php');

// Подключение моделей
require_once(DIR_ROOT . '/models/model_deposits.php');
require_once(DIR_ROOT . '/models/model_log.php');
require_once(DIR_ROOT . '/models/model_users.php');


// Определение объектов
$this->obj['model_deposits'] = new Model_Deposits();
$this->obj['model_log'] = new Model_Log();
$this->obj['model_users'] = new Model_Users();


$start_time = time();
$max_execution_time = ini_get('max_execution_time') ?? 60;
$max_execution_time -= 10;

// Получение не закрытых депозитов со просроченным сроком выплаты
$result = $this->obj['model_deposits']->get_list_by_time();
if($result) {
	$valute = 'usd';
	foreach($result as $val) {

		// Проверка таймера
		if(time() > ($start_time + $max_execution_time)) break;

		// Проверка состояния депозита
		if($val['amount'] - $val['payout'] <= 0) continue;

		// Вычисление текущей процентной ставки
		$percent = 0;
		for($i = 1; $i < 9; $i++) {
			if($i < 8) {
				if($val['pool_amount'] >= $this->data['config']['marketing']['levels']['level'.$i]['min'] && $val['pool_amount'] <= $this->data['config']['marketing']['levels']['level'.$i]['max']) {
					$percent = $this->data['config']['marketing']['levels']['level'.$i]['percent'];
					break;
				}
			} else {
				if($val['pool_amount'] >= $this->data['config']['marketing']['levels']['level'.$i]['min']) {
					$percent = $this->data['config']['marketing']['levels']['level'.$i]['percent'];
					break;
				}
			}
		}

		// Вычисление суммы платежа
		$amount = Lib_Main::clear_num(($val['amount'] - $val['payout']) * $percent / 100, 2);
		if($amount > 0) {

			// Получение пользователя
			$users[$val['user_id']] = $this->obj['model_users']->get_by_id(['id' => $val['user_id']]);

			// Обновление строки депозита
			$this->obj['model_deposits']->update(['set' => ['next' => (time() + (60 * 60 * 24))], 'where' => $val['id']]);

			// Зачисление на баланс пользователю
			$this->obj['model_users']->update(['set' => [$valute => Lib_Main::clear_num($users[$val['user_id']][$valute] + $amount)], 'where' => $users[$val['user_id']]['id']]);

			// Запись в лог
			$this->obj['model_log']->insert(['user_id' => $users[$val['user_id']]['id'], 'text' => '[m-cron--deposits|payout] #'.$val['id'].' : <span class="g-currency-'.$valute.'">'.Lib_Main::beauty_number($amount).'</span>', 'control' => $valute.'='.Lib_Main::clear_num($users[$val['user_id']][$valute] + $amount), 'datetime' => time()]);
		}
	}
}