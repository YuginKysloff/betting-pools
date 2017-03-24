<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Controller_Users {

	public function __construct($data = null) {
		$this->data = $data;
		
		// Уровень доступа
		$this->access = 2;
		
		// Автологин
		$this->auto_login();
		
		// Подключение моделей
		require_once(DIR_ROOT.'/models/model_black_login.php');
		require_once(DIR_ROOT.'/models/model_fail.php');
		require_once(DIR_ROOT.'/models/model_log.php');
		require_once(DIR_ROOT.'/models/model_multi.php');
		require_once(DIR_ROOT.'/models/model_referral_income.php');
		require_once(DIR_ROOT.'/models/model_users.php');
		require_once(DIR_ROOT.'/models/model_warning.php');
		require_once(DIR_ROOT.'/models/model_white.php');
		require_once(DIR_ROOT.'/models/model_wallets.php');
		
		// Определение объектов
		$this->obj['model_black_login'] = new Model_Black_Login();
		$this->obj['model_fail'] = new Model_Fail();
		$this->obj['model_log'] = new Model_Log();
		$this->obj['model_multi'] = new Model_Multi();
		$this->obj['model_users'] = new Model_Users();
		$this->obj['model_warning'] = new Model_Warning();
		$this->obj['model_white'] = new Model_White();
		$this->obj['model_wallets'] = new Model_Wallets();
	}

	// Автологин
	protected function auto_login() {
		
        if(isset($_COOKIE['login']) && !isset($_SESSION['login'])) {
            $_SESSION['login'] = $_COOKIE['login'];
        }
        if(isset($_COOKIE['password']) && !isset($_SESSION['password'])) {
            $_SESSION['password'] = $_COOKIE['password'];
        }
        if(isset($_COOKIE['hash']) && !isset($_SESSION['hash'])) {
            $_SESSION['hash'] = $_COOKIE['hash'];
        }
        $this->logged_in = isset($_SESSION['login']) && isset($_SESSION['password']) && isset($_SESSION['hash']) ? true : false;
    }

	// Извлечение пользователя из базы
	public function get_user() {
		
		// Проверка автологина
		if($this->logged_in == false) return false;

		// Подготовка и очистка данных
		$login = Lib_Main::clear_str($_SESSION['login'] ?? '');
		$password = Lib_Main::clear_str($_SESSION['password'] ?? '');

		// Получение пользователя
		if(!$user = $this->obj['model_users']->get_by_log_pass(['login' => $login, 'password' => $password])) {
			throw new Exception(Lib_Main::rew_page('errors/data'));
		}
		
		// Хеш и проверка данных
		$hash = hash('sha512', $user['id'].$user['login'].$user['password'].$this->data['config']['site']['pass_key']);
		if($hash != $_SESSION['hash']) {
			throw new Exception(Lib_Main::rew_page('errors/data'));
		}
		
		// Проверка уровеня доступа
		if($user['access'] == '1') {
			throw new Exception(Lib_Main::rew_page('errors/blocked'));
		}
		if($user['access'] < $this->access) {
			throw new Exception(Lib_Main::rew_page('errors/access'));
		}

		// Получение IP
		$ip = Lib_Main::get_ip();

		// Проверка на новый IP
		if(isset($user['check_ip']) && $user['check_ip']) {
			if($ip != $user['ip']) {
				Core_Security::dest_data();
				throw new Exception(Lib_Main::rew_page('check-ip'));
			}
		}

		// Получение языка
		$lang = Lib_Lang::get();
		
		// Проверка изменений языка
		if($user['lang'] != $lang) $data['set']['lang'] = $user['lang'] = $lang;

		// Проверка последней активности
		if(time() - $user['activity'] > 900) $data['set']['activity'] = $user['activity'] = time();

		// Присвоение нового IP
		if($ip != $user['ip']) {
			$data['set']['last_ip'] = $user['last_ip'] = $user['ip'];
			$data['set']['ip'] = $user['ip'] = $ip;
		}

		// Обновление данных пользователя
		if(isset($data['set'])) {
			$this->obj['model_users']->update(['set' => $data['set'], 'where' => $user['id']]);
		}
		return $user;
	}

	// Регистрация нового пользователя
	public function signup() {

		// Получение ID блока для вывода результата
		$result_id = $this->data['route']['param']['result_id'] ?? '';

		// Если пользователь авторизирован перенаправляем
		if($this->logged_in) {
			$response['handler'] = Lib_Main::rew_page('profile');
			return $response;
		}

		// Проверка доступности регистрации
		if(!$this->data['config']['site']['signup']) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['users']['signup-close']]]);
			return $response;
		}

		// Проверка и обработка логина
		$login = $_POST['login'] ?? '';
		$err = Lib_Validation::login($login);
		if($login != $err) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['lib--validation'][$err]]]);
			return $response;
		}

		// Проверка и обработка E-Mail
		$email = $_POST['email'] ?? '';
		$err = Lib_Validation::email($email);
		if($email != $err) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['lib--validation'][$err]]]);
			return $response;
		}

		// Проверка и обработка пароля
		$password = $_POST['password'] ?? '';
		$err = Lib_Validation::password($password);
		if($password != $err) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['lib--validation'][$err]]]);
			return $response;
		}

		// Проверка на запрещенные логины
		if(preg_match("/admin|test|moder/i", $login) || $this->obj['model_black_login']->check(['login' => $login])) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['users']['login-forbidden']]]);
			return $response;
		}

		// Проверка логина на занятость
		if($this->obj['model_users']->id_by_login(['login' => $login])) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['users']['login-busy']]]);
			return $response;
		}

		// Проверка E-mail на занятость
		if($this->obj['model_users']->id_by_email(['email' => $email])) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['users']['email-busy']]]);
			return $response;
		}
        
        // Валидация кошельков
        if(isset($_POST['wallet'])) {
            foreach($_POST['wallet'] as $key => $val) {
                if(!$val) continue;
                (preg_match($this->data['config']['payments'][$key]['check'], trim($val))) ? $wallets[$key] = trim($val) : $message['error'][] = $this->data['lang']['users']['wrong-wallet'].' '.$this->data['config']['payments'][$key]['name'].' '.$val;
            }
        }

		// Кодирование пароля
		$pass = $password; // Переменная для отправки пароля письмом при регистрации
        $password = hash('sha512',$password);
        $password = strrev($password);
        $password .= $this->data['config']['site']['pass_key'];

		// Определение спонсора
		if(isset($_COOKIE['sponsor'])) {
			$sponsor = Lib_Main::clear_str($_COOKIE['sponsor']);
			if(strlen($sponsor) >= 3) {
				$id = substr($sponsor, 1, -1);
				if($result = $this->obj['model_users']->get_by_id(['id' => $id])) {
					$sponsor_id = $result['id'];
				}
			}
		}

		// Источник перехода
        if(isset($_COOKIE['url'])) {
            $url = Lib_Main::clear_str($_COOKIE['url']);
        } else {
            if(!preg_match("/".$_SERVER['SERVER_NAME']."/i", $_SERVER['HTTP_REFERER'])){
                $url = Lib_Main::clear_str($_SERVER['HTTP_REFERER']);
            } else {
                $url = '';
            }
        }

		// Временная зона
		$timezone = 0;
				if(isset($_COOKIE['timezone'])) {
		            $timezone = Lib_Main::clear_num($_COOKIE['timezone'], 0);
		            if($timezone > 720 || $timezone < -720) $timezone = 0;
		        } else {
		            $timezone = 0;
		        }

		// Добавление пользователя в базу данных
		$link = Lib_Main::generate_num(2);
		$ip = Lib_Main::get_ip();
		$user_id = $this->obj['model_users']->insert(['login' => $login, 'password' => $password, 'email' => $email, 'sponsor_id' => ($sponsor_id ?? 0), 'rcb' => $this->data['config']['marketing']['rcb'], 'link' => $link, 'url' => $url, 'ip' => $ip, 'last_ip' => $ip, 'timezone' => $timezone, 'lang' => Lib_Lang::get(), 'activity' => time(), 'datetime' => time()]);

		// Создание хеша
		$hash = hash('sha512', $user_id.$login.$password.$this->data['config']['site']['pass_key']);

		// Запись события в лог файл
		$this->obj['model_log']->insert(['user_id' => $user_id, 'text' => '[m-users|signup]', 'datetime' => time()]);
		
        // Добавление кошельков пользователя
        if(isset($wallets)) {
           foreach($wallets as $key => $val) {
                    $this->obj['model_wallets']->insert(['user_id' => $user_id, 'payment' => $key, 'wallet' => $val]);
                    $this->obj['model_log']->insert(['user_id' => $user_id, 'text' => '[m-users|add-wallet] '.$key.' : '.$val, 'datetime' => time()]);
           }
        }

		// Запись данных в сессию и куки
		$this->set_data(['login' => $login, 'password' => $password, 'hash' => $hash]);

		// Отправка письма новому пользователю
		$subject = $this->data['lang']['mails--signup']['signup'].' '.$this->data['config']['site']['name'];
		$text = Core_View::load_mail('signup', ['lang' => $this->data['lang'], 'config' => $this->data['config'], 'login' => $login, 'pass' => $pass]); 
		Lib_Main::send_mail(['to' => $email, 'to_name' => $login, 'from' => $this->data['config']['site']['support'], 'from_name' => $this->data['config']['site']['name'], 'subject' => $subject, 'message' => $text]);

		// Страница перехода
		$response['handler'] = Lib_Main::rew_page('profile');

		// Формирование зашифрованного ID пользователя
		$multi = substr($link, 0, 1).$user_id.substr($link, -1);

		// Проверка на мультиаккаунты
		$this->multi(['user_id' => $user_id, 'multi_id' => $multi]);

		// Вывод результатов
		return $response;
	}

	// Авторизация пользователя
	public function login() {

		// Получение ID блока для вывода результата
		$result_id = $this->data['route']['param']['result_id'] ?? '';

		// Если пользователь авторизирован перенаправляем
		if($this->logged_in) {
			$response['handler'] = Lib_Main::rew_page('profile');
			return $response;
		}

		// Определяем IP
		$ip = Lib_Main::get_ip();

		// Проверка количества неудачных попыток
		if($this->obj['model_fail']->get_count(['value' => $ip]) >= 3) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['users']['fail-data']]]);
			return $response;
		}

		// Проверка и обработка логина
		$login = $_POST['login'] ?? '';
		if($login != ($err = Lib_Validation::login($login))) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['lib--validation'][$err]]]);
			return $response;
		}

		// Проверка и обработка пароля
		$password = $_POST['password'] ?? '';
		if($password != ($err = Lib_Validation::password($password))) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['lib--validation'][$err]]]);
			return $response;
		}

		// Кодирование пароля
        $password = hash('sha512',$password);
        $password = strrev($password);
        $password .= $this->data['config']['site']['pass_key'];

		// Извлекаем пользователя из базы
		if(!$user = $this->obj['model_users']->get_by_log_pass(['login' => $login, 'password' => $password])) {
			$this->obj['model_fail']->insert(['value' => $ip, 'datetime' => time()]);
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['users']['invalid-data']]]);
			return $response;
		}

		// Проверка на блокировку аккаунта
		if($user['access'] < 2) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['users']['account-blocked']]]);
			return $response;
		}

		// Проверка IP
		if($user['check_ip']) {
			if($ip != $user['ip']) {

				// Запись E-Mail в сессию
				$_SESSION['email'] = $user['email'];

				// Запись проверочного кода в сессию
				$_SESSION['check'] = $check = hash('sha512', $user['id'].$user['ip'].$user['datetime'].$this->data['config']['site']['pass_key']);

				// Отправка письма с кодом, если прошло менее ~ 15 минут
				if(!$this->obj['model_fail']->get_count(['value' => $user['email']])) {

					// Формирование проверочного кода
					$check = substr($check, 50, 15);

					// Отправка письма с PIN-кодом
					$subject = $this->data['lang']['mails--check-ip']['check-subject'];
					$text = Core_View::load_mail('check_ip', ['lang' => $this->data['lang'], 'config' => $this->data['config'], 'pin_code' => $check]);
					Lib_Main::send_mail(['to' => $user['email'], 'to_name' => $user['login'], 'from' => $this->data['config']['site']['support'], 'from_name' => $this->data['config']['site']['name'], 'subject' => $subject, 'message' => $text]);
				}

				// Вывод результатов
				$response['handler'] = Lib_Main::rew_page('check');
				return $response;
			}
		}

		// Создание хеша
		$hash = hash('sha512', $user['id'].$login.$password.$this->data['config']['site']['pass_key']);

		// Запись данных в сессию и куки
		$this->set_data(['login' => $login, 'password' => $password, 'hash' => $hash, 'remember' => ($_POST['remember'] ?? '')]);

		// Страница перехода
		$response['handler'] = Lib_Main::rew_page('profile');

		// Формирование зашифрованного ID пользователя
		$multi = substr($user['link'], 0, 1).$user['id'].substr($user['link'], -1);

		// Проверка на мультиаккаунты
		$this->multi(['user_id' => $user['id'], 'multi_id' => $multi]);

		// Вывод результатов
		return $response;
	}

	// Восстановление доступа (отправка письма)
	public function lost_pass() {

		// Получение ID блока для вывода результата
		$result_id = $this->data['route']['param']['result_id'] ?? '';

		// Если пользователь атвторизирован перенаправляем
		if($this->logged_in) {
			$response['handler'] = Lib_Main::rew_page('account');
			return $response;
		}

		// Проверка и обработка E-Mail
		$email = $_POST['email'];
		if($email != ($err = Lib_Validation::email($email))) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['lib--validation'][$err]]]);
			return $response;
		}

		// Получение пользователя из базы
		if(!$user = $this->obj['model_users']->get_by_email(['email' => $email])) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['users']['email-not-reg']]]);
			return $response;
		}

		// Проверка на блокировку аккаунта
		if($user['access'] < 2) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['users']['account-blocked']]]);
			return $response;
		}

		// Проверка последней отправки письма
		if($this->obj['model_fail']->get_count(['value' => $email]) > 0) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['users']['last-send']]]);
			return $response;
		}

		// Запись отправки письма в таблицу
		$this->obj['model_fail']->insert(['value' => $email, 'datetime' => time()]);

		// Отправка письма
		$link = $this->data['config']['site']['protocol'].'://'.$this->data['config']['site']['domain'].'/lost/'.substr($user['link'], 0, 1).$user['id'].substr($user['link'], -1).'/'.hash('sha512', $user['id'].$user['login'].$user['password'].$this->data['config']['site']['pass_key']);
		$activate = '<a href="'.$link.'">'.$link.'</a>';
		$subject = $this->data['lang']['mails--lost']['lost-subject'].' '.$this->data['config']['site']['name'];
		$text = Core_View::load_mail('lost', ['activate' => $activate, 'config' => $this->data['config'], 'lang' => $this->data['lang']]);
		Lib_Main::send_mail(['to' => $email, 'to_name' => $user['login'], 'from' => $this->data['config']['site']['support'], 'from_name' => $this->data['config']['site']['name'], 'subject' => $subject, 'message' => $text]);

		$message['success'][] = $this->data['lang']['users']['recover-link'];
		$response['handler'] = '<script>$("#lost__form input[name=email]").val("");</script>';
		$response[$result_id] = Core_View::message($message);
		return $response;
	}

	// Восстановление доступа (проверка результатов)
	public function lost_pass_act() {

		// Проверка автологина
		if(!isset($this->logged_in)) $this->auto_login();

		// Если пользователь атвторизирован перенаправляем
		if($this->logged_in) {
			exit(Lib_Main::rew_page('profile'));
		}

		// Обработка ссылки
		$link = Lib_Main::clear_str($this->data['route']['link'] ?? '');
		if(strlen($link) < 3) {
			exit(Lib_Main::rew_page('errors/data'));
		}

		// Получение ID пользователя
		$user_id = substr($link, 1, -1);

		// Получение пользователя
		if(!$user = $this->obj['model_users']->get_by_id(['id' => $user_id])) {
			exit(Lib_Main::rew_page('errors/data'));
		}

		// Проверка соответствия данных
		if($link != (substr($link, 0, 1).$user['id'].substr($link, -1)) || $this->data['route']['key'] != (hash('sha512', $user['id'].$user['login'].$user['password'].$this->data['config']['site']['pass_key']))) {
			exit(Lib_Main::rew_page('errors/data'));
		}
		
		// Генерация нового пароля
		$pass = Lib_Main::generate_password(10);
		$password = hash('sha512', $pass);
		$password = strrev($password);
		$password .= $this->data['config']['site']['pass_key'];

		// Создание хеша
		$hash = hash('sha512', $user['id'].$user['login'].$password.$this->data['config']['site']['pass_key']);
		
		// Обновление данных пользователя
		$this->obj['model_users']->update(['set' => ['password' => $password], 'where' => $user['id']]);
		
		// Запись данных в сессию и куки
		$this->set_data(['login' => $user['login'], 'password' => $password, 'hash' => $hash]);
		
		// Отправка письма с новым паролем
		$subject = $this->data['lang']['mails--new-password']['pass-subject'].' '.$this->data['config']['site']['name'];
		$text = Core_View::load_mail('new_password', ['lang' => $this->data['lang'], 'config' => $this->data['config'], 'password' => $pass]);
		Lib_Main::send_mail(['to' => $user['email'], 'to_name' => $user['login'], 'from' => $this->data['config']['site']['support'], 'from_name' => $this->data['config']['site']['name'], 'subject' => $subject, 'message' => $text]);

		$message['success'][] = $this->data['lang']['users']['access-restored'];
		$_SESSION['message'] = Core_View::message($message);
		exit(Lib_Main::rew_page('profile'));
	}

	// Партнёрская программа
	public function sponsor($sponsor, $c_user = null) {

		// Обработка спонсора
		if(!$sponsor = Lib_Main::clear_str($sponsor)) return;

		// Получение ID спонсора
		if(preg_match("/[a-zA-Z]+/", $sponsor)) {
			$id = $this->obj['model_users']->id_by_login(['login' => $sponsor]);
		} else {
			if(strlen($sponsor) >= 3) {
				$id = substr($sponsor, 1, -1);
			}
		}

		// Проверка существования ID
		if(isset($id) && $id) {

			// Получение пользователя по ID
			$user = $this->obj['model_users']->get_by_id(['id' => $id]);

			// Проверка авторизации и совпадения ID
			if($c_user && $c_user['id'] == $user['id']) return;

			// Добавление перехода и запись спонсора
			if($user) {
				$this->obj['model_users']->update(['set' => ['visit' => ++$user['visit']], 'where' => $id]);
				$sponsor = substr($user['link'], 0, 1).$user['id'].substr($user['link'], -1);
				setcookie("sponsor", $sponsor, time()+9999999, '/');
			}
		}
	}

	// Определяем источник перехода
	public function url() {
		if(isset($_SERVER['HTTP_REFERER'])) {
			if (!preg_match("/".$_SERVER['SERVER_NAME']."/i", $_SERVER['HTTP_REFERER'])) {
				setcookie("url", $_SERVER['HTTP_REFERER'], time()+9999999, '/');
			}
		}
	}

	// Смена языка
	public function change_lang() {
		$response['handler'] = Lib_Lang::set($this->data['route']['param']['lang']);
		return $response;
	}

	// Смена аватара
	public function avatar() {

		// Подключение библиотеки
		require_once(DIR_ROOT.'/lib/lib_upload.php');

		// Формирование имени файла
		$name = $this->data['user']['id'].Lib_Main::generate_password(8);

		// Загрузка изображения
		$result = Lib_Upload::image(['image' => $_FILES[$this->data['route']['param']['input']], 'name' => $name, 'path' => DIR_ROOT.'/download/images/avatar/', 'width' => 510, 'height' => 510, 'ext' => 'jpg']);

		// Проверка результатов
		if(is_array($result)) {
			if($this->data['user']['avatar']) {
				if(file_exists(DIR_ROOT.'/download/images/avatar/'.$this->data['user']['avatar'])) {
					unlink(DIR_ROOT.'/download/images/avatar/'.$this->data['user']['avatar']);
				}
			}

			// Обновление пути
			$this->obj['model_users']->update(['set' => ['avatar' => $result['avatar']], 'where' => $this->data['user']['id']]);
			$response['handler'] = '<script>$(".group-avatar-img").attr("src", "/download/images/avatar/'.$result['avatar'].'");</script>';
			$message['success'][] = $this->data['lang']['users']['save-avatar'];

			// Добавление мини аватара
			if($this->data['user']['avatar']) {
				if(file_exists(DIR_ROOT.'/download/images/avatar/min/'.$this->data['user']['avatar'])) {
					unlink(DIR_ROOT.'/download/images/avatar/min/'.$this->data['user']['avatar']);
				}
			}
			Lib_Upload::imageresize(['infile' => DIR_ROOT.'/download/images/avatar/'.$result['avatar'], 'outfile' => DIR_ROOT.'/download/images/avatar/min/'.$result['avatar'], 'width' => 80, 'height' => 80, 'ext' => 'jpg']);

			$response['handler'][] = '<script>$(".group-avatar-img").attr("src", "/download/images/avatar/min/'.$result['avatar'].'");</script>';
			$response['handler'][] = '<script>messager("'.$this->data['lang']['users']['save-avatar'].'", "success")</script>';
		} else {
			$response['handler'][] = '<script>$(".group-avatar-img").attr("src", "/'.$this->data['config']['site']['admin'].'/views/default/img/load-avatar-error.jpg")</script>';
			$response['handler'][] = '<script>messager("'.$this->data['lang']['lib--upload'][$result].'")</script>';
		}

		// Вывод результатов
		return $response;
	}
	
	// Выход
	public function logout() {
		Core_Security::dest_data();
		$response['handler'] = Lib_Main::rew_page();
		return $response;
	}
	
	// Начисления по партнерской программе
	public static function refsys($data) {
        if($data['user']['sponsor_id'] && $data['amount'] > 0) {
			$obj['model_log'] = new Model_Log();
			$obj['model_referral_income'] = new Model_Referral_Income();
			$obj['model_users'] = new Model_Users();
            $sponsor = $obj['model_users']->get_by_id(['id' => $data['user']['sponsor_id']]);
            if($sponsor) {
                $amount = Lib_Main::clear_num($data['amount'] * ($sponsor['rcb'] / 100), 2);
				if($amount > 0) {

					// Создание записи реферального вознаграждения спонсору
					$obj['model_referral_income']->insert(['user_id' => $data['user']['id'], 'sponsor_id' => $sponsor['id'], 'valute' => $data['valute'], 'amount' => $amount, 'payment' => $data['payment'], 'datetime' => time()]);

					// Обновление баланса спонсора
					$obj['model_users']->update(['set' => [$data['valute'] => Lib_Main::clear_num($sponsor[$data['valute']] + $amount)], 'where' => $sponsor['id']]);

		        	// Запись в лог
					$obj['model_log']->insert(['user_id' => $sponsor['id'], 'text' => '[m-users|refsys-fee] '.$data['user']['login'].' : <span class="g-currency-'.$data['valute'].'">'.Lib_Main::beauty_number($amount).'</span>', 'control' => $data['valute'].'='.($sponsor[$data['valute']] + $amount), 'datetime' => time()]);
				}
            }
        }
	}

	// Подтверждение входа с нового IP
	public function confirm_ip() {

		// Получение ID блока для вывода результата
		$result_id = $this->data['route']['param']['result_id'] ?? '';

		// Проверка и обработка E-Mail
		$email = Lib_Main::clear_str($_POST['email'] ?? '');
		if(!$email) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.($this->data['user']['login'] ?? '#empty').' &#8594 '.__FILE__.' ('.__LINE__.')']);
			$response['handler'] = Lib_Main::rew_page('errors/data');
			return $response;
		}

		// Проверка и обработка кода
		$code = Lib_Main::clear_str($_POST['code'] ?? '');
		if(!$code) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['users']['verif-code']]]);
			return $response;
		}

		// Получение пользователя по E-mail
		if(!$user = $this->obj['model_users']->get_by_email(['email' => $email])) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.($this->data['user']['login'] ?? '#empty').' &#8594 '.__FILE__.' ('.__LINE__.')']);
			$response['handler'] = Lib_Main::rew_page('errors/data');
			return $response;
		}

		// Проверка пин-кода
		$check = hash('sha512', $user['id'].$user['ip'].$user['datetime'].$this->data['config']['site']['pass_key']);
		$check = substr($check, 50, 15);
		if($check != $code) {
			$response[$result_id] = Core_View::message(['error' => [$this->data['lang']['users']['wrong-code']]]);
			return $response;
		}

		// Создание хеша
		$hash = hash('sha512', $user['id'].$user['login'].$user['password'].$this->data['config']['site']['pass_key']);

		// Запись данных в сессию и куки
		$this->set_data(['login' => $user['login'], 'password' => $user['password'], 'hash' => $hash]);

		// Определение IP
		$ip = Lib_Main::get_ip();

		// Обновление IP
		$this->obj['model_users']->update(['set' => ['ip' => $ip, 'last_ip' => $user['last_ip']], 'where' => $user['id']]);

		// Формирование зашифрованного ID пользователя
		$multi = substr($user['link'], 0, 1).$user['id'].substr($user['link'], -1);

		// Проверка на мультиаккаунты
		$this->multi(['user_id' => $user['id'], 'multi_id' => $multi]);

		// Вывод результатов
		$response['handler'] = Lib_Main::rew_page('profile');
		return $response;
	}

	// Запись данных в сессию и куки
	public function set_data($data) {

		// Проверка существования необходимых данных
		if(!isset($data['login']) || !isset($data['password']) || !isset($data['hash'])) return false;

		// Запись данных пользователя в сессию
		$_SESSION['login'] = $data['login'];
		$_SESSION['password'] = $data['password'];
		$_SESSION['hash'] = $data['hash'];

		// Запись в куки
		if(!isset($data['remember']) || !$data['remember']) return true;
		setcookie('login', $data['login'], time()+9999999, '/');
		setcookie('password', $data['password'], time()+9999999, '/');
		setcookie('hash', $data['hash'], time()+9999999, '/');

		// Возврат результата
		return true;
	}

	// Проверка на мультиаккаунты
	public function multi($data) {

		// Проверка доступности модуля
		if(!$this->data['config']['site']['multi']) return;

		// Проверка на белый список
		if($this->obj['model_white']->check(['user_id' => $data['user_id']])) {
			setcookie('multi', $data['multi_id'], time()+9999999, '/');
			return;
		}

		// Проверка на мультиаккаунты
		if(isset($_COOKIE['multi'])) {
			$multi = Lib_Main::clear_str($_COOKIE['multi']);
			if(strlen($multi) >= 3) {
				$multi = substr($multi, 1, -1);
				if($multi != $data['user_id']) {

					// Получения пользователя по первому мультиаккаунту
					$first = $this->obj['model_users']->get_by_id(['id' => $multi]);
					if($first) {

						// Получение группы по первому мультиаккаунту
						$first_multi = $this->obj['model_multi']->check(['user_id' => $first['id']]);

						// Получение группы по второму мультиаккаунту
						$second_multi = $this->obj['model_multi']->check(['user_id' => $data['user_id']]);

						// Запись и отключение инстанта, если нет первого или второго мультиаккаунта в таблице
						if(!$first_multi || !$second_multi) {

							// Запись в таблицу безопасности
							$this->obj['model_warning']->insert(['category' => 2, 'user_id' => $data['user_id'], 'ip' => Lib_Main::get_ip(), 'text' => '[m-users|multi-detected] : '.$first['login'].' &#8594 '.__FILE__.' ('.__LINE__.')', 'datetime' => time()]);

							// Формирование группы
							if($first_multi) {
								$grouping = $first_multi['grouping'];
							} else {
								if($second_multi) {
									$grouping = $second_multi['grouping'];
								} else {
									$grouping = $this->obj['model_multi']->max_grouping() + 1;
								}
							}

							// Запись и отключение инстанта первого мультиаккаунта
							if(!$first_multi) {
								$this->obj['model_multi']->insert(['user_id' => $first['id'], 'grouping' => $grouping]);
								$this->obj['model_users']->update(['set' => ['instant' => 6], 'where' => $first['id']]);
							}

							// Запись и отключение инстанта второго мультиаккаунта
							if(!$second_multi) {
								$this->obj['model_multi']->insert(['user_id' => $data['user_id'], 'grouping' => $grouping]);
								$this->obj['model_users']->update(['set' => ['instant' => 6], 'where' => $data['user_id']]);
							}
						}

						// Если присутствует первый и второй мультиаккаунт, и группа отлличается - добавляем всю группу второго к первому
						if($first_multi && $second_multi && $first_multi['grouping'] != $second_multi['grouping']) {
							$list = $this->obj['model_multi']->get_by_grouping(['grouping' => $second_multi['grouping']]);
							if($list) {
								foreach($list as $val) {
									$this->obj['model_multi']->update(['set' => ['grouping' => $first_multi['grouping']], 'where' => $val['id']]);
								}
							}
						}
					}
				}
			}
			return;
		}
		setcookie('multi', $data['multi_id'], time()+9999999, '/');
	}
}