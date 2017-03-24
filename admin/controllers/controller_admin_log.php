<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Controller_Admin_Log {

	public function __construct($data) {
        $this->data = $data;

        // Подключение моделей
        require_once(DIR_ROOT.'/models/model_log.php');
        require_once(DIR_ROOT.'/models/model_users.php');

        // Определение объектов
        $this->obj['model_log'] = new Model_Log();
        $this->obj['model_users'] = new Model_Users();
	}

    // Метод по умолчанию
    public function index() {

        // Формирование вида
        $page = Core_View::load('log', $this->data);
        return $page;
    }

    // Получение логов
    public function get_list() {

        $login = Lib_Main::clear_str($_POST['login'] ?? '');
        if(empty($login)) {
            $count = $this->obj['model_log']->cnt();
        } else {
            $id = $this->obj['model_users']->id_by_login(['login' => $login]);
			if(!$id) {
				$response['log__list'] =  Core_View::load('log_list', $this->data);
				return $response;
			}
			$count = $this->obj['model_log']->cnt(['where' => ['user_id' => $id]]);
        }
		$per_page = 50;
		$start_row = Lib_Main::clear_num($this->data['route']['param']['p'] ?? 0, 0);
		if($start_row < 0 || $start_row >= $count) $start_row = 0;

		$this->data['result']['list'] = $this->obj['model_log']->get_list(['start_row' => $start_row, 'per_page' => $per_page, 'user_id' => ($id ?? 0)]);
		
		if($this->data['result']['list']) {
			foreach($this->data['result']['list'] as &$val) {
				$val['text'] = Lib_Main::markup($val['text'], ['lang' => $this->data['lang']]);
				$val['usd'] = '-';
				if(!empty($val['control'])) {
					$control = explode('|', $val['control']);
					foreach($control as $item) {
						list($nam, $sign) = explode('=', $item);
						if(isset($this->data['config']['valutes'][$nam])) $val[$nam] = Lib_Main::beauty_number($sign);
					}
				}
				$val['datetime'] = date($this->data['config']['formats']['admin_datetime'], ($val['datetime'] + ($this->data['user']['timezone'] * 60)));
			}
			$this->data['result']['pagination'] = ($count > $per_page) ? Lib_Main::pagination_ajax($count, $per_page, 4, $start_row, '/'.$this->data['config']['site']['admin'].'/log/get_list', 'list', '#log__list', 'log__form') : '';
		}
        $response['log__list'] = Core_View::load('log_list', $this->data);
        return $response;
    }
}