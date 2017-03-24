<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */
 
defined('SW_CONSTANT') or die;

// Подключение библиотек для выплат
require_once(DIR_ROOT.'/lib/lib_payout.php');

class Controller_Payout {

	public function __construct($data = null) {
		$this->data = $data;
		
		// Закрытый раздел
		if(!$this->data['user']) exit(Lib_Main::rew_page('login'));

		// Подключение моделей
		require_once(DIR_ROOT.'/models/model_log.php');
		require_once(DIR_ROOT.'/models/model_payout.php');
		require_once(DIR_ROOT.'/models/model_users.php');
		require_once(DIR_ROOT.'/models/model_wallets.php');
		
		// Определение объектов
		$this->obj['model_log'] = new Model_Log();
		$this->obj['model_payout'] = new Model_Payout();
		$this->obj['model_users'] = new Model_Users();
		$this->obj['model_wallets'] = new Model_Wallets();		
	}
	
	// Метод по умолчанию
	public function index() {
		
		// Вывод результатов
		$page = Core_View::load('payout', $this->data);
		return $page;
	}

	// Получение списка выплат
	public function get_list() {

		// Количество выплат на странице
		$per_page = 50;

		// Общее количество выплат
		$count = $this->obj['model_payout']->user_count(['user_id' => $this->data['user']['id']]);
		if($count) {

		// Начальная страница выплат
		$start_row = Lib_Main::clear_num($this->data['route']['param']['p'] ?? 0, 0);
		if($start_row < 0 || $start_row >= $count) $start_row = 0;

			// Список выплат
			$this->data['result']['list'] = $this->obj['model_payout']->user_list(['user_id' => $this->data['user']['id'], 'start_row' => $start_row, 'per_page' => $per_page]);
			if($this->data['result']['list']) {

				// Формирование результатов
				foreach ($this->data['result']['list'] as &$val) {
					$val['amount'] = Lib_Main::beauty_number($val['amount']);
					$val['datetime'] = date($this->data['config']['formats']['datetime'], $val['datetime'] + ($this->data['user']['timezone'] * 60));
				}

				// Пагинация
				$this->data['result']['pagination'] = ($count > $per_page) ? Lib_Main::pagination($count, $per_page, 5, $start_row, '/payout/get_list', 'list', '#payout__list'):'';
			}
		}

		// Вывод результатов
		$response['payout__list'] = Core_View::load('payout_list', $this->data);
		return $response;
	}
	
