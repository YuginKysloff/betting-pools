<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Controller_Log {

    public function __construct($data) {
        $this->data = $data;
		
		// Проверка доступа в закрытый раздел
		if(!$this->data['user']) exit(Lib_Main::rew_page('login'));

        // Подключение моделей
        require_once(DIR_ROOT.'/models/model_log.php');

        // Определение объектов
        $this->obj['model_log'] = new Model_Log();
    }

    // Метод по умолчанию
    public function index() {

        // Формирование вида
        $page = Core_View::load('log', $this->data);
        return $page;
    }

    // Получение списка новостей с пагинацией
    public function get_list() {
		$count = $this->obj['model_log']->cnt(['where' => ['user_id' => $this->data['user']['id']]]);
        $per_page = 50;
        $start_row = Lib_Main::clear_num($this->data['route']['param']['p'] ?? 0, 0);
        if($start_row < 0 || $start_row >= $count) $start_row = 0;
		if($count) {
			$this->data['result']['list'] = $this->obj['model_log']->get_list(['user_id' => $this->data['user']['id'], 'start_row' => $start_row, 'per_page' => $per_page]);
			if($this->data['result']['list']) {
				$timezone = $_COOKIE['timezone'] ?? 0;
				$timezone = Lib_Main::clear_num(($this->data['user']['timezone'] ?? $timezone) * 60, 0);
				foreach ($this->data['result']['list'] as &$val) {
					$val['datetime'] = date($this->data['config']['formats']['datetime_full'], ($val['datetime'] + $timezone));
					$val['text'] = Lib_Main::markup($val['text'], ['lang' => $this->data['lang']]);
				}
				$this->data['result']['pagination'] = ($count > $per_page) ? Lib_Main::pagination_ajax($count, $per_page, 4, $start_row, '/log/get_list', 'list', '#log__list') : '';
			}
		}

		// Формирование вида
		$response['log__list'] = Core_View::load('log_list', $this->data);
		return $response;		
    }
}