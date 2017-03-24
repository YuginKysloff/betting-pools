<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Controller_Index {

    public function __construct($data) {
        $this->data = $data;

        // Подключение моделей
        require_once(DIR_ROOT.'/models/model_news.php');
        require_once(DIR_ROOT.'/models/model_payout.php');
        require_once(DIR_ROOT.'/models/model_users.php');
        require_once(DIR_ROOT.'/models/model_fill.php');
        require_once(DIR_ROOT.'/models/model_pools.php');
        require_once(DIR_ROOT.'/models/model_deposits.php');

        // Определение объектов
        $this->obj['model_news'] = new Model_News();
        $this->obj['model_fill'] = new Model_Fill();
        $this->obj['model_payout'] = new Model_Payout();
        $this->obj['model_users'] = new Model_Users();
        $this->obj['model_pools'] = new Model_Pools();
        $this->obj['model_deposits'] = new Model_Deposits();
    }

    // Метод по умолчанию
    public function index() {

        // Вывод списка результатов
        $this->get_list();

        // Формирование вида
        $page = Core_View::load('index', $this->data);
        return $page;
    }

    // Получение общей статистики
    public function get_list() {
        $this->data['result']['all_users'] = Lib_Main::beauty_number($this->obj['model_users']->all());
        $payout = $this->obj['model_payout']->get_paid();
        if ($payout) {
            foreach ($payout as $val) {
                $this->data['result']['payout'][$val['valute']] = Lib_Main::beauty_number($val['amount']);
            }
        }
        $this->data['result']['news'] = $this->obj['model_news']->get(['current_lang' => $this->data['current_lang'], 'start_row' => 0, 'per_page' => 3]);
        $result = $this->obj['model_fill']->all();
        if ($result) {
            foreach ($result as $val) {
                $fill[$val['valute']] = $val['amount'];
            }
        }
        $result = $this->obj['model_payout']->all();
        if ($result) {
            foreach ($result as $val) {
                $payout[$val['valute']] = $val['amount'];
            }
        }
        $this->data['result']['reserve']['usd'] = isset($fill['usd']) ? Lib_Main::beauty_number(($fill['usd'] - ($payout['usd'] ?? 0)) * 0.85) : 0;
        $this->data['result']['payouts']['usd'] = Lib_Main::beauty_number($payout['usd'] ?? 0);
        
        // Последние 5 пополнения и выплаты
        $timezone = $_COOKIE['timezone'] ?? 0;
        $timezone = Lib_Main::clear_num(($this->data['user']['timezone'] ?? $timezone) * 60, 0);
        $this->data['result']['fill'] = $this->obj['model_fill']->last(['start_row' => 0, 'per_page' => 5]);
        if($this->data['result']['fill']) {
            foreach($this->data['result']['fill'] as &$val) {
                $val['datetime'] = date($this->data['config']['formats']['datetime_full'], ($val['datetime'] + $timezone));
                $val['amount'] = Lib_Main::beauty_number($val['amount']);
            }
        }
        $this->data['result']['payout'] = $this->obj['model_payout']->last(['start_row' => 0, 'per_page' => 5]);
        if($this->data['result']['payout']) {
            foreach($this->data['result']['payout'] as &$val) {
                $val['datetime'] = date($this->data['config']['formats']['datetime_full'], ($val['datetime'] + $timezone));
                $val['amount'] = Lib_Main::beauty_number($val['amount']);
            }
        }
    }
	
	// Последний активный пулл
    public function get_last() {
        $this->data['result']['list'][0] = $this->obj['model_pools']->get_last();
        if($this->data['result']['list'][0]) {
			if($this->data['result']['list'][0]['amount']) {
				foreach($this->data['config']['marketing']['levels'] as $value) {
					if((!isset($value['max'])) || ($this->data['result']['list'][0]['amount'] >= $value['min'] && $this->data['result']['list'][0]['amount'] <= $value['max'])) {
						$this->data['result']['list'][0]['percent'] = $value['percent'];
						break;
					}
				}
				if($this->data['result']['list'][0]['end'] > time()) {
					$timer = $this->data['result']['list'][0]['end'] - time();
					$this->data['result']['list'][0]['timer']['hour'] = (int)($timer / 3600);
					$this->data['result']['list'][0]['timer']['minute'] = (int)($timer / 60 % 60);
					$this->data['result']['list'][0]['timer']['second'] = (int)($timer % 60);
				}
			} else {
				$this->data['result']['list'][0]['min'] = $this->data['config']['marketing']['levels']['level1']['min'];
				$this->data['result']['list'][0]['max'] = $this->data['config']['marketing']['levels']['level1']['max'];
				$this->data['result']['list'][0]['percent'] = $this->data['config']['marketing']['levels']['level1']['percent'];
			}
			$this->data['result']['list'][0]['datetime'] = date($this->data['config']['formats']['datetime'], $this->data['result']['list'][0]['datetime'] + ($this->data['user']['timezone'] * 60));
			$this->data['result']['list'][0]['end'] = date($this->data['config']['formats']['datetime'], $this->data['result']['list'][0]['end'] + ($this->data['user']['timezone'] * 60));
        }
		
		// Формирование вида
        $response['index__last_pool'] = Core_View::load('pools_list', $this->data);
		return $response;
    }
	
	// Покупка депозита
    public function buy() {
		
		require_once(DIR_ROOT.'/controllers/controller_pools.php');
		
		$obj['controller_pools'] = new Controller_Pools($this->data);
		
		$_SESSION['temp'] = true;
		$response = $obj['controller_pools']->buy();
		if(!$response) {
			$id = Lib_Main::clear_num($this->data['route']['param']['id'] ?? 0, 0);
			$valute = Lib_Main::clear_str($_POST['valute'] ?? '');
			$amount = Lib_Main::abs_num(Lib_Main::clear_num($_POST['amount'] ?? 0, 2));
			$this->data['result']['buyed'][$id] = Lib_Main::beauty_number($amount);
			$response = $this->get_last();
			$response['balance__usd'] = Lib_Main::beauty_number($this->data['user'][$valute] - $amount);
		}
		return $response;
	}
}