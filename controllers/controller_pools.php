<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Controller_Pools {

    public function __construct($data) {
        $this->data = $data;

        // Подключение моделей
		require_once(DIR_ROOT.'/models/model_deposits.php');
		require_once(DIR_ROOT.'/models/model_log.php');
		require_once(DIR_ROOT.'/models/model_users.php');
		require_once(DIR_ROOT.'/models/model_pools.php');

        // Определение объектов
		$this->obj['model_deposits'] = new Model_Deposits();
		$this->obj['model_log'] = new Model_Log();
		$this->obj['model_pools'] = new Model_Pools();
		$this->obj['model_users'] = new Model_Users();
    }

    // Метод по умолчанию
    public function index() {
		
		// Закрытый раздел
		if(!$this->data['user']) exit(Lib_Main::rew_page('login'));
    
        // Формирование вида
        $page = Core_View::load('pools', $this->data);
        return $page;			
    }
    
	// Формирование списка пулов
	public function get_list() {
		
		// Закрытый раздел
		if(!$this->data['user']) exit(Lib_Main::rew_page('login'));

        // Количество пулов на странице
        $per_page = 5;

        // Общее количество пулов
        $count = $this->obj['model_pools']->cnt();
        if($count) {

            // Стартовый пул
            $start_row = Lib_Main::clear_num($this->data['route']['param']['p'] ?? 0, 0);
            if($start_row < 0 || $start_row >= $count) $start_row = 0;

            // Список пулов
            $this->data['result']['list'] = $this->obj['model_pools']->get_list(['start_row' => $start_row, 'per_page' => $per_page]);
            if($this->data['result']['list']) {

                // Формирование результатов
                foreach($this->data['result']['list'] as &$val) {
                    if($val['amount']) {
                        foreach($this->data['config']['marketing']['levels'] as $value) {
                            if((!isset($value['max'])) || ($val['amount'] >= $value['min'] && $val['amount'] <= $value['max'])) {
                                $val['percent'] = $value['percent'];
                                break;
                            }
                        }
                        if($val['end'] > time()) {
                            $timer = $val['end'] - time();
                            $val['timer']['hour'] = (int)($timer / 3600);
                            $val['timer']['minute'] = (int)($timer / 60 % 60);
                            $val['timer']['second'] = (int)($timer % 60);
                        }
                    } else {
                        $val['min'] = $this->data['config']['marketing']['levels']['level1']['min'];
                        $val['max'] = $this->data['config']['marketing']['levels']['level1']['max'];
                        $val['percent'] = $this->data['config']['marketing']['levels']['level1']['percent'];
                    }
                    $val['datetime'] = date($this->data['config']['formats']['datetime'], $val['datetime'] + ($this->data['user']['timezone'] * 60));
                    $val['end'] = date($this->data['config']['formats']['datetime'], $val['end'] + ($this->data['user']['timezone'] * 60));
                }

                // Пагинация
                if($count > $per_page) $this->data['result']['pagination'] = Lib_Main::pagination_ajax($count, $per_page, 4, $start_row, '/pools/get_list', 'list', '#pools__list');
            }

            // Вывод результатов
            $response['pools__list'] = Core_View::load('pools_list', $this->data);
            return $response;
        }
    }
    
    // Cписок депозитов
    public function get_deposits() {
        
        // Обработка входящих данных
        $id = Lib_Main::clear_num($this->data['route']['param']['id'] ?? 0, 0);
        if(!$id) {
            Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
            $response['handler'] = Lib_Main::rew_page('errors/data');
            return $response;
        }
        
        // Формирование списка
        $this->data['result']['deposits'] = $this->obj['model_deposits']->get_list(['pool_id' => $id]);
        if($this->data['result']['deposits']) {
            foreach($this->data['result']['deposits'] as &$val) {
                $val['amount'] = Lib_Main::beauty_number($val['amount']);
                $val['payout'] = Lib_Main::beauty_number($val['payout']);
                $val['accrued'] = Lib_Main::beauty_number($val['accrued']);
            }
        }

        // Вывод результатов
        $response['pools_list__info'.$id] = Core_View::load('pools_info', $this->data);
        return $response;
    }
    
    // Покупка депозита
    public function buy() {
		
		// Закрытый раздел
		if(!$this->data['user']) exit(Lib_Main::rew_page('login'));
    
        // Получение и обработка id пула
        $id = Lib_Main::clear_num($this->data['route']['param']['id'] ?? 0, 0);

        // Получение и обработка валюты
        $valute = Lib_Main::clear_str($_POST['valute'] ?? '');

        // Получение и обработка суммы
        $amount = Lib_Main::abs_num(Lib_Main::clear_num($_POST['amount'] ?? 0, 2));
		
		// Проверка наличия пула
        if(!$pools = $this->obj['model_pools']->get_by_id(['id' => $id])) {
            Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
            $response['handler'] = Lib_Main::rew_page('errors/data');
            return $response;
        }
        
        // Проверка введеной суммы
        if(!$amount) {
            $response['pools__error'.$id] = Core_View::message(['error' => [$this->data['lang']['pools']['enter-amount']]]);
            return $response;
        }

        // Проверка необходимой суммы на балансе
        if($amount > $this->data['user'][$valute]) {
			$response['pools__error'.$id] = Core_View::message(['error' => [$this->data['lang']['pools']['insufficient']]]);
            return $response;
        }
		
		// Проверка на минимальную сумму
        if($amount < 1) {
			$response['pools__error'.$id] = Core_View::message(['error' => ['Минимум <span class="g-currency-usd">1</span>']]);
            return $response;
        }
		
		// Добавление суммы в пул
        $this->obj['model_pools']->update(['set' => ['amount' => ($pools['amount'] + $amount)], 'where' => $id]);
                
        // Списание суммы с баланса
        $this->obj['model_users']->update(['set' => [$valute => Lib_Main::clear_num($this->data['user'][$valute] - $amount)], 'where' => $this->data['user']['id']]);
        
        // Добавление депозита
        $this->obj['model_deposits']->insert(['pool_id' => $id, 'user_id' => $this->data['user']['id'], 'amount' => $amount, 'next' => (time() + (60 * 60 * 24)), 'datetime' => time()]);
    
        // Добавление записи в лог событий
        $this->obj['model_log']->insert(['user_id' => $this->data['user']['id'], 'text' => '[m-pools|buy-deposit] #'.$id.' : <span class="g-currency-'.$valute.'">'.Lib_Main::beauty_number($amount).'</span>', 'control' => $valute.'='.($this->data['user'][$valute] - $amount), 'datetime' => time()]);
    
        // Вывод результатов
		$this->data['result']['buyed'][$id] = Lib_Main::beauty_number($amount);
        if(!isset($_SESSION['temp'])) {
			$response = $this->get_list();
			$response['balance__usd'] = Lib_Main::beauty_number($this->data['user'][$valute] - $amount);
		} else {
			unset($_SESSION['temp']);
		}
        return $response ?? '';
    }
}