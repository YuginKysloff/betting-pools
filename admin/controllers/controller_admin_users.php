<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */
 
defined('SW_CONSTANT') or die;

require_once(DIR_ROOT.'/controllers/controller_users.php');

class Controller_Admin_Users extends Controller_Users {
	
	public function __construct($data = null) {
		parent::__construct($data);
		
		// Уровень доступа
		$this->access = 4;

		if($this->data['route']['controller'] !== 'users' && (!isset($this->data['route']['method']) || $this->data['route']['method'] !== 'login') && !$this->logged_in) {
			$page = Core_View::load('login', $this->data);
			if(!$page) exit(Lib_Main::rew_page('errors/404'));
			exit($page);
		}

		// Подключение моделей
		require_once(DIR_ROOT.'/models/model_fill.php');
		require_once(DIR_ROOT.'/models/model_payout.php');
		require_once(DIR_ROOT.'/models/model_users.php');
		require_once(DIR_ROOT.'/models/model_wallets.php');

		// Определение объектов
		$this->obj['model_fill'] = new Model_Fill();
		$this->obj['model_payout'] = new Model_Payout();
		$this->obj['model_users'] = new Model_Users();
		$this->obj['model_wallets'] = new Model_Wallets();
	}

	// Метод по умолчанию
	public function index() {

		// Формирование вида
		$page = Core_View::load('users', $this->data);
		return $page;
	}

	// Получение всех пользователей
	public function get_list() {

		// Проверка фильтров
		$regexp = Lib_Main::clear_num($_POST['regexp'] ?? 0, 0);
		$like = Lib_Main::clear_str($_POST['like'] ?? '');
		$field = Lib_Main::clear_str($_POST['field'] ?? '');
		$value = Lib_Main::clear_str($_POST['value'] ?? '');
		
		if(!empty($value)) {
			$where = "WHERE ";
			switch($field) {
				case 'login':
				case 'email':
				case 'ip':
					if($regexp != 0) {
						$where .= empty($like) ? "u1.`".$field."` = '".$value."'" : "u1.`".$field."` LIKE '".str_replace("val", $value, $like)."'";
					} else {
						$where .= empty($like) ? "UPPER(u1.`".$field."`) = UPPER('".$value."')" : "UPPER(u1.`".$field."`) LIKE UPPER('".str_replace("val", $value, $like)."')";
					}
				break;
				case 'sponsor':
					if($regexp != 0) {
						$where .= empty($like) ? "u2.`login` = '".$value."'" : "u2.`login` LIKE '".str_replace("val", $value, $like)."'";
					} else {
						$where .= empty($like) ? "UPPER(u2.`login`) = UPPER('".$value."')" : "UPPER(u2.`login`) LIKE UPPER('".str_replace("val", $value, $like)."')";
					}
				break;
			}
		}

		$count = $this->obj['model_users']->filter_cnt(['where' => ($where ?? '')]);
		$per_page = 50;
        $start_row = Lib_Main::clear_num($this->data['route']['param']['p'] ?? 0, 0);
        if($start_row < 0 || $start_row >= $count) $start_row = 0;
		
		if($count > 0) {
			$this->data['result']['list'] = $this->obj['model_users']->filter_list(['where' => ($where ?? ''), 'like' => $like, 'field' => $field, 'value' => $value, 'start_row' => $start_row, 'per_page' => $per_page]);
			if($this->data['result']['list']) {
				foreach($this->data['result']['list'] as &$val) {
					
					// Количество рефералов
					$val['refs'] = $this->obj['model_users']->ref_count(['sponsor_id' => $val['id']]);
					
					// Сумма пополнений по валютам
					$fill = $this->obj['model_fill']->user_all(['user_id' => $val['id']]);
					if($fill) {
						foreach($fill as $value) {
							$val['fill'][$value['valute']] = Lib_Main::beauty_number($value['amount'] ?? 0);
						}
					}
					
					// Сумма выплат по валютам
					$payout = $this->obj['model_payout']->user_all(['user_id' => $val['id']]);
					if($payout) {
						foreach($payout as $value) {
							$val['payout'][$value['valute']] = Lib_Main::beauty_number($value['amount'] ?? 0);
						}
					}
					
					// Формирование баланса
					if(isset($val['usd'])) $val['usd'] = Lib_Main::beauty_number($val['usd']);
					if(isset($val['rub'])) $val['rub'] = Lib_Main::beauty_number($val['rub']);
					if(isset($val['eur'])) $val['eur'] = Lib_Main::beauty_number($val['eur']);
					if(isset($val['btc'])) $val['btc'] = Lib_Main::beauty_number($val['btc']);
					
					
					$val['activity'] = date($this->data['config']['formats']['date'], $val['activity'] + ($this->data['user']['timezone'] * 60));
					$val['datetime'] = date($this->data['config']['formats']['date'], $val['datetime'] + ($this->data['user']['timezone'] * 60));
					
					// Список кошельков
					$wallets = $this->obj['model_wallets']->get(['user_id' => $val['id']]);
					if($wallets) {
						foreach($wallets as $value) {
							$val['wallets'][$value['payment']] = $value['wallet'];
						}
					}
				}
				$this->data['result']['pagination'] = ($count > $per_page) ? Lib_Main::pagination_ajax($count, $per_page, 4, $start_row, '/'.$this->data['config']['site']['admin'].'/users/get_list', 'list', '#users__list', 'users__form') : '';
			}
		}
		$response['users__list'] = Core_View::load('users_list', $this->data);
		return $response;
	}

