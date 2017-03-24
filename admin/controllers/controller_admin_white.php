<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Controller_Admin_White {

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
        require_once(DIR_ROOT.'/models/model_white.php');

        // Определение объектов
		$this->obj['core_settings'] = new Core_Settings();
        $this->obj['model_fill'] = new Model_Fill();
        $this->obj['model_payout'] = new Model_Payout();
        $this->obj['model_settings'] = new Model_Settings();
        $this->obj['model_settings_category'] = new Model_Settings_Category();
        $this->obj['model_users'] = new Model_Users();
        $this->obj['model_white'] = new Model_White();
    }

    public function index() {

		// Формирование спонсоров для авторегистраций
		if(isset($this->data['config']['automatization']['autosignup']['sponsor'])) {
			if(is_array($this->data['config']['automatization']['autosignup']['sponsor'])) {
				foreach($this->data['config']['automatization']['autosignup']['sponsor'] as $val) {
					$this->data['result']['list'][] = $val;
				}
			} else {
				$this->data['result']['list'][] = $this->data['config']['automatization']['autosignup']['sponsor'];
			}
			$this->data['result']['sponsor'] = Core_View::load('white_sponsor_list', $this->data);
		}
		
		// Формирование списков автопополнений
		// Категория 1
		$path = DIR_ROOT.'/admin/txt/autofill-category-1.txt';
		if(file_exists($path)) {
			$this->data['result']['autofill']['category-list-1'] = file_get_contents($path);
		}
		
		// Категория 2
		$path = DIR_ROOT.'/admin/txt/autofill-category-2.txt';
		if(file_exists($path)) {
			$this->data['result']['autofill']['category-list-2'] = file_get_contents($path);
		}
		
		// Категория 3
		$path = DIR_ROOT.'/admin/txt/autofill-category-3.txt';
		if(file_exists($path)) {
			$this->data['result']['autofill']['category-list-3'] = file_get_contents($path);
		}
		
		// Категория 4
		$path = DIR_ROOT.'/admin/txt/autofill-category-4.txt';
		if(file_exists($path)) {
			$this->data['result']['autofill']['category-list-4'] = file_get_contents($path);
		}

        // Формиование вида
        $page = Core_View::load('white', $this->data);
        return $page;
    }
	
    // Получение белого списока
    public function get_list() {
		
		$count = $this->obj['model_white']->cnt();
        $per_page = 50;
		$start_row = Lib_Main::clear_num($this->data['route']['param']['p'] ?? 0, 0);
		if($start_row < 0 || $start_row >= $count) $start_row = 0;

		if($count > 0) {
			$this->data['result']['list'] = $this->obj['model_white']->get(['start_row' => $start_row, 'per_page' => $per_page]);
			if($this->data['result']['list']) {
				foreach($this->data['result']['list'] as &$val) {

					// Количество рефералов
					$val['refs'] = $this->obj['model_users']->ref_count(['sponsor_id' => $val['user_id']]);
					
					// Сумма пополнений по валютам
					$fill = $this->obj['model_fill']->user_all(['user_id' => $val['user_id']]);
					if($fill) {
						foreach($fill as $value) {
							$val['fill'][$value['valute']] = Lib_Main::beauty_number($value['amount'] ?? 0);
						}
					}

					// Сумма выплат по валютам
					$payout = $this->obj['model_payout']->user_all(['user_id' => $val['user_id']]);
					if($payout) {
						foreach($payout as $value) {
							$val['payout'][$value['valute']] = Lib_Main::beauty_number($value['amount'] ?? 0);
						}
					}
					
					// Получение спонсора по ID
					if($val['sponsor_id'] != 0) $val['sponsor'] = $this->obj['model_users']->get_by_id(['id' => $val['sponsor_id']])['login'];
				}
				$this->data['result']['pagination'] = ($count > $per_page) ? Lib_Main::pagination_ajax($count, $per_page, 4, $start_row, '/'.$this->data['config']['site']['admin'].'/white/get_list', 'list', '#white__list') : null;
			}
		}
		$response['white__list'] =  Core_View::load('white_list', $this->data);
		return $response;
    }

	// Добавление пользователя в белый список
	public function add() {

		// Проверка валидности логина
		$login = Lib_Main::clear_str($_POST['login'] ?? '');
		if($login !== ($er = Lib_Validation::login($login))) {
			$response['handler'] = '<script>messager("error", "'.$this->data['lang']['lib--validation'][$er].'");</script>';
			return $response;
		}
		
		// Проверка на существование пользователя
		$user_id = $this->obj['model_users']->id_by_login(['login' => $login]);
		if(!$user_id) {
			$response['handler'] = '<script>messager("error", "Пользователь не зарегистрирован");</script>';
			return $response;
		}

		// Проверка на наличие пользователя в белом списке
		if($this->obj['model_white']->check(['user_id' => $user_id])) {
			$response['handler'] = '<script>messager("error", "Пользователь уже добавлен");</script>';
			return $response;
		}

		// Добавление в белый список
		$this->obj['model_white']->insert(['user_id' => $user_id]);

		$response['handler'] = '<script>process(\'/'.$this->data['config']['site']['admin'].'/white/get_list\', \'list\', \'#white__list\');</script>';
		$response['handler'] .= '<script>messager("success", "Пользователь добавлен в белый список");</script>';
		return $response;
	}
	
	// Пополнение
	public function inout() {
		
		// Получение и обработка данных
		$action = $this->data['route']['param']['action'] ?? '';
		if(empty($action) || !in_array($action, ['fill', 'payout'])) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
            $response['handler'] = Lib_Main::rew_page('errors/data');
            return $response;
		}
		$login = Lib_Main::clear_str($_POST['login'] ?? '');
		$payment = Lib_Main::clear_str($_POST['payment'] ?? '');
		$valute = Lib_Main::clear_str($_POST['valute'] ?? '');
		$amount = $_POST['amount'] ?? '';
		
		if(empty($login)) {
			$response['handler'] = '<script>messager("error", "Введите логин");</script>';
			return $response;
		}
		if(empty($amount)) {
			$response['handler'] = '<script>messager("error", "Введите сумму");</script>';
			return $response;
		}
		$amount = Lib_Main::clear_num($amount, 2);
		if(!$user_id = $this->obj['model_users']->id_by_login(['login' => $login])) {
			$response['handler'] = '<script>messager("error", "Пользователь не зарегистрирован");</script>';
			return $response;
		}
		if(!$white = $this->obj['model_white']->check(['user_id' => $user_id])) {
			$response['handler'] = '<script>messager("error", "Пользователь не в белом списке");</script>';
			return $response;
		}
		if(!isset($this->data['config']['payments'][$payment][$valute]) || $this->data['config']['payments'][$payment][$valute] !== '1') {
			$response['handler'] = '<script>messager("error", "Выбранная платежная система не поддерживает данную валюту");</script>';
			return $response;
		}
		if($this->data['config']['site'][$valute.'_'.$action] !== '1') {
			$response['handler'] = '<script>messager("error", "Данные операции недоступны");</script>';
			return $response;
		}
		if($amount < $this->data['config']['payments'][$payment][$valute.'_min_'.$action]) {
			$response['handler'] = '<script>messager("error", "Сумма ниже минимума");</script>';
			return $response;
		}
		if($amount > $this->data['config']['payments'][$payment][$valute.'_max_'.$action]) {
			$response['handler'] = '<script>messager("error", "Сумма превышает максимум");</script>';
			return $response;
		}
		switch($action) {
			case 'fill':
				$this->obj['model_fill']->insert(['user_id' => $user_id, 'valute' => $valute, 'amount' => $amount, 'payment' => $payment, 'status' => '1', 'sort' => time(), 'wl' => '1', 'datetime' => time()]);
				
				// Сумма пополнений по валютам
				$result = $this->obj['model_fill']->user_all(['user_id' => $user_id]);
				if($result) {
					foreach($result as $val) {
						$fill[$val['valute']] = Lib_Main::beauty_number($val['amount'] ?? 0);
					}
				}
				$response['handler'] = '<script>$("#white__form input[name=amount]").val(""); $("#white_list__str_'.$white['id'].' .fill-'.$valute.'").html("'.Lib_Main::beauty_number($fill[$valute]).'"); messager("success", "Баланс пополнен : '.Lib_Main::beauty_number($amount).' '.strtoupper($valute).' ('.$this->data['config']['payments'][$payment]['name'].')");</script>';
			break;
			case 'payout':
				$this->obj['model_payout']->insert(['user_id' => $user_id, 'valute' => $valute, 'amount' => $amount, 'payment' => $payment, 'status' => '1', 'reason' => '1', 'sort' => time(), 'wl' => '1', 'datetime' => time()]);
				
				// Сумма выплат по валютам
				$result = $this->obj['model_payout']->user_all(['user_id' => $user_id]);
				if($result) {
					foreach($result as $val) {
						$payout[$val['valute']] = Lib_Main::beauty_number($val['amount'] ?? 0);
					}
				}
				$response['handler'] = '<script>$("#white__form input[name=amount]").val(""); $("#white_list__str_'.$white['id'].' .payout-'.$valute.'").html("'.Lib_Main::beauty_number($payout[$valute]).'"); messager("success", "Выплата произведена : '.Lib_Main::beauty_number($amount).' '.strtoupper($valute).' ('.$this->data['config']['payments'][$payment]['name'].')");</script>';
			break;
		}
		return $response;
	}
	
	// Удаление из белого списка
	public function delete() {

		if(!isset($this->data['route']['param']['id'])) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
            $response['handler'] = Lib_Main::rew_page('errors/data');
            return $response;
		}
		$id = Lib_Main::clear_num($this->data['route']['param']['id'], 0);
		if(!$this->obj['model_white']->check_by_id(['id' => $id])) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
            $response['handler'] = Lib_Main::rew_page('errors/data');
            return $response;
		}
		
		// Удаление пользователя
		$this->obj['model_white']->delete(['where' => $id]);

		$response['handler'] = '<script>messager("success", "Пользователь удален из белого списка");</script>';
		$response['handler'] .= '<script>$("#white_list__str_'.$id.'").fadeOut(500);</script>';
		return $response;
	}
	
	// Авторегистрации
	public function autosignup() {
		
		switch($this->data['route']['param']['action']) {
			case 'list':
			
				// Путь к файлу
				$path = DIR_ROOT.'/admin/txt/autosignup.txt';
				
				// Проверка существования файла
				if(file_exists($path)) {
					$file = file($path);
					$count = count($file);
					$per_page = 20;
					$start_row = Lib_Main::clear_num($this->data['route']['param']['p'] ?? 0, 0);
					if($start_row < 0 || $start_row >= $count) $start_row = 0;
					if($count > 0) {
						foreach($file as $val) {
							if(!isset($i)) $i = 0;
							if(!isset($cnt)) $cnt = 0;
							++$i;
							if($i <= $start_row) continue;
							++$cnt;
							if($cnt > $per_page) break;
							list($this->data['result']['list'][$i]['login'], $this->data['result']['list'][$i]['email']) = explode('::', $val);
						}
						$this->data['result']['pagination'] = ($count > $per_page) ? Lib_Main::pagination_ajax($count, $per_page, 4, $start_row, '/'.$this->data['config']['site']['admin'].'/white/autosignup/action/list', 'autosignup_list', '#white__autosignup_list') : null;
					}
				}
				$response['white__autosignup_list'] =  Core_View::load('white_autosignup_list', $this->data);
			break;
			case 'check_enabled':	
				$this->data['result']['timer_name'] = 'autosignup';
				$response['handler'][] = '<script>if(typeof autosignup_timer_id !=="undefined") clearInterval(autosignup_timer_id); if(typeof refresh_timer_autosignup_id != "undefined") clearTimeout(refresh_timer_autosignup_id);</script>';
				if($this->data['config']['automatization']['autosignup']['enabled'] == 0) {
					$response['white__autosignup_timer'] = Core_View::message(['grey' => 'отключен']);
					$response['handler'][] = '<script>$("#white__autosignup_enabled").prop("checked", false); if(typeof autosignup_timer_id != "undefined") clearInterval(autosignup_timer_id);</script>';
				} else {
					$response['white__autosignup_timer'] = Core_View::load('white_timer', $this->data);
					$response['handler'][] = '<script>$("#white__autosignup_enabled").attr("checked", "checked");</script>';
				}
			break;
			case 'enabled':
				$enabled = isset($_POST['enabled']) ? 1 : 0;
				$category = $this->obj['model_settings_category']->check_key(['name' => 'autosignup'])[0]['id'];
				if($enabled == 1) {
					$path = DIR_ROOT.'/admin/txt/autosignup.txt';
					if(!file_exists($path)) {
						$response['handler'][] = '<script>$("#white__autosignup_enabled").prop("checked", false); messager("error", "Отсутствует список");</script>';
						return $response;
					}
					$setting = $this->obj['model_settings']->get_id_by_name(['name' => 'last', 'category' => $category]);
					$last = strtotime(date("Y-m-d H:i")) + 60 + ((mt_rand($this->data['config']['automatization']['autosignup']['min'], $this->data['config']['automatization']['autosignup']['max'])) * 60);
					$this->obj['model_settings']->update(['set' => ['value' => $last], 'where' => $setting]);
					$this->data['config']['automatization']['autosignup']['last'] = $last;
				}
				$setting = $this->obj['model_settings']->get_id_by_name(['name' => 'enabled', 'category' => $category]);
				$this->obj['model_settings']->update(['set' => ['value' => $enabled], 'where' => $setting]);
				
				$this->upd_config();
				
				$this->data['config']['automatization']['autosignup']['enabled'] = $enabled;
				$this->data['route']['param']['action'] = 'check_enabled';
				$response = $this->autosignup();
				return $response;
			break;
			case 'min':
			case 'max':
				$value = $this->data['route']['param']['action'] == 'min' ? $_POST['min'] : $_POST['max'];
				if(!$value) {
					$response['handler'] = '<script>messager("error", "Значение не может быть пустым");</script>';
					return $response;
				}
				if(!isset($this->data['config']['automatization']['autosignup'][$this->data['route']['param']['action']]) || $value == $this->data['config']['automatization']['autosignup'][$this->data['route']['param']['action']]) return;
				$category = $this->obj['model_settings_category']->check_key(['name' => 'autosignup'])[0]['id'];
				$setting = $this->obj['model_settings']->get_id_by_name(['name' => $this->data['route']['param']['action'], 'category' => $category]);
				$this->obj['model_settings']->update(['set' => ['value' => $value], 'where' => $setting]);
				
				$this->upd_config();
				
				$response['handler'] = '<script>messager("success", "Новое значение успешно сохранено");</script>';
				return $response;
			break;
			case 'password':
				$password = Lib_Main::clear_str($_POST['password']);
				$category = $this->obj['model_settings_category']->check_key(['name' => 'autosignup'])[0]['id'];
				$setting = $this->obj['model_settings']->get_id_by_name(['name' => 'password', 'category' => $category]);
				$this->obj['model_settings']->update(['set' => ['value' => $password], 'where' => $setting]);
				
				$this->upd_config();
				
				$response['handler'] = '<script>messager("success", "Пароль успешно сохранен");</script>';
				return $response;
			break;
			case 'sponsor':
				$sponsor = $_POST['sponsor'] ?? '';
				if(!$sponsor || !is_array($sponsor)) {
					$response['handler'] = '<script>messager("error", "Произошла ошибка");</script>';
					return $response;
				}
				$category = $this->obj['model_settings_category']->check_key(['name' => 'autosignup'])[0]['id'];
				$settings = $this->obj['model_settings']->get_by_category(['category' => $category]);
				if(!$settings) {
					$response['handler'] = '<script>messager("error", "Произошла ошибка");</script>';
					return $response;
				}
				foreach($settings as $val) {
					if($val['name'] !== 'sponsor') continue;
					$i = isset($i) ? ++$i : 0;
					if($val['value'] !== $sponsor[$i]) {
						$err = Lib_Validation::login($sponsor[$i]);
						if($sponsor[$i] != $err) {
							$response['handler'] = '<script>messager("error", "'.$this->data['lang']['lib--validation'][$err].'");</script>';
							return $response;
						}
						if(!$this->obj['model_users']->id_by_login(['login' => $sponsor[$i]])) {
							$response['handler'][] = '<script>messager("error", "Пользователь &quot;'.$sponsor[$i].'&quot; не зарегистрирован");</script>';
							return $response;
						}
						$data[$i]['set'] = $sponsor[$i];
						$data[$i]['where'] = $val['id'];
					}
				}
				if(isset($data)) {
					foreach($data as $val) {
						$this->obj['model_settings']->update(['set' => ['value' => $val['value']], 'where' => $val['where']]);
						$response['handler'][] = '<script>messager("success", "Спонсор &quot;'.$sponsor[$i].'&quot; успешно сохранен");</script>';
					}
					return $response;
				}
			break;
			case 'add_sponsor':
				$category = $this->obj['model_settings_category']->check_key(['name' => 'autosignup'])[0]['id'];
				$sort = $this->obj['model_settings']->last_sort() + 1;
				$this->obj['model_settings']->insert(['category' => $category, 'name' => 'sponsor', 'value' => '', 'comment' => 'Спонсор', 'sort' => $sort]);
				
				$this->upd_config();
				
				$this->data['result']['list'][] = '';
				$response['handler'][] = '<script>$("#white__delete_sponsor").before(\''.Lib_Db::escape_string(Core_View::load('white_sponsor_list', $this->data)).'\');</script>';
				$response['handler'][] = '<script>messager("wait", "Введите логин спонсора");</script>';
				return $response;
			break;
			case 'delete_sponsor':
				$category = $this->obj['model_settings_category']->check_key(['name' => 'autosignup'])[0]['id'];
				$settings = $this->obj['model_settings']->get_by_category(['category' => $category]);
				if(!$settings) {
					$response['handler'] = '<script>messager("error", "Произошла ошибка");</script>';
					return $response;
				}
				foreach($settings as $val) {
					if($val['name'] !== 'sponsor') continue;
					$last_id = $val['id'];
				}
				if(!$last_id) {
					$response['handler'] = '<script>messager("error", "Спонсоров нет");</script>';
					return $response;
				}
				$this->obj['model_settings']->delete(['where' => $last_id]);
				
				$this->upd_config();
				
				$response['handler'] = '<script>$("#white__autosignup .controll-panel__item:last").remove(); messager("success", "Спонсор удален");</script>';
				return $response;
			break;
			case 'add_file':
				if(!isset($_FILES['list']['tmp_name'])) {
					$response['handler'] = '<script>messager("error", "Произошла ошибка при загрузке файла");</script>';
					return $response;
				}
				$name = time().'-list-temp.txt';
				$path = DIR_ROOT.'/admin/txt/';
				if(!move_uploaded_file($_FILES['list']['tmp_name'], $path.$name)) {
					$response['handler'] = '<script>messager("error", "Произошла ошибка при загрузке файла");</script>';
					return $response;
				}
				$lines = file($path.$name);
				if(!is_array($lines)) {
					$response['handler'] = '<script>messager("error", "Произошла ошибка при формировании массива");</script>';
					return $response;
				}
				foreach($lines as $val) {
					$i = isset($i) ? ++$i : 1;
					$line = iconv("windows-1251", "utf-8", $val);
					if(!$line = @explode('::', $line)) continue;
					@list($login, $email) = $line;
					if(!$login || !$email) continue;
					
					$login = str_replace(["\r\n", "\r", "\n"], '',  trim($login));
					$err = Lib_Validation::login($login);
					if($login != $err) {
						$response['handler'][] = '<script>messager("error", "'.$this->data['lang']['lib--validation'][$err].' : &quot;'.Lib_Db::escape_string($login).'&quot; ('.$i.' строка)", 10000);</script>';
						continue;
					}
					$email = str_replace(["\r\n", "\r", "\n"], '',  trim($email));
					$err = Lib_Validation::email($email);
					if($email != $err) {
						$response['handler'][] = '<script>messager("error", "'.$this->data['lang']['lib--validation'][$err].' : &quot;'.Lib_Db::escape_string($email).'&quot; ('.$i.' строка)", 10000);</script>';
						continue;
					}
					if($this->obj['model_users']->id_by_login(['login' => $login])) {
						$response['handler'][] = '<script>messager("error", "Логин &quot;'.$login.'&quot; уже зарегистрирован");</script>';
						continue;
					}
					if($this->obj['model_users']->id_by_email(['email' => $email])) {
						$response['handler'][] = '<script>messager("error", "E-mail &quot;'.$email.'&quot; уже зарегистрирован");</script>';
						continue;
					}
					$new[] = $login.'::'.$email.PHP_EOL;
				}
				unlink($path.$name);
				
				file_put_contents($path.'autosignup.txt', $new, FILE_APPEND | LOCK_EX);
				$response['handler'][] = '<script>$("#white__autosignup_file").val(""); process(\'/'.$this->data['config']['site']['admin'].'/white/autosignup/action/list\', \'autosignup_list\', \'#white__autosignup_list\')</script>';
				return $response;
			break;
			case 'delete_list_line':
				$p = Lib_Main::clear_num($this->data['route']['param']['p'] ?? 0, 0);
				$line = $this->data['route']['param']['line'] ?? '';
				if(!$line) {
					$response['handler'] = '<script>messager("error", "Произошла ошибка");</script>';
					return $response;
				}
				$path = DIR_ROOT.'/admin/txt/autosignup.txt';
				if(!file_exists($path)) {
					$response['handler'] = '<script>messager("error", "Произошла ошибка при считывании файла");</script>';
					return $response;
				}
				$file = file($path);
				if(!is_array($file)) {
					$response['handler'] = '<script>messager("error", "Произошла ошибка при считывании файла");</script>';
					return $response;
				}
				unset($file[$line - 1]);
				file_put_contents($path, implode("", $file), LOCK_EX);
				$response['handler'] = '<script>process(\'/'.$this->data['config']['site']['admin'].'/white/autosignup/action/list/p/'.$p.'\', \'autosignup_list\', \'#white__autosignup_list\')</script>';
				return $response;
			break;
			case 'delete_file':
				$path = DIR_ROOT.'/admin/txt/autosignup.txt';
				if(!file_exists($path)) {
					$response['handler'] = '<script>messager("error", "Файла не существует");</script>';
					return $response;
				}
				unlink($path);
				if($this->data['config']['automatization']['autosignup']['enabled'] == '1') {
					$category = $this->obj['model_settings_category']->check_key(['name' => 'autosignup'])[0]['id'];
					$setting = $this->obj['model_settings']->get_id_by_name(['name' => 'enabled', 'category' => $category]);
					$this->obj['model_settings']->update(['set' => ['value' => 0], 'where' => $setting]);
					
					$this->upd_config();
					
					$this->data['config']['automatization']['autosignup']['enabled'] = 0;
					$this->data['route']['param']['action'] = 'check_enabled';
					$response = $this->autosignup();
				}
				$response['handler'][] = '<script>$("#white__autosignup_enabled").prop("checked", false); process(\'/'.$this->data['config']['site']['admin'].'/white/autosignup/action/list\', \'autosignup_list\', \'#white__autosignup_list\')</script>';
				return $response;
			break;
			default:
				Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
				$response['handler'] = Lib_Main::rew_page('errors/data');
			break;
		}
		return $response;
	}
	
	// Автопополнения
	public function autofill() {
		
		switch($this->data['route']['param']['action']) {
			case 'list':
			
				// Путь к файлу
				$path = DIR_ROOT.'/admin/txt/autofill-log.txt';
				
				// Проверка существования файла
				if(file_exists($path)) {
					$file = file($path);
					$count = count($file);
					$per_page = 20;
					$start_row = Lib_Main::clear_num($this->data['route']['param']['p'] ?? 0, 0);
					if($start_row < 0 || $start_row >= $count) $start_row = 0;
					if($count > 0) {
						foreach($file as $val) {
							if(!isset($i)) $i = 0;
							if(!isset($cnt)) $cnt = 0;
							++$i;
							if($i <= $start_row) continue;
							++$cnt;
							if($cnt > $per_page) break;
							list($this->data['result']['list'][$i]['datetime'], $this->data['result']['list'][$i]['text']) = explode('::', $val);
							$this->data['result']['list'][$i]['datetime'] = date($this->data['config']['formats']['datetime'], Lib_Main::clear_num($this->data['result']['list'][$i]['datetime'], 0));
						}
						$this->data['result']['pagination'] = ($count > $per_page) ? Lib_Main::pagination_ajax($count, $per_page, 4, $start_row, '/'.$this->data['config']['site']['admin'].'/white/autofill/action/list', 'autofill_list', '#white__autofill_list') : null;
					}
				}
				$response['white__autofill_list'] =  Core_View::load('white_autofill_list', $this->data);
			break;
			case 'check_enabled':
				$this->data['result']['timer_name'] = 'autofill';
				$response['handler'][] = '<script>if(typeof autofill_timer_id != "undefined") clearInterval(autofill_timer_id); if(typeof refresh_timer_autofill_id != "undefined") clearTimeout(refresh_timer_autofill_id);</script>';
				if($this->data['config']['automatization']['autofill']['enabled'] == 0) {
					$response['white__autofill_timer'] = Core_View::message(['grey' => 'отключен']);
					$response['handler'][] = '<script>$("#white__autofill_enabled").prop("checked", false); if(typeof autofill_timer_id != "undefined") clearInterval(autofill_timer_id);</script>';
				} else {
					$response['white__autofill_timer'] = Core_View::load('white_timer', $this->data);
					$response['handler'][] = '<script>$("#white__autofill_enabled").attr("checked", "checked");</script>';
				}
				return $response;
			break;
			case 'enabled':
				$enabled = isset($_POST['enabled']) ? 1 : 0;
				$category = $this->obj['model_settings_category']->check_key(['name' => 'autofill'])[0]['id'];
				if($enabled == 1) {
					$setting = $this->obj['model_settings']->get_id_by_name(['name' => 'last', 'category' => $category]);
					$last = strtotime(date("Y-m-d H:i")) + 60 + ((mt_rand($this->data['config']['automatization']['autofill']['min'], $this->data['config']['automatization']['autofill']['max'])) * 60);
					$this->obj['model_settings']->update(['set' => ['value' => $last], 'where' => $setting]);
					$this->data['config']['automatization']['autofill']['last'] = $last;
				}
				$setting = $this->obj['model_settings']->get_id_by_name(['name' => 'enabled', 'category' => $category]);
				$this->obj['model_settings']->update(['set' => ['value' => $enabled], 'where' => $setting]);
				
				$this->upd_config();
				
				$this->data['config']['automatization']['autofill']['enabled'] = $enabled;
				$this->data['route']['param']['action'] = 'check_enabled';
				$response = $this->autofill();
				return $response;
			break;
			case 'save':
				if(!isset($_POST['min']) || $_POST['min'] != $this->data['config']['automatization']['autofill']['min']) {
					$value = Lib_Main::clear_num($_POST['min'] ?? 0, 0);
					if($value > $_POST['max'] ?? 0) {
						$response['handler'] = '<script>messager("error", "Минимальное время не может быть больше чем Максимальное время");</script>';
						return $response;
					}
					$data['set']['min'] = $value;
					$response['handler'][] = '<script>$("#white__autofill_form input[name=min]").val("'.$value.'"); messager("success", "Минимальное время успешно сохранено");</script>';
				}
				if(!isset($_POST['max']) || $_POST['max'] != $this->data['config']['automatization']['autofill']['max']) {
					$value = Lib_Main::clear_num($_POST['max'] ?? 0, 0);
					$data['set']['max'] = $value;
					$response['handler'][] = '<script>$("#white__autofill_form input[name=max]").val("'.$value.'"); messager("success", "Максимальное время успешно сохранено");</script>';
				}
				foreach($this->data['config']['valutes'] as $key => $val) {
					if(!$val['fill']) continue;
					if(!isset($_POST[$key]) || $_POST[$key] != $this->data['config']['automatization']['autofill'][$key]) {
						$value = Lib_Main::clear_num($_POST[$key] ?? 0, 0);
						$data['set'][$key] = $value;
						$response['handler'][] = '<script>$("#white__autofill_form input[name='.$key.']").val("'.$value.'"); messager("success", "Вероятность выбора '.strtoupper($key).' успешно сохранена");</script>';
					}
				}
				foreach($this->data['config']['payments'] as $key => $val) {
					if(!$val['enabled']) continue;
					if(!isset($_POST[$key]) || $_POST[$key] != $this->data['config']['automatization']['autofill'][$key]) {
						$value = Lib_Main::clear_num($_POST[$key] ?? 0, 0);
						$data['set'][$key] = $value;
						$response['handler'][] = '<script>$("#white__autofill_form input[name='.$key.']").val("'.$value.'"); messager("success", "Вероятность выбора '.$val['name'].' успешно сохранена");</script>';
					}
				}
				if(!isset($_POST['category1']) || $_POST['category1'] != $this->data['config']['automatization']['autofill']['category1']) {
					$value = Lib_Main::clear_num($_POST['category1'] ?? 0, 0);
					$data['set']['category1'] = $value;
					$response['handler'][] = '<script>$("#white__autofill_form input[name=category1]").val("'.$value.'"); messager("success", "Вероятность выбора категории Small успешно сохранена");</script>';
				}
				if(!isset($_POST['category2']) || $_POST['category2'] != $this->data['config']['automatization']['autofill']['category2']) {
					$value = Lib_Main::clear_num($_POST['category2'] ?? 0, 0);
					$data['set']['category2'] = $value;
					$response['handler'][] = '<script>$("#white__autofill_form input[name=category2]").val("'.$value.'"); messager("success", "Вероятность выбора категории Medium успешно сохранена");</script>';
				}
				if(!isset($_POST['category3']) || $_POST['category3'] != $this->data['config']['automatization']['autofill']['category3']) {
					$value = Lib_Main::clear_num($_POST['category3'] ?? 0, 0);
					$data['set']['category3'] = $value;
					$response['handler'][] = '<script>$("#white__autofill_form input[name=category3]").val("'.$value.'"); messager("success", "Вероятность выбора категории High успешно сохранена");</script>';
				}
				if(!isset($_POST['category4']) || $_POST['category4'] != $this->data['config']['automatization']['autofill']['category4']) {
					$value = Lib_Main::clear_num($_POST['category4'] ?? 0, 0);
					$data['set']['category4'] = $value;
					$response['handler'][] = '<script>$("#white__autofill_form input[name=category4]").val("'.$value.'"); messager("success", "Вероятность выбора категории Top успешно сохранена");</script>';
				}
				$path = DIR_ROOT.'/admin/txt/autofill-category-1.txt';
				$category1list = file_exists($path) ? file_get_contents($path) : '';
				if(isset($_POST['category1list']) && $_POST['category1list'] != $category1list) {
					$category1list = Lib_Main::clear_str($_POST['category1list']);
					file_put_contents($path, $category1list, LOCK_EX);
					$response['handler'][] = '<script>messager("success", "Список категории Small успешно сохранен");</script>';
				}
				$path = DIR_ROOT.'/admin/txt/autofill-category-2.txt';
				$category2list = file_exists($path) ? file_get_contents($path) : '';
				if(isset($_POST['category2list']) && $_POST['category2list'] != $category2list) {
					$category2list = Lib_Main::clear_str($_POST['category2list']);
					file_put_contents($path, $category2list, LOCK_EX);
					$response['handler'][] = '<script>messager("success", "Список категории Medium успешно сохранен");</script>';
				}
				$path = DIR_ROOT.'/admin/txt/autofill-category-3.txt';
				$category3list = file_exists($path) ? file_get_contents($path) : '';
				if(isset($_POST['category3list']) && $_POST['category3list'] != $category3list) {
					$category3list = Lib_Main::clear_str($_POST['category3list']);
					file_put_contents($path, $category3list, LOCK_EX);
					$response['handler'][] = '<script>messager("success", "Список категории High успешно сохранен");</script>';
				}
				$path = DIR_ROOT.'/admin/txt/autofill-category-4.txt';
				$category4list = file_exists($path) ? file_get_contents($path) : '';
				if(isset($_POST['category4list']) && $_POST['category4list'] != $category4list) {
					$category4list = Lib_Main::clear_str($_POST['category4list']);
					file_put_contents($path, $category4list, LOCK_EX);
					$response['handler'][] = '<script>messager("success", "Список категории Top успешно сохранен");</script>';
				}
				if(isset($data['set'])) {
					$category = $this->obj['model_settings_category']->check_key(['name' => 'autofill'])[0]['id'];
					foreach($data['set'] as $key => $val) {
						$setting = $this->obj['model_settings']->get_id_by_name(['name' => $key, 'category' => $category]);
						$this->obj['model_settings']->update(['set' => ['value' => $val], 'where' => $setting]);
					}
					$this->upd_config();
				}
				
				if(!isset($response)) $response['handler'][] = '<script>messager("error", "Изменений не обнаружено");</script>';
				return $response;
			break;
			case 'delete_file':
				$path = DIR_ROOT.'/admin/txt/autofill-log.txt';
				if(!file_exists($path)) {
					$response['handler'] = '<script>messager("error", "Файла не существует");</script>';
					return $response;
				}
				unlink($path);
				$response['handler'][] = '<script>process(\'/'.$this->data['config']['site']['admin'].'/white/autofill/action/list\', \'autofill_list\', \'#white__autofill_list\')</script>';
				return $response;
			break;
			default:
				Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
				$response['handler'] = Lib_Main::rew_page('errors/data');
			break;
		}
		return $response;
	}
	
	// Автовыплаты
	public function autopayout() {
		
		switch($this->data['route']['param']['action']) {
			case 'list':
			
				// Путь к файлу
				$path = DIR_ROOT.'/admin/txt/autopayout-log.txt';
				
				// Проверка существования файла
				if(file_exists($path)) {
					$file = file($path);
					$count = count($file);
					$per_page = 20;
					$start_row = Lib_Main::clear_num($this->data['route']['param']['p'] ?? 0, 0);
					if($start_row < 0 || $start_row >= $count) $start_row = 0;
					if($count > 0) {
						foreach($file as $val) {
							if(!isset($i)) $i = 0;
							if(!isset($cnt)) $cnt = 0;
							++$i;
							if($i <= $start_row) continue;
							++$cnt;
							if($cnt > $per_page) break;
							list($this->data['result']['list'][$i]['datetime'], $this->data['result']['list'][$i]['text']) = explode('::', $val);
							$this->data['result']['list'][$i]['datetime'] = date($this->data['config']['formats']['datetime'], Lib_Main::clear_num($this->data['result']['list'][$i]['datetime'], 0));
						}
						$this->data['result']['pagination'] = ($count > $per_page) ? Lib_Main::pagination_ajax($count, $per_page, 4, $start_row, '/'.$this->data['config']['site']['admin'].'/white/autopayout/action/list', 'autopayout_list', '#white__autopayout_list') : null;
					}
				}
				$response['white__autopayout_list'] = Core_View::load('white_autopayout_list', $this->data);
			break;
			case 'check_enabled':
				$this->data['result']['timer_name'] = 'autopayout';
				$response['handler'][] = '<script>if(typeof autopayout_timer_id != "undefined") clearInterval(autopayout_timer_id); if(typeof refresh_timer_autopayout_id != "undefined") clearTimeout(refresh_timer_autopayout_id);</script>';
				if($this->data['config']['automatization']['autopayout']['enabled'] == 0) {
					$response['white__autopayout_timer'] = Core_View::message(['grey' => 'отключен']);
					$response['handler'][] = '<script>$("#white__autopayout_enabled").prop("checked", false); if(typeof autopayout_timer_id != "undefined") clearInterval(autopayout_timer_id);</script>';
				} else {
					$response['white__autopayout_timer'] = Core_View::load('white_timer', $this->data);
					$response['handler'][] = '<script>$("#white__autopayout_enabled").attr("checked", "checked");</script>';
				}
				return $response;
			break;
			case 'enabled':
				$enabled = isset($_POST['enabled']) ? 1 : 0;
				$category = $this->obj['model_settings_category']->check_key(['name' => 'autopayout'])[0]['id'];
				if($enabled == 1) {
					$setting = $this->obj['model_settings']->get_id_by_name(['name' => 'last', 'category' => $category]);
					$last = strtotime(date("Y-m-d H:i")) + 60 + ((mt_rand($this->data['config']['automatization']['autopayout']['min'], $this->data['config']['automatization']['autopayout']['max'])) * 60);
					$this->obj['model_settings']->update(['set' => ['value' => $last], 'where' => $setting]);
					$this->data['config']['automatization']['autopayout']['last'] = $last;
				}
				$setting = $this->obj['model_settings']->get_id_by_name(['name' => 'enabled', 'category' => $category]);
				$this->obj['model_settings']->update(['set' => ['value' => $enabled], 'where' => $setting]);
				
				$this->upd_config();
				
				$this->data['config']['automatization']['autopayout']['enabled'] = $enabled;
				$this->data['route']['param']['action'] = 'check_enabled';
				$response = $this->autopayout();
				return $response;
			break;
			case 'save':
				if(!isset($_POST['min']) || $_POST['min'] != $this->data['config']['automatization']['autopayout']['min']) {
					$value = Lib_Main::clear_num($_POST['min'] ?? 0, 0);
					if($value > $_POST['max'] ?? 0) {
						$response['handler'] = '<script>messager("error", "Минимальное время не может быть больше чем Максимальное время");</script>';
						return $response;
					}
					$data['set']['min'] = $value;
					$response['handler'][] = '<script>$("#white__autopayout_form input[name=min]").val("'.$value.'"); messager("success", "Минимальное время успешно сохранено");</script>';
				}
				if(!isset($_POST['max']) || $_POST['max'] != $this->data['config']['automatization']['autopayout']['max']) {
					$value = Lib_Main::clear_num($_POST['max'] ?? 0, 0);
					$data['set']['max'] = $value;
					$response['handler'][] = '<script>$("#white__autopayout_form input[name=max]").val("'.$value.'"); messager("success", "Максимальное время успешно сохранено");</script>';
				}
				if(!isset($_POST['percent_min']) || $_POST['percent_min'] != $this->data['config']['automatization']['autopayout']['percent_min']) {
					$value = Lib_Main::clear_num($_POST['percent_min'] ?? 0, 0);
					if($value > $_POST['percent_max'] ?? 0) {
						$response['handler'] = '<script>messager("error", "Минимальное время не может быть больше чем Максимальное время");</script>';
						return $response;
					}
					$data['set']['percent_min'] = $value;
					$response['handler'][] = '<script>$("#white__autopayout_form input[name=percent_min]").val("'.$value.'"); messager("success", "Минимальная выплата успешно сохранена");</script>';
				}
				if(!isset($_POST['percent_max']) || $_POST['percent_max'] != $this->data['config']['automatization']['autopayout']['percent_max']) {
					$value = Lib_Main::clear_num($_POST['percent_max'] ?? 0, 0);
					$data['set']['percent_max'] = $value;
					$response['handler'][] = '<script>$("#white__autopayout_form input[name=percent_max]").val("'.$value.'"); messager("success", "Максимальная выплата успешно сохранена");</script>';
				}
				if(isset($data['set'])) {
					$category = $this->obj['model_settings_category']->check_key(['name' => 'autopayout'])[0]['id'];
					foreach($data['set'] as $key => $val) {
						$setting = $this->obj['model_settings']->get_id_by_name(['name' => $key, 'category' => $category]);
						$this->obj['model_settings']->update(['set' => ['value' => $val], 'where' => $setting]);
					}
					$this->upd_config();
				}
				
				if(!isset($response)) $response['handler'][] = '<script>messager("error", "Изменений не обнаружено");</script>';
				return $response;
			break;
			case 'delete_file':
				$path = DIR_ROOT.'/admin/txt/autopayout-log.txt';
				if(!file_exists($path)) {
					$response['handler'] = '<script>messager("error", "Файла не существует");</script>';
					return $response;
				}
				unlink($path);
				$response['handler'][] = '<script>process(\'/'.$this->data['config']['site']['admin'].'/white/autopayout/action/list\', \'autopayout_list\', \'#white__autopayout_list\')</script>';
				return $response;
			break;
			default:
				Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
				$response['handler'] = Lib_Main::rew_page('errors/data');
			break;
		}
		return $response;
	}
	
	// Обновление файла настроек
	public function upd_config() {
		file_put_contents(DIR_ROOT.'/json/config.json', json_encode($this->obj['core_settings']->get_tree()), LOCK_EX);
	}
}