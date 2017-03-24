<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Controller_Account {

    public function __construct($data) {
        $this->data = $data;

	    // Закрытый раздел
	    if(!$this->data['user']) exit(Lib_Main::rew_page('login'));

        // Подключение моделей
        require_once(DIR_ROOT.'/models/model_fill.php');
	    require_once(DIR_ROOT.'/models/model_log.php');
		require_once(DIR_ROOT.'/models/model_payout.php');
		require_once(DIR_ROOT.'/models/model_users.php');
		require_once(DIR_ROOT.'/models/model_wallets.php');
        require_once(DIR_ROOT.'/models/model_referral_income.php');

        // Определение объектов
        $this->obj['model_fill'] = new Model_Fill();
		$this->obj['model_log'] = new Model_Log();
		$this->obj['model_payout'] = new Model_Payout();
		$this->obj['model_users'] = new Model_Users();
		$this->obj['model_wallets'] = new Model_Wallets();
        $this->obj['model_referral_income'] = new Model_Referral_Income();
    }

    // Метод по умолчанию
    public function index() {

		// Дата регистрации пользователя
		$this->data['user']['datetime'] = date($this->data['config']['formats']['datetime_full'], $this->data['user']['datetime'] + ($this->data['user']['timezone'] * 60));

		// Сумма пополнения
		$result = $this->obj['model_fill']->user_all(['user_id' => $this->data['user']['id']]);
		if($result) {
			foreach($result as $val) {
				$this->data['result']['fill'][$val['valute']] = Lib_Main::beauty_number($val['amount']);
			}
		}

		// Сумма выплаты
		$result = $this->obj['model_payout']->user_all(['user_id' => $this->data['user']['id']]);
		if($result) {
			foreach($result as $val) {
				$this->data['result']['payout'][$val['valute']] = Lib_Main::beauty_number($val['amount']);
			}
		}

		// Количество рефералов
		$this->data['result']['ref']['count'] = Lib_Main::beauty_number($this->obj['model_users']->ref_count(['sponsor_id' => $this->data['user']['id']]));

		// Доход от рефералов
		$result = $this->obj['model_referral_income']->income(['sponsor_id' => $this->data['user']['id']]);
		if($result) {
			foreach($result as $val) {
				$this->data['result']['referral_income'][$val['valute']] = Lib_Main::beauty_number($val['amount']);
			}
		}

		// Кто пригласил
		$this->data['result']['sponsor'] = $this->obj['model_users']->get_by_id(['id' => $this->data['user']['sponsor_id']]);
		
		// Формирование списка часовых поясов
		$this->data['result']['timezone'] = $this->timezone();

		// Получение кошельков пользователя
		$result = $this->obj['model_wallets']->get(['user_id' => $this->data['user']['id']]);
		if($result) {
			foreach($result as $val) {
				$this->data['result']['wallets'][$val['payment']] = $val['wallet'];
			}
		}

        // Формирование вида
        $page = Core_View::load('account', $this->data);
        return $page;			
    }

	// Сохранение изменений
	public function save() {

		// Добавление кошельков пользователя
		$result = $this->obj['model_wallets']->get(['user_id' => $this->data['user']['id']]);
		if($result) {
			foreach($result as $val) {
				$wallets[$val['payment']] = $val['wallet'];
			}
		}

		// Получение и обработка платежной системы
		foreach($this->data['config']['payments'] as $key => $val) {
			$new_wallet = (isset($_POST[$key])) ? $_POST[$key] : '';
			if(!isset($wallets[$key]) && ($new_wallet !== '')) {
				if(!preg_match($val['check'], trim($new_wallet))) {
					$message['error'][] = $this->data['lang']['account']['wrong-wallet'].' '.$val['name'];
				} else {

					// Получение и обработка кошелька
					$result = $this->obj['model_wallets']->check_wallet(['wallet' => $new_wallet]);
					if($result) {
						Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-account|wallet-selected] '.$val['name'].' [m-account|other-account] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);

						// Отключение инстанта
						$this->obj['model_users']->update(['set' => ['instant' => 7], 'where' => $this->data['user']['id']]);
						$this->obj['model_users']->update(['set' => ['instant' => 7], 'where' => $result['user_id']]);
					}

					// Добавление кошелька пользователя
					$this->obj['model_wallets']->insert(['payment' => $key, 'user_id' => $this->data['user']['id'], 'wallet' => $new_wallet]);

					// Запись в лог о добавлении кошелька
					$this->obj['model_log']->insert(['user_id' => $this->data['user']['id'], 'text' => '[m-account|add-wallet] '.$val['name'].' : '.$new_wallet]);

					$message['success'][] = $this->data['lang']['account']['wallet'].' '.$val['name'].' '.$this->data['lang']['account']['saved'];
					$response['handler'][] = '<script>$("#account__form input[name='.$key.']").parent().after("'.$new_wallet.'").remove();</script>';
				}
			}
		}

		// Получение паролей
		$password_old = trim($_POST['password_old'] ?? '');
		$password_new = trim($_POST['password_new'] ?? '');

		// Проверка ввода паролей
		if($password_old || $password_new) {

			// Проверка старого пароля на ввод данных
			if(!$password_old) {
				$message['error'][] = $this->data['lang']['account']['enter-old-pass'];
			} else {

				// Проверка нового пароля на ввод данных
				if(!$password_new) {
					$message['error'][] = $this->data['lang']['account']['enter-new-pass'];
				} else {

					// Кодирование старого пароля
					$password_old = hash('sha512', $password_old);
					$password_old = strrev($password_old);
					$password_old .= $this->data['config']['site']['pass_key'];

					// Проверка на существование пользователя с таким же логином и паролем
					if(!$this->obj['model_users']->get_by_log_pass(['login' => $this->data['user']['login'], 'password' => $password_old])) {
						$message['error'][] = $this->data['lang']['account']['pass-incorrectly'];

						// Валидация нового пароля
					} elseif($password_new !== ($er = Lib_Validation::password($password_new))) {
						$message['error'][] = $this->data['lang']['lib--validation'][$er];
					} else {

						// Кодирование нового пароля
						$password = $password_new;  // Переменная для отправки нового пароля письмом
						$password_new = hash('sha512', $password_new);
						$password_new = strrev($password_new);
						$password_new .= $this->data['config']['site']['pass_key'];

						// Проверка идентичности паролей
						if($password_old == $password_new) {
							$message['error'][] = $this->data['lang']['account']['same-pass'];
						} else {

							// Создание хеша
							$hash = hash('sha512', $this->data['user']['id'] . $this->data['user']['login'].$password_new.$this->data['config']['site']['pass_key']);

							// Запись данных пользователя в сессию
							$_SESSION['password'] = $password_new;
							$_SESSION['hash'] = $hash;

							// Запись данных пользователя в куки
							if(isset($_COOKIE['login']) && isset($_COOKIE['password']) && isset($_COOKIE['hash'])) {
								setcookie("password", $password_new, time() + 9999999, "/");
								setcookie("hash", $hash, time() + 9999999, "/");
							}

							// Запись нового пароля в базу
							$this->obj['model_users']->update(['set' => ['password' => $password_new], 'where' => $this->data['user']['id']]);

							// Запись в лог
							$this->obj['model_log']->insert(['user_id' => $this->data['user']['id'], 'text' => '[m-account|change-pass]', 'datetime' => time()]);

							// Отправка письма
							$subject = $this->data['lang']['mails--change-password']['subject'];
							$text = Core_View::load_mail('change_password', ['lang' => $this->data['lang'], 'config' => $this->data['config'], 'password' => $password]);
							Lib_Main::send_mail(['to' => $this->data['user']['email'], 'to_name' => $this->data['user']['login'], 'from' => $this->data['config']['site']['support'], 'from_name' => $this->data['config']['site']['name'], 'subject' => $subject, 'message' => $text]);

							// Очистка полей ввода
							$response['handler'][] = '<script>$("#account__form input[name=password_old], #account__form input[name=password_new]").val("")</script>';

							// Создание сообщения
							$message['success'][] = $this->data['lang']['account']['pass-saved'];
						}
					}
				}
			}
		}

		if(!isset($message)) {
			$message['error'][] = $this->data['lang']['account']['no-changes'];
		}
		$response['account__form_error'] = Core_View::message($message);
		return $response;
	}

	// Список временных зон
	private function timezone() {
		return array(-720, -660, -600, -540, -480, -420, -360, -300, -258, -240, -180, -120, -60, 0, 60, 120, 180, 198, 240, 258, 300, 318, 327, 360, 378, 420, 480, 540, 558, 600, 660, 720);
	}
}