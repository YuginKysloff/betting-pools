<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Controller_Admin_Statistics {

    public function __construct($data) {
		$this->data = $data;
		
		// Подключение ядра настроек
		require_once(DIR_ROOT.'/core/core_settings.php');

        // Подключение моделей
        require_once(DIR_ROOT.'/models/model_fill.php');
        require_once(DIR_ROOT.'/models/model_payout.php');
		require_once(DIR_ROOT.'/models/model_settings.php');
        require_once(DIR_ROOT.'/models/model_settings_category.php');
        require_once(DIR_ROOT.'/models/model_users.php');

        // Определение объектов
		$this->obj['core_settings'] = new Core_Settings();
        $this->obj['model_fill'] = new Model_Fill();
        $this->obj['model_payout'] = new Model_Payout();
		$this->obj['model_settings'] = new Model_Settings();
        $this->obj['model_settings_category'] = new Model_Settings_Category();
        $this->obj['model_users'] = new Model_Users();
    }

    // Метод по умолчанию
    public function index() {

        // Формирование вида
        $page = Core_View::load('statistics', $this->data);
        return $page;
    }
	
	// Краткая финансовая информация
	public function finance() {
		$this->finance_list();
		$fill = $this->obj['model_fill']->all(['wl' => 0]);
		if($fill) {
			foreach($fill as $val) {
				$this->data['result']['general']['fill'][$val['valute']]['real'] = $val['amount'];
			}
		}
		$fill = $this->obj['model_fill']->all(['wl' => 1]);
		if($fill) {
			foreach($fill as $val) {
				$this->data['result']['general']['fill'][$val['valute']]['white'] = $val['amount'];
			}
		}
		$payout = $this->obj['model_payout']->all(['wl' => 0]);
		if($payout) {
			foreach($payout as $val) {
				$this->data['result']['general']['payout'][$val['valute']]['real'] = $val['amount'];
			}
		}
		$payout = $this->obj['model_payout']->all(['wl' => 1]);
		if($payout) {
			foreach($payout as $val) {
				$this->data['result']['general']['payout'][$val['valute']]['white'] = $val['amount'];
			}
		}

		// Вывод результатов
        $response['statistics__finance'] = Core_View::load('statistics_finance', $this->data);
        return $response;
	}
	
	// Полная финансовая информация
	public function finance_all() {
		$this->finance_list(true);

		// Вывод результатов
        $content = Lib_Db::escape_string(Core_View::load('statistics_finance_list', $this->data));
        $response['handler'] = '<script>$("#statistics__all_finance").after("'.$content.'").remove();</script>';
        return $response;
	}
	
	// Список финансовой информации
	public function finance_list($more = null) {
		$first_fill = $this->obj['model_fill']->first();
		$first_payout = $this->obj['model_payout']->first();
		$first = $first_fill > $first_payout ? $first_payout : $first_fill;
		if($more) {
			$end = strtotime("tomorrow") - 345600;
			if(!$first || $first > $end) return;
			$start = $first;
		} else {
			$end = strtotime("tomorrow");
			$start = strtotime("tomorrow") - 345600;
		}
		$fill = $this->obj['model_fill']->period(['start' => $start, 'end' => $end]);
		if($fill) {
			foreach($fill as $val) {
				$def = $val['wl'] == '0' ? 'real' : 'white';
				@$this->data['result']['list'][date("Y-m-d", ($val['datetime'] + ($this->data['user']['timezone'] * 60)))]['fill'][$val['valute']][$def] += $val['amount'];
			}
		}
		$payout = $this->obj['model_payout']->period(['start' => $start, 'end' => $end]);
		if($payout) {
			foreach($payout as $val) {
				$def = $val['wl'] == '0' ? 'real' : 'white';
				@$this->data['result']['list'][date("Y-m-d", ($val['datetime'] + ($this->data['user']['timezone'] * 60)))]['payout'][$val['valute']][$def] += $val['amount'];
			}
		}
		for($i=$end; $i>$start; $i-=86400) {
			$time = ($i - 86400) + ($this->data['user']['timezone'] * 60);
			$this->data['result']['more'] = ($time > ($first + ($this->data['user']['timezone'] * 60))) ? true : false;
			if(isset($this->data['result']['list'][date("Y-m-d", $time)])) continue;
			$this->data['result']['list'][date("Y-m-d", $time)] = null;
		}
		krsort($this->data['result']['list']);
	}
	
	// Балансы кошельков
    public function wallets() {
		
		// Дефолтные значения общей суммы
		$this->data['result']['wallets']['total']['usd'] = $this->data['result']['wallets']['total']['rub'] = $this->data['result']['wallets']['total']['eur'] = $this->data['result']['wallets']['total']['btc'] = 0;
		
        // AdvCash
        if($this->data['config']['payments']['advcash']['wallet'] !== '') {
            require_once(DIR_ROOT.'/lib/payments/MerchantWebService.php');
            $merchantWebService = new MerchantWebService();

            $arg0 = new authDTO();
            $arg0->apiName = $this->data['config']['payments']['advcash']['api_name'];
            $arg0->accountEmail = $this->data['config']['payments']['advcash']['wallet'];
            $arg0->authenticationToken = $merchantWebService->getAuthenticationToken($this->data['config']['payments']['advcash']['api_pass']);

            $getBalances = new getBalances();
            $getBalances->arg0 = $arg0;

            try {
                $getBalancesResponse = $merchantWebService->getBalances($getBalances);
                $arBalance = $getBalancesResponse->return;
				$this->data['result']['wallets']['total']['usd'] += $usd = $arBalance[0]->amount;
                $this->data['result']['wallets']['total']['eur'] += $eur = $arBalance[1]->amount;
                $this->data['result']['wallets']['total']['rub'] += $rub = $arBalance[2]->amount;
                $this->data['result']['wallets']['advcash']['usd'] = Lib_Main::beauty_number($usd);
                $this->data['result']['wallets']['advcash']['eur'] = Lib_Main::beauty_number($eur);
                $this->data['result']['wallets']['advcash']['rub'] = Lib_Main::beauty_number($rub);
            } catch (Exception $e) {
				$response['handler'] = '<script>messager("error", "AdvCash : '.Lib_Db::escape_string($e->getMessage().'<br>'.$e->getTraceAsString()).'", 0);</script>';
				$message['error'][] = 'Ошибка';
				$error = Core_View::message($message);
				$this->data['result']['wallets']['advcash']['usd'] = $this->data['result']['wallets']['advcash']['eur'] = $this->data['result']['wallets']['advcash']['rub'] = $error;
            }
        }

        // Payeer
        if($this->data['config']['payments']['payeer']['wallet'] !== '') {
            require_once(DIR_ROOT.'/lib/payments/Cpayeer.php');
            $accountNumber = $this->data['config']['payments']['payeer']['wallet'];
            $apiId = $this->data['config']['payments']['payeer']['api_id'];
            $apiKey = $this->data['config']['payments']['payeer']['api_key'];
            $payeer = new CpayeerLib($accountNumber, $apiId, $apiKey);
            if($payeer->isAuth()) {
                $arBalance = $payeer->getBalance();
				$this->data['result']['wallets']['total']['usd'] += $usd = $arBalance['balance']['USD']['BUDGET'];
                $this->data['result']['wallets']['total']['eur'] += $eur = $arBalance['balance']['EUR']['BUDGET'];
                $this->data['result']['wallets']['total']['rub'] += $rub = $arBalance['balance']['RUB']['BUDGET'];
				$this->data['result']['wallets']['payeer']['usd'] = Lib_Main::beauty_number($usd);
				$this->data['result']['wallets']['payeer']['eur'] = Lib_Main::beauty_number($eur);
				$this->data['result']['wallets']['payeer']['rub'] = Lib_Main::beauty_number($rub);
            } else {
				ob_start();
				echo '<pre>'; var_dump($payeer->getErrors());
				$result = ob_get_contents();
				ob_end_clean();
				$response['handler'] = '<script>messager("error", "Payeer : '.Lib_Db::escape_string($result).'", 0);</script>';
				$message['error'][] = 'Ошибка';
				$error = Core_View::message($message);
				$this->data['result']['wallets']['payeer']['usd'] = $this->data['result']['wallets']['payeer']['eur'] = $this->data['result']['wallets']['payeer']['rub'] = $error;
            }
        }

        // Perfect Money
        if($this->data['config']['payments']['perfectmoney']['wallet'] !== '') {
            $get_balance = @file_get_contents('https://perfectmoney.is/acct/balance.asp?AccountID='.$this->data['config']['payments']['perfectmoney']['account_id'].'&PassPhrase='.$this->data['config']['payments']['perfectmoney']['pass_phrase']);
            $get_balance = preg_replace("!\[(.*?)\]!si","\\1", $get_balance);
            $pattern = "|<td>".$this->data['config']['payments']['perfectmoney']['wallet']."</td><td>(.+?)</td></tr>|is";
            preg_match($pattern, $get_balance, $out);
			if(isset($out[1])) {
				$this->data['result']['wallets']['total']['usd'] += $usd = $out[1];
				$this->data['result']['wallets']['perfectmoney']['usd'] = Lib_Main::beauty_number($usd);
			} else {
				$this->data['result']['wallets']['perfectmoney']['usd'] = Core_View::message(['error' => ['Ошибка']]);
			}
        }
		
		// Вывод результатов
        $response['statistics__wallets'] = Core_View::load('statistics_wallets', $this->data);
        return $response;
    }
	
	// Вкл. / Выкл. платежной системы
	public function wallets_enabled() {
		$payment = $this->data['route']['param']['payment'];
		if(!isset($this->data['config']['payments'][$payment]['enabled'])) {
			$response['handler'] = '<script>messager("error", "Произошла ошибка! Перезагрузите страницу");</script>';
			return $response;
		}
		$enabled = $this->data['config']['payments'][$payment]['enabled'] == '0' ? 1 : 0;	
		$category = $this->obj['model_settings_category']->check_key(['name' => $payment])[0]['id'];
		$setting = $this->obj['model_settings']->get_id_by_name(['name' => 'enabled', 'category' => $category]);
		$this->obj['model_settings']->update(['set' => ['value' => $enabled], 'where' => $setting]);
				
		$this->upd_config();
				
		$enabled = $enabled == 0 ? 'disabled' : 'enabled';
		$response['handler'] = '<script>$("#statistics_wallets__payment_'.$payment.'").attr("src", "/admin/views/'.$this->data['config']['site']['template'].'/img/'.$enabled.'.png");</script>';
		return $response;
	}
	
	// Краткая статистика регистраций
	public function signup() {
		$this->signup_list();

		// Вывод результатов
        $response['statistics__signup'] = Core_View::load('statistics_signup', $this->data);
        return $response;
	}
	
	// Полная статистика регистраций
	public function signup_all() {
		$this->signup_list(true);

		// Вывод результатов
        $response['statistics__all_signup'] = Core_View::load('statistics_signup_list', $this->data);
        $response['handler'] = '<script>$("#statistics__show_all_signup").hide();</script>';
        return $response;
	}
	
	// Список статистики регистраций
	public function signup_list($more = null) {
		$first = $this->obj['model_users']->first();
		if($more) {
			$end = strtotime("tomorrow") - 345600;
			if(!$first || $first > $end) return;
			$start = $first;
		} else {
			$end = strtotime("tomorrow");
			$start = strtotime("tomorrow") - 345600;
		}
		$users = $this->obj['model_fill']->period(['start' => $start, 'end' => $end]);
		if($users) {
			foreach($users as $val) {
				@$this->data['result']['list'][date("Y-m-d", ($val['datetime'] + ($this->data['user']['timezone'] * 60)))] += 1;
			}
		}
		for($i=$end; $i>$start; $i-=86400) {
			$time = ($i - 86400) + ($this->data['user']['timezone'] * 60);
			$this->data['result']['more'] = ($time > ($first + ($this->data['user']['timezone'] * 60))) ? true : false;
			if(isset($this->data['result']['list'][date("Y-m-d", $time)])) continue;
			$this->data['result']['list'][date("Y-m-d", $time)] = 0;
		}
		krsort($this->data['result']['list']);
	}
	
	// Список последних результатов
	public function last() {
		$per_page = 10;
		switch(Lib_Main::clear_str($this->data['route']['param']['dir'] ?? '')) {
			case 'fill':
				$count = $this->obj['model_fill']->all_count();
				$start_row = Lib_Main::clear_num($this->data['route']['param']['p'] ?? 0, 0);
				if($start_row < 0 || $start_row >= $count) $start_row = 0;

				$this->data['result']['list'] = $this->obj['model_fill']->last(['start_row' => $start_row, 'per_page' => $per_page]);
				if($this->data['result']['list']) {
					foreach($this->data['result']['list'] as &$val) {
						$val['sort'] = date($this->data['config']['formats']['admin_datetime'], $val['sort'] + ($this->data['user']['timezone'] * 60));
						$val['payment'] = $this->data['config']['payments'][$val['payment']]['name'];
						$val['valute'] = strtoupper($val['valute']);
						$val['amount'] = Lib_Main::beauty_number($val['amount']);
					}
					$this->data['result']['pagination'] = ($count > $per_page) ? Lib_Main::pagination_ajax($count, $per_page, 4, $start_row, '/'.$this->data['config']['site']['admin'].'/statistics/last/dir/fill', 'last', 'statistics__last') : null;
				}
				$dir = 'fill';
			break;
			case 'payout':
				$count = $this->obj['model_payout']->all_count();
				$start_row = Lib_Main::clear_num($this->data['route']['param']['p'] ?? 0, 0);
				if($start_row < 0 || $start_row >= $count) $start_row = 0;

				$this->data['result']['list'] = $this->obj['model_payout']->last(['start_row' => $start_row, 'per_page' => $per_page]);
				if($this->data['result']['list']) {
					foreach($this->data['result']['list'] as &$val) {
						$val['sort'] = date($this->data['config']['formats']['admin_datetime'], $val['sort'] + ($this->data['user']['timezone'] * 60));
						$val['payment'] = $this->data['config']['payments'][$val['payment']]['name'];
						$val['valute'] = strtoupper($val['valute']);
						$val['amount'] = Lib_Main::beauty_number($val['amount']);
					}
					$this->data['result']['pagination'] = ($count > $per_page) ? Lib_Main::pagination_ajax($count, $per_page, 4, $start_row, '/'.$this->data['config']['site']['admin'].'/statistics/last/dir/payout', 'last', 'statistics__last') : null;
				}
				$dir = 'payout';
			break;
			case 'signup':
				$count = $this->obj['model_users']->all();
				$start_row = Lib_Main::clear_num($this->data['route']['param']['p'] ?? 0, 0);
				if($start_row < 0 || $start_row >= $count) $start_row = 0;
				
				$this->data['result']['list'] = $this->obj['model_users']->last(['start_row' => $start_row, 'per_page' => $per_page]);
				if($this->data['result']['list']) {
					foreach($this->data['result']['list'] as &$val) {
						$val['datetime'] = date($this->data['config']['formats']['admin_datetime'], $val['datetime'] + ($this->data['user']['timezone'] * 60));
					}
					$this->data['pagination'] = ($count > $per_page) ? Lib_Main::pagination_ajax($count, $per_page, 4, $start_row, '/'.$this->config['site']['admin'].'/statistics/get_last_users/result/last_users', 'p_users', '#last_users') : null;
				}
				$dir = 'signup';
			break;
			case 'url':
				$result = $this->obj['model_users']->url();
				if($result) {
					$allcnt = 0;
					foreach($result as $val) {
						if($val['url'] == '') {
							$this->data['result']['list']['']['count'] = (($this->data['result']['list']['']['count']) ?? 0) + $val['count'];
							$allcnt += $val['count'];
							continue;
						}
						$host = parse_url($val['url']);
						if($host['host'] == '') {
							$$this->data['result']['list']['']['count'] = (($this->data['result']['list']['']['count']) ?? 0) + $val['count'];
							$allcnt += $val['count'];
							continue;
						}
						if(!isset($this->data['result']['list'][$host['host']]['count'])) $this->data['result']['list'][$host['host']]['count'] = 0;
						$this->data['result']['list'][$host['host']]['count'] += $val['count'];
						$allcnt += $val['count'];
					}
					arsort($this->data['result']['list']);
					foreach($this->data['result']['list'] as &$val) {
						$val['count'] = Lib_Main::beauty_number($val['count']);
						$val['percent'] = Lib_Main::beauty_number($val['count'] / $allcnt * 100);
					}
				}
				$dir = 'url';
			break;
			default:
				Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')', 'datetime' => time()]);
				$response['handler'] = Lib_Main::rew_page('errors/data');
				return $response;
			break;
		}
		$response['statistics__last'] =  Core_View::load('statistics_last_'.$dir, $this->data);
		return $response;
	}
	
	// Обновление файла настроек
	public function upd_config() {
		file_put_contents(DIR_ROOT.'/json/config.json', json_encode($this->obj['core_settings']->get_tree()), LOCK_EX);
	}
}