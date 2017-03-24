<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Controller_Contacts {

	public function __construct($data) {
		$this->data = $data;

		// Подключение моделей
		require_once(DIR_ROOT.'/models/model_fail.php');

		// Определение объектов
		$this->obj['model_fail'] = new Model_Fail();
	}

	// Метод по умолчанию
	public function index() {

		// Формирование вида
		$page = Core_View::load('contacts', $this->data);
		return $page;
	}

	// Отправка сообщения
	public function send() {

		// Получение и обработка имени
		$name = Lib_Main::clear_str($_POST['name']);
		if(!$name) {
			$message['error'][] = $this->data['lang']['contacts']['enter-your-name'];
			$response['contacts__form_error'] = Core_View::message($message);
			return $response;
		}

		// Получение и обработка E-Mail
		$email = $_POST['email'];
		if($email !== ($err = Lib_Validation::email($email))){
			$message['error'][] = $this->data['lang']['lib--validation'][$err];
			$response['contacts__form_error'] = Core_View::message(['error' => [$this->data['lang']['lib--validation'][$err]]]);
			return $response;
		}

		// Получение и обработка текста сообщения
		$text = Lib_Main::clear_str($_POST['message']);
		if(!$text) {
			$response['contacts__form_error'] = Core_View::message(['error' => [$this->data['lang']['contacts']['enter-your-text']]]);
			return $response;
		}

		// Получение ip
		$ip = Lib_Main::get_ip();

		// Проверка времени отправки предыдущего сообщения
		if($this->obj['model_fail']->get_count(['value' => $ip]) > 1){
			$response['contacts__form_error'] = Core_View::message(['error' => [$this->data['lang']['contacts']['try-later']]]);
			return $response;
		}

		// Отправка сообщения
		$text = Core_View::load_mail('contacts', ['message' => nl2br($text), 'subject' => 'Сообщение от '.$name ?? $this->data['user']['login'], 'ip' => Lib_Main::get_ip(), 'email' => $email, 'config' => $this->data['config'], 'lang' => $this->data['lang']]);
		Lib_Main::send_mail(['to' => $this->data['config']['site']['support'], 'to_name' => $this->data['config']['site']['name'], 'from' => $email, 'from_name' => ($name ?? $this->data['user']['login']), 'subject' => $this->data['lang']['contacts']['message-from'].' '.$this->data['config']['site']['name'], 'message' => $text]);

		// Запись в таблицу fail
		$this->obj['model_fail']->insert(['value' => $ip, 'datetime' => time()]);

		$message['success'][] = $this->data['lang']['contacts']['success'];
		$response['contacts__form_error'] = Core_View::message($message);
		$response['handler'] = '<script>$("#contacts__form input[name=email], #contacts__form input[name=name], #contacts__form textarea[name=message]").val("");</script>';
		return $response;
	}
}