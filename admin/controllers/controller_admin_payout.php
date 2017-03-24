<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

// Подключение библиотек
require_once(DIR_ROOT.'/lib/lib_payout.php');

class Controller_Admin_Payout extends Lib_Payout {

	public function __construct($data) {
		$this->data = $data;
		
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

		// Вывод списка результатов
		$this->get_list();

		// Формирование вида
		$page = Core_View::load('payout', $this->data);
		return $page;
	}

    public function get_list() {
        $count = $this->obj['model_payout']->waiting_cnt();
        $per_page = 50;
		$start_row = Lib_Main::clear_num($this->data['route']['param']['p'] ?? 0, 0);
		if($start_row < 0 || $start_row >= $count) $start_row = 0;

		$this->data['result']['payouts'] = $this->obj['model_payout']->waiting(['start_row' => $start_row, 'per_page' => $per_page]);
		if($this->data['result']['payouts']) {
			foreach($this->data['result']['payouts'] as &$val) {
				$val['datetime'] = date($this->data['config']['formats']['admin_datetime'], ($val['datetime'] + ($this->data['user']['timezone'] * 60)));
				$val['valute'] = strtoupper($val['valute']);
				$val['amount'] = Lib_Main::beauty_number($val['amount']);
				$val['payment'] = $this->data['config']['payments'][$val['payment']]['name'];
				$val['reason'] = $this->data['config']['reasons'][$val['reason']];
			}
			$this->data['result']['pagination'] = $count > $per_page ? Lib_Main::pagination($count, $per_page, 4, $start_row, $this->data['config']['site']['admin'].'/payout') : null;
		}
    }
    
    // Подтверждение выплаты
    public function confirm() {

		if(!isset($this->data['route']['param']['ident'])) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.($this->data['user']['login'] ?? '#empty').' &#8594 '.__FILE__.' ('.__LINE__.')', 'datetime' => time()]);
			$response['handler'] = Lib_Main::rew_page('errors/data');
			return $response;
		}
        $id = Lib_Main::clear_num($this->data['route']['param']['ident'] ?? 0, 0);
        
        // Получение платежа
		$payout = $this->obj['model_payout']->get_by_id(['id' => $id]);
		if(!$payout) {
			$response['handler'] = '<script>payout__mask('.$payout['id'].', "error", "Ошибка : выплаты не существует");</script>';
            return $response;
		}
        if($payout['status'] !== '1') {
			$response['handler'] = '<script>payout__mask('.$payout['id'].', "error", "Выплата уже подтверждена");</script>';
			return $response;
        }
		
        // Получение пользователя
        $user = $this->obj['model_users']->get_by_id(['id' => $payout['user_id']]);
		if(!$user) {
			$response['handler'] = '<script>payout__mask('.$payout['id'].', "error", "Ошибка пользователя");</script>';
            return $response;
		}

        // Получение кошелька
        $wallet = $this->obj['model_wallets']->wallet(['user_id' => $user['id'], 'payment' => $payout['payment']]);
		if(!$wallet) {
			$response['handler'] = '<script>payout__mask('.$payout['id'].', "error", "Не обнаружен кошелек пользователя");</script>';
            return $response;
		}

		$this->obj['lib_payout'] = new Lib_Payout();
		$last_id = $this->obj['model_payout']->last_id();
		try{
			$error = $this->obj['lib_payout']->payout(['config' => $this->data['config'], 'valute' => $payout['valute'], 'amount' => Lib_Main::clear_num($payout['amount'], 0), 'payment' => $payout['payment'], 'wallet' => $wallet, 'payment_id' => $payout['id'], 'comment' => $this->data['config']['site']['name']]);
			if($error) {
				$response['handler'] = '<script>payout__mask('.$payout['id'].', "error", "Ошибка"); messager("error", "'.$error.'", 0);</script>';
				return $response;
			}
		} catch(Exception $e) {
			$response['handler'] = '<script>payout__mask('.$payout['id'].', "error", "Ошибка платежного ключа");</script>';
			return $response;
		}
		
        // Обновление статуса выплаты
		$this->obj['model_payout']->update(['set' => ['status' => '3', 'sort' => time()], 'where' => $payout['id']]);

        // Запись в лог
		$this->obj['model_log']->insert(['user_id' => $user['id'], 'text' => '[m-admin--payout|payout-confirm] : <span class="g-sum--'.$payout['valute'].'">-'.Lib_Main::beauty_number($payout['amount']).'</span>', 'datetime' => time()]);
		
		// Вывод результатов
		$response['handler'] = '<script>payout__mask('.$payout['id'].', "success", "Выплачено");</script>';
        return $response;
    }
	
	// Отказ выплаты
    public function denial() {

		if(!isset($this->data['route']['param']['ident'])) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.($this->data['user']['login'] ?? '#empty').' &#8594 '.__FILE__.' ('.__LINE__.')', 'datetime' => time()]);
			$response['handler'] = Lib_Main::rew_page('errors/data');
			return $response;
		}
        $id = Lib_Main::clear_num($this->data['route']['param']['ident'] ?? 0, 0);
        
        // Получение платежа
		$payout = $this->obj['model_payout']->get_by_id(['id' => $id]);
		if(!$payout) {
			$response['handler'] = '<script>payout__mask('.$payout['id'].', "error", "Ошибка : выплаты не существует");</script>';
            return $response;
		}
        if($payout['status'] !== '1') {
			$response['handler'] = '<script>payout__mask('.$payout['id'].', "error", "Выплата уже подтверждена");</script>';
			return $response;
        }
		
        // Получение пользователя
        $user = $this->obj['model_users']->get_by_id(['id' => $payout['user_id']]);
		if(!$user) {
			$response['handler'] = '<script>payout__mask('.$payout['id'].', "error", "Ошибка пользователя");</script>';
            return $response;
		}

		// Обновление статуса выплаты
		$this->obj['model_payout']->update(['set' => ['status' => '2', 'sort' => time()], 'where' => $payout['id']]);	

        // Обновление баланса
		$this->obj['model_users']->update(['set' => [$payout['valute'] => (Lib_Main::clear_num($user[$payout['valute']] + $payout['amount']))], 'where' => $user['id']]);

        // Запись в лог
		$this->obj['model_log']->insert(['user_id' => $user['id'], 'text' => '[m-admin--payout|payout-denied] : <span class="g-sum--'.$payout['valute'].'">+'.Lib_Main::beauty_number($payout['amount']).'</span>', 'datetime' => time()]);

		// Вывод результатов
		$response['handler'] = '<script>payout__mask('.$payout['id'].', "success", "Выплата отклонена");</script>';
        return $response;
    }
}