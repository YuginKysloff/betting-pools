<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Controller_Check {

	public function __construct($data) {
		$this->data = $data;

		// Подключение моделей
		require_once(DIR_ROOT . '/models/model_users.php');

		// Определение объектов
		$this->obj['model_users'] = new Model_Users();
	}

	// Метод по умолчанию
	public function index() {

		// Проверка входящих данных
		if(!isset($_SESSION['email']) || !isset($_SESSION['check'])) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
			exit(Lib_Main::rew_page('errors/data'));
		}

		// Получение и обработка
		$email = Lib_Main::clear_str($_SESSION['email']);
		$check = Lib_Main::clear_str($_SESSION['check']);
		unset($_SESSION['email']);
		unset($_SESSION['check']);

		if($email != Lib_Validation::email($email)) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
			exit(Lib_Main::rew_page('errors/data'));
		}
		$user = $this->obj['model_users']->get_by_email(['email' => $email]);
		if(!$user) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
			exit(Lib_Main::rew_page('errors/data'));
		}
		if($check != hash('sha512', $user['id'].$user['ip'].$user['datetime'].$this->data['config']['site']['pass_key'])) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
			exit(Lib_Main::rew_page('errors/data'));
		}

		$this->data['result']['email'] = $email;

		// Формирование вида
		$page = Core_View::load('check', $this->data);
		return $page;
	}
}