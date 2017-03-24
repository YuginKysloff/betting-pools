<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */
 
defined('SW_CONSTANT') or die;

// Подключение библиотек для выплат/пополнений
require_once(DIR_ROOT.'/lib/lib_fill.php');

class Controller_Fill extends Lib_Fill {

	public function __construct($data = null) {
		$this->data = $data;
		
		// Закрытый раздел
		if(!$this->data['user']) exit(Lib_Main::rew_page('login'));

		// Подключение моделей
		require_once(DIR_ROOT.'/models/model_fill.php');
		require_once(DIR_ROOT.'/models/model_log.php');
		require_once(DIR_ROOT.'/models/model_users.php');
		require_once(DIR_ROOT.'/models/model_wallets.php');

		// Определение объектов
		$this->obj['model_fill'] = new Model_Fill();
		$this->obj['model_log'] = new Model_Log();
		$this->obj['model_users'] = new Model_Users();
		$this->obj['model_wallets'] = new Model_Wallets();
	}
	
	// Метод по умолчанию
	public function index() {

		// Формирование вида
		$page = Core_View::load('fill', $this->data);
		return $page;
	}
	
	// Получение списка пополнений с пагинацией
	public function get_list() {

		// Количество пополнений на странице
		$per_page = 20;

		// Общее количество пополнений
		$count = $this->obj['model_fill']->user_count(['user_id' => $this->data['user']['id']]);
		if($count) {

		// Начальная страница пополнений
		$start_row = Lib_Main::clear_num($this->data['route']['param']['p'] ?? 0, 0);
		if($start_row < 0 || $start_row >= $count) $start_row = 0;

			// Список пополнений
			$this->data['result']['list'] = $this->obj['model_fill']->user_list(['user_id' => $this->data['user']['id'], 'start_row' => $start_row, 'per_page' => $per_page]);
			if($this->data['result']['list']) {

				// Формирование результатов
				foreach ($this->data['result']['list'] as &$val) {
					$val['amount'] = Lib_Main::beauty_number($val['amount']);
					$val['datetime'] = date($this->data['config']['formats']['datetime'], $val['datetime'] + ($this->data['user']['timezone'] * 60));
				}

				// Пагинация
				if($count > $per_page) $this->data['result']['pagination'] = Lib_Main::pagination_ajax($count, $per_page, 4, $start_row, '/fill/get_list', 'list', '#fill__list');
			}
		}

		// Вывод результатов
		$response['fill__list'] = Core_View::load('fill_list', $this->data);
		return $response;
	}
	
	// Контроллер пополнения
	public function fill_in() {

		// Получение id для вывода результатов
		$result_id = $this->data['route']['param']['result_id'] ?? '';

		// Обработка валюты
		$valute = Lib_Main::clear_str($_POST['valute'] ?? '');

		// Обработка платежной системы
		$payment = Lib_Main::clear_str($_POST['payment'] ?? '');

		// Обработка суммы
		$amount = Lib_Main::abs_num(Lib_Main::clear_num($_POST['amount'] ?? 0, 2));

		// Проверка платежной системы
		if(!$payment) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['fill']['select-payment']]]);
			return $response;
		}
		if(!isset($this->data['config']['payments'][$payment])) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.($this->data['user']['login'] ?? '#empty').' &#8594 '.__FILE__.' ('.__LINE__.')']);
			$response['handler'] = Lib_Main::rew_page('errors/data');
			return $response;
		}

		// Проверка валюты
		if(!$valute) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['fill']['select-valute']]]);
			return $response;
		}
		if(!array_key_exists($valute, $this->data['config']['valutes'])) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.($this->data['user']['login'] ?? '#empty').' &#8594 '.__FILE__.' ('.__LINE__.')']);
			$response['handler'] = Lib_Main::rew_page('errors/data');
			return $response;
		}

		// Проверка поддержки валюты платежной системой
		if(!isset($this->data['config']['payments'][$payment][$valute.'_min_fill'])) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['fill']['paysys-error']]]);
			return $response;
		}

		// Проверка разрешения платежа
		if(!$this->data['config']['valutes'][$valute]['fill']) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['fill']['fill-disabled']]]);
			return $response;
		}

		// Проверка доступности платежной системы
		if(!$this->data['config']['payments'][$payment]['fill']) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['fill']['payment-disabled']]]);
			return $response;
		}

		// Проверка суммы пополнения
		if($amount <= 0) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['fill']['enter-amount']]]);
			return $response;
		}

		// Проверка минимальной/максимальной суммы пополнения
		if($amount < $this->data['config']['payments'][$payment][$valute.'_min_fill']) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['fill']['min-amount'] . ' <span class="g-' . $valute . '">' . Lib_Main::beauty_number($this->data['config']['payment'][$payment][$valute][$valute.'_min_fill']) . '</span>']]);
			return $response;
		}
		if($amount > $this->data['config']['payments'][$payment][$valute.'_max_fill']) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['fill']['max-amount'] . ' <span class="g-' . $valute . '">' . Lib_Main::beauty_number($this->data['config']['payment'][$payment][$valute][$valute.'_max_fill']) . '</span>']]);
			return $response;
		}

		// Проверка количества неудачных попыток за последние 15 мин
		$fill = $this->obj['model_fill']->cnt_last(['user_id' => $this->data['user']['id']]);
		if($fill > 3) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['fill']['three-pending']]]);
			return $response;
		}

		// Запись в таблицу пополнений
		$order_id = $this->obj['model_fill']->insert(['user_id' => $this->data['user']['id'], 'valute' => $valute, 'amount' => $amount, 'payment' => $payment, 'sort' => time(), 'datetime' => time()]);
		
		try{
			$result = $this->fill(['config' => $this->data['config'], 'user' => $this->data['user'], 'order_id' => $order_id, 'payment' => $payment, 'valute' => $valute, 'amount' => $amount, 'comment' => $this->data['lang']['fill']['filling-balance'].' '.$this->data['config']['site']['domain']]);
		} catch(Exception $e) {
			Core_Security::write_warning(['category' => 2, 'user_id' => $this->data['user']['id'], 'text' => '[m-fill|fill-error] : '.($this->data['user']['login'] ?? '#empty').' &#8594 '.__FILE__.' ('.__LINE__.')']);
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['fill']['fill-error']]]);
			return $response;
		}

		// Вывод результатов
		$message['success'][] = $this->data['lang']['fill']['success-wait'];
		$response['handler'][] = $result['form'];
		$response['handler'][] = '<script>$("input[name=amount]").attr("disabled", true); $("#pay_form").submit();</script>';
        $response[$result_id] = Core_View::message($message);
        return $response;
	}
}