	// Получение базы E-mail
	public function download_base() {
		
		// Проверка прав
		if($this->data['user']['access'] < 5) {
            Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
            $response['handler'] = Lib_Main::rew_page('errors/data');
            return $response;
        }
		
		$result = $this->obj['model_users']->get_emails();
		if($result) {
			$emails = '';
			foreach($result as $val) {
				$emails .= $val['email']."\r\n";
			}
			$name = date("Y-m-d", time())."--".$this->data['config']['site']['name']."--base-email--".time().'.txt';
			file_put_contents(DIR_ROOT."/txt/emails/".$name, $emails);
			$response['users__emails_block'] = '<a id="users__loadbase" href="'.$this->data['config']['site']['protocol'].'://'.$this->data['config']['site']['domain'].'/txt/emails/'.$name.'" download>Скачать файл '.$name.'</a><script>$("#users__loadbase")[0].click();</script>';
			return $response;
		}
		$message['grey'][] = 'Нет результатов';
		$response['users__emails_block'] = Core_View::message($message);
		return $response;
	}

	// Изменение инстанта
	public function change() {

		if(!isset($this->data['route']['param']['user_id']) || !isset($this->data['route']['param']['result']) || !isset($this->data['route']['param']['action'])) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
            $response['handler'] = Lib_Main::rew_page('errors/data');
            return $response;
		}
		$user_id = Lib_Main::clear_num($this->data['route']['param']['user_id'], 0);
		$user = $this->obj['model_users']->get_by_id(['id' => $user_id]);
		if(!$user) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
            $response['handler'] = Lib_Main::rew_page('errors/data');
            return $response;
		}
		$action = $this->data['route']['param']['action'] ?? '';
		if(empty($action) || !in_array($action, ['avatar', 'instant', 'access', 'password'])) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
            $response['handler'] = Lib_Main::rew_page('errors/data');
            return $response;
		}
		$result = Lib_Main::clear_str(urldecode($this->data['route']['param']['result']) ?? '');
		if(empty($result)) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
			$response['handler'] = Lib_Main::rew_page('errors/data');
			return $response;
		}

		// Проверка наличия прав пользователя на изменение
		if($this->data['user']['access'] <= $user['access']) {
			$message['error'][] = 'Ошибка';
			$response[$result] = Core_View::message($message);
			$response['handler'] = '<script>messager("error", "Нет прав на изменение");</script>';
			return $response;
		}

		// Подготовка результатов
		switch($action) {
			case 'avatar':
				if(empty($user['avatar'])) {
					$response['handler'] = '<script>messager("error", "Аватар отсутствует");</script>';
					return $response;
				}
				if(file_exists(DIR_ROOT.'/download/images/avatar/'.$user['avatar'])) {
					unlink(DIR_ROOT.'/download/images/avatar/'.$user['avatar']);
				}
				if(file_exists(DIR_ROOT.'/download/images/avatar/min/'.$user['avatar'])) {
					unlink(DIR_ROOT.'/download/images/avatar/min/'.$user['avatar']);
				}
				$this->obj['model_users']->update(['set' => ['avatar' => ''], 'where' => $user_id]);
				$response['handler'] = '<script>$("#'.$this->data['route']['param']['result'].'").attr("src", "/download/images/avatar/min/empty.jpg"); messager("success", "Аватар успешно удален");</script>';
				return $response;
			break;
			case 'instant':
				if($user['instant'] != 1) {
					$value = 1;
					$message['green'][] = 'Инстант';
				} else {
					$value = 2;
					$message['red'][] = 'Инстант';
				}
			break;
			case 'access':
				if($user['access'] != 1) {
					$value = 1;
					$message['red'][] = 'Блок';
				} else {
					$value = 2;
					$message['green'][] = 'Блок';
				}
			break;
			case 'password':
				$pass = Lib_Main::generate_password(10);
				$value = hash('sha512', $pass);
				$value = strrev($value);
				$value .= $this->data['config']['site']['pass_key'];
				$message['orange'][] = $pass;
			break;
			default:
				Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
				$response['handler'] = Lib_Main::rew_page('errors/data');
				return $response;
			break;
		}
		
		// Обновление данных
		$this->obj['model_users']->update(['set' => [$action => $value], 'where' => $user_id]);

		$response[$result] = Core_View::message($message);
		return $response;
	}

	// Обновление данных пользователя
	public function save() {
		
		if(!isset($this->data['route']['param']['user_id']) || !isset($_POST['access']) || !isset($_POST['rcb']) || !isset($_POST['email'])) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
            $response['handler'] = Lib_Main::rew_page('errors/data');
            return $response;
		}
		$user_id = Lib_Main::clear_num($this->data['route']['param']['user_id'], 0);
		$user = $this->obj['model_users']->get_by_id(['id' => $user_id]);
		if(!$user) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
            $response['handler'] = Lib_Main::rew_page('errors/data');
            return $response;
		}
		
		// Проверка наличия прав пользователя на изменение
		if($this->data['user']['access'] <= $user['access']) {
			$response['handler'] = '<script>messager("error", "Нет прав на изменение");</script>';
			return $response;
		}
		
		$access = Lib_Main::clear_num($_POST['access'] ?? 0, 0);
		$rcb = Lib_Main::clear_num($_POST['rcb'] ?? 0, 0);
		$email = Lib_Main::clear_str($_POST['email'] ?? '');
		
		if($access != 0 && $access != $user['access']) {
			$data['set']['access'] = $access;
		}
		if($rcb != 0 && $rcb != $user['rcb']) {
			$data['set']['rcb'] = $rcb;
		}
		if(!empty($email) && $email != $user['email']) {
			$data['set']['email'] = $email;
		}
		
		if($this->data['user']['access'] >= 5) {
			
			// Обновление кошельков
			$result = $this->obj['model_wallets']->get(['user_id' => $user['id']]);
			if($result) {
				foreach($result as $value) {
					$wallets[$value['payment']] = $value['wallet'];
				}
			}
			foreach($this->data['config']['payments'] as $key => $val) {
				if(!isset($_POST[$key])) continue;
				$new = Lib_Main::clear_str($_POST[$key] ?? '');
				if(isset($wallets[$key])) {
					if($new == $wallets[$key]) continue;
					if(empty($new)) {
						$this->obj['model_wallets']->delete(['where' => [['user_id', '=', $user['id']], ['payment', '=', $key]]]);
					} else {
						$this->obj['model_wallets']->update(['set' => ['wallet' => $new], 'where' => [['user_id', '=', $user['id']], ['payment', '=', $key]]]);
					}
					$update = true;
				} elseif(!empty($new)) {
					$this->obj['model_wallets']->insert(['user_id' => $user['id'], 'payment' => $key, 'wallet' => $new]);
					$update = true;
				}
			}
			
			// Обновление баланса
			if(isset($_POST['balance']) && is_array($_POST['balance'])) {
				foreach($_POST['balance'] as $key => $val) {
					$amount = Lib_Main::clear_str($val);
					if(isset($user[$key]) && $user[$key] != $amount) $data['set'][$key] = $amount;
				}
			}
		}

		// Обновление данных
		if(isset($data['set'])) $this->obj['model_users']->update(['set' => $data['set'], 'where' => $user['id']]);
		if(isset($update) || isset($data['set'])) {
			$response['handler'] = '<script>messager("success", "Информация успешно обновлена");</script>';
			return $response;
		}
		$response['handler'] = '<script>messager("error", "Изменений не обнаружено");</script>';
		return $response;
	}
}