	// Контроллер выплаты
	public function payout_in() {

		// Получение id для вывода результатов
		$result_id = $this->data['route']['param']['result_id'] ?? '';

		// Обработка платежной системы
		$payment = Lib_Main::clear_str($_POST['payment'] ?? '');

		// Обработка валюты
		$valute = Lib_Main::clear_str($_POST['valute'] ?? '');

		// Обработка суммы
		$amount = Lib_Main::abs_num(Lib_Main::clear_num($_POST['amount'] ?? 0, 2));

		// Проверка платежной системы
		if(!$payment) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['payout']['select-payment']]]);
			return $response;
		}
		if(!isset($this->data['config']['payments'][$payment])) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.($this->data['user']['login'] ?? '#empty').' &#8594 '.__FILE__.' ('.__LINE__.')']);
			$response['handler'] = Lib_Main::rew_page('errors/data');
			return $response;
		}

		// Проверка введенной валюты
		if(!$valute) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['payout']['select-valute']]]);
			return $response;
		}
		if(!array_key_exists($valute, $this->data['config']['valutes'])) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.($this->data['user']['login'] ?? '#empty').' &#8594 '.__FILE__.' ('.__LINE__.')']);
			$response['handler'] = Lib_Main::rew_page('errors/data');
			return $response;
		}

		// Проверка поддержки валюты платежной системой
		if(!isset($this->data['config']['payments'][$payment][$valute.'_min_payout'])) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['payout']['paysys-error']]]);
			return $response;
		}

		// Проверка суммы выпплаты
		if($amount <= 0) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['payout']['enter-amount']]]);
			return $response;
		}

		// Проверка минимальной суммы выплаты
		if($amount < $this->data['config']['payments'][$payment][$valute.'_min_payout']) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['payout']['min-amount'] . ' <span class="g-' . $valute . '">' . Lib_Main::beauty_number($this->data['config']['payments'][$payment][$valute.'_min_payout']) . '</span>']]);
			return $response;
		}

		// Проверка разрешения выплаты
		if(!$this->data['config']['valutes'][$valute]['payout']) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['payout']['payout-disabled']]]);
			return $response;
		}
		
		// Проверка доступности платежной системы
		if(!$this->data['config']['payments'][$payment]['payout']) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['payout']['payment-disabled']]]);
			return $response;
		}

		// Проверка необходимой суммы на балансе
		if($amount > $this->data['user'][$valute]) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['payout']['insufficient']]]);
			return $response;
		}

		// Проверка кошелька
		$wallet = $this->obj['model_wallets']->wallet(['user_id' => $this->data['user']['id'], 'payment' => $payment])['wallet'] ?? '';
		if(!$wallet) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['payout']['select-wallet']]]);
			return $response;
		}

		// Обновление баланса
		$balance[$valute] = Lib_Main::clear_num($this->data['user'][$valute] - $amount, 2);
		$this->obj['model_users']->update(['set' => [$valute => $balance[$valute]], 'where' => $this->data['user']['id']]);

		// Проверка инстанта
		if($this->data['user']['instant'] != 1) {
			return $this->payout_wait(['valute' => $valute, 'amount' => $amount, 'payment' => $payment, 'reason' => $this->data['user']['instant'], 'balance' => $balance, 'result_id' => $result_id]);
		}

		// Проверка максимальной суммы для инстанта
		if($amount > $this->data['config']['payments'][$payment][$valute.'_max_payout']) {
			return $this->payout_wait(['valute' => $valute, 'amount' => $amount, 'payment' => $payment, 'reason' => 3, 'balance' => $balance, 'result_id' => $result_id]);
		}

		// Если последний вывод раньше чем указано в настройках
		if((time() - $this->obj['model_payout']->get_last_user_sort(['user_id' => $this->data['user']['id']])) < $this->data['config']['site']['instant_interval']) {
			return $this->payout_wait(['valute' => $valute, 'amount' => $amount, 'payment' => $payment, 'reason' => 4, 'balance' => $balance, 'result_id' => $result_id]);
		}

		// Получение последней выплаты
		$payment_id = $this->obj['model_payout']->get_last_id();

		// Вылата средств пользователю
		try {
			$this->obj['lib_payout'] = new Lib_Payout();
			$comment = $this->data['lang']['payout']['user-payment'].' '.$this->data['user']['login'].' '.$this->data['lang']['payout']['from-project'].' '.$this->data['config']['site']['domain'];
			$result = $this->obj['lib_payout']->payout(['config' => $this->data['config'], 'valute' => $valute, 'amount' => $amount, 'payment' => $payment, 'wallet' => $wallet, 'payment_id' => ++$payment_id, 'comment' => $comment]);
		} catch(Exception $e) {
			return $this->payout_wait(['valute' => $valute, 'amount' => $result['amount'], 'payment' => $payment, 'reason' => '8', 'balance' => $balance, 'result_id' => $result_id]);
		}
		if($result['status'] == 'fail') {
			return $this->payout_wait(['valute' => $valute, 'amount' => $result['amount'], 'payment' => $payment, 'reason' => '8', 'balance' => $balance, 'result_id' => $result_id]);
		} else {

			// Запись в таблицу выплаты
			$this->obj['model_payout']->insert(['user_id' => $this->data['user']['id'], 'valute' => $valute, 'amount' => $amount, 'payment' => $payment, 'status' => 1, 'reason' => 1, 'sort' => time(), 'datetime' => time()]);

			// Запись в лог
			$this->obj['model_log']->insert(['user_id' => $this->data['user']['id'], 'text' => '[m-payout|out-instant] : - <span class="g-currency-'.$valute.'">'.Lib_Main::beauty_number($amount).'</span>', 'control' => $valute.'='.$balance[$valute], 'datetime' => time()]);

			// Вывод результататов
			$message['success'][] = $this->data['lang']['payout']['out-instant'];
			$response['balance__'.$valute] = Lib_Main::beauty_number($balance[$valute]);
			$response['payout__list'] = $this->get_list()['payout__list'];
			$response[$result_id] = Core_View::message($message);
			return $response;
		}
	}

	// Выплата в ожидание
	public function payout_wait($data) {
		$this->obj['model_payout']->insert(['user_id' => $this->data['user']['id'], 'valute' => $data['valute'], 'amount' => $data['amount'], 'payment' => $data['payment'], 'status' => 0, 'reason' => $data['reason'], 'sort' => time(), 'datetime' => time()]);
		$this->obj['model_log']->insert(['user_id' => $this->data['user']['id'], 'text' => '[m-payout|payment-mode] : - <span class="g-currency-'.$data['valute'].'">'.Lib_Main::beauty_number($data['amount']).'</span>', 'control' => $data['valute'].'='.$data['balance'][$data['valute']], 'datetime' => time()]);
		$message['success'][] = $this->data['lang']['payout']['payment-mode'];
		$response['balance__'.$data['valute']] = Lib_Main::beauty_number($data['balance'][$data['valute']]);
		$response['payout__list'] = $this->get_list()['payout__list'];
		$response[$data['result_id']] = Core_View::message($message);
		return $response;
	}
}