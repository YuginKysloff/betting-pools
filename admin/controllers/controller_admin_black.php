<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Controller_Admin_Black {

    public function __construct($data) {
        $this->data = $data;

        // Подключение моделей
        require_once(DIR_ROOT.'/models/model_black_ip.php');
        require_once(DIR_ROOT.'/models/model_black_login.php');

        // Определение объектов
        $this->obj['model_black_ip'] = new Model_Black_Ip();
        $this->obj['model_black_login'] = new Model_Black_Login();
    }

    public function index() {

        // Формирование вида
        $page = Core_View::load('black', $this->data);
        return $page;
    }

    // Получение общего списка
    public function get_list() {

        // Получение списка заблокированных ip и логинов
        $this->data['black_ip'] = $this->obj['model_black_ip']->get();
        $this->data['black_login'] = $this->obj['model_black_login']->get();

        $response['black__list'] = Core_View::load('black_list', $this->data);
        return $response;
    }
	
	// Получение логина или ip для добавления или удаления из черного списка
	public function action() {

		// Получение данных
		$category = Lib_Main::clear_str($_POST['category']) ?? '';
		$value = Lib_Main::clear_str($_POST['value']) ?? '';

        // Проверка на правильность переданных параметров
        if((($method = $this->data['route']['param']['method']) !== 'add' && $method !== 'delete') || empty($category)) {
            Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
            $response['handler'] = Lib_Main::rew_page('errors/data');
            return $response;
        }

		if($value == '') {
            $response['handler'] = '<script>messager("error","Введите значение");</script>';
            return $response;
		}

		// Проверка и обработка логина
		if($category == 'login') {
			if($value !== ($er = Lib_Validation::login($value))) {
                $response['handler'] = '<script>messager("error", "'.$this->data['lang']['lib--validation'][$er].'");</script>';
                return $response;
			} else {
				$black_login = $this->obj['model_black_login']->check(['login' => $value]);
				if($this->data['route']['param']['method'] == 'add') {
					if($black_login) {
						$response['handler'] = '<script>messager("error", "Логин уже в списке");</script>';
						return $response;
					}
					$this->obj['model_black_login']->insert(['login' => $value]);
					$response['handler'] = '<script>messager("success", "Логин '.$value.' добавлен");</script>';
				} else {
					if(!$black_login) {
						$response['handler'] = '<script>messager("error", "Логин не обнаружен");</script>';
						return $response;
					}
					$this->obj['model_black_login']->delete(['where' => $black_login['id']]);
					$response['handler'] = '<script>messager("success", "Логин '.$value.' удален");</script>';
				}
			}
        }
		
		// Проверка и обработка IP
		if($category == 'ip') {
			if($value !== ($er = Lib_Validation::ip($value))) {
                $response['handler'] = '<script>messager("error", "'.$this->data['lang']['lib--validation'][$er].'");</script>';
                return $response;
			} else {
				$black_ip = $this->obj['model_black_ip']->check(['ip' => $value]);
				if($this->data['route']['param']['method'] == 'add') {
					if($black_ip) {
						$response['handler'] = '<script>messager("error", "Логин уже в списке");</script>';
						return $response;
					}
					$this->obj['model_black_ip']->insert(['ip' => $value]);
					$response['handler'] = '<script>messager("success", "Логин '.$value.' добавлен");</script>';
				} else {
					if(!$black_ip) {
						$response['handler'] = '<script>messager("error", "Логин не обнаружен");</script>';
						return $response;
					}
					$this->obj['model_black_ip']->delete(['where' => $black_ip['id']]);
					$response['handler'] = '<script>messager("success", "Логин '.$value.' удален");</script>';
				}
				
			}
			file_put_contents(DIR_ROOT.'/json/black_ip.json', json_encode($obj['model_black_ip']->get()), LOCK_EX);
        }
		
		// Вывод результатов
		$response['handler'] .= '<script>$("#black__form input").val("");</script>';
        $this->data['black_ip'] = $this->obj['model_black_ip']->get();
        $this->data['black_login'] = $this->obj['model_black_login']->get();
        $response['black__list'] = Core_View::load('black_list', $this->data);
		return $response;
	}
}