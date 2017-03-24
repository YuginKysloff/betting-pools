<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Controller_Admin_Security {

    public function __construct($data) {
        $this->data = $data;

        // Подключение моделей
        require_once(DIR_ROOT.'/models/model_users.php');
        require_once(DIR_ROOT.'/models/model_warning.php');

        // Определение объектов
        $this->obj['model_users'] = new Model_Users();
        $this->obj['model_warning'] = new Model_Warning();
    }

    public function index() {

        // Формирование вида
        $page = Core_View::load('security', $this->data);
        return $page;
    }

    public function get_list() {

        // Получение POST данных из формы фильтров
        $category = Lib_Main::clear_str($_POST['category'] ?? '');
        $value = Lib_Main::clear_str($_POST['value'] ?? '');

		if($value == '') {
			$count = $this->obj['model_warning']->cnt();
		} else {
			$count = ($category == 'login') ? $this->obj['model_warning']->cnt_by_login(['login' => $value]) : $this->obj['model_warning']->cnt_by_ip(['ip' => $value]);
		}
        $per_page = 50;
        $start_row = Lib_Main::clear_num($this->data['route']['param']['p'] ?? 0, 0);
        if($start_row < 0 || $start_row >= $count) $start_row = 0;

		if($value == '') {
			$this->data['result']['list'] = $this->obj['model_warning']->get_list(['start_row' => $start_row, 'per_page' => $per_page]);
		} else {
			$this->data['result']['list'] = ($category == 'login') ? $this->obj['model_warning']->get_list_by_login(['login' => $value, 'start_row' => $start_row, 'per_page' => $per_page]) : $this->obj['model_warning']->get_list_by_ip(['ip' => $value, 'start_row' => $start_row, 'per_page' => $per_page]);
		}
        if($this->data['result']['list']) {
            foreach($this->data['result']['list'] as &$val) {
                $val['datetime'] = date($this->data['config']['formats']['admin_datetime'], $val['datetime'] + ($this->data['user']['timezone'] * 60));
                $val['text'] = Lib_Main::markup($val['text'], ['lang' => $this->data['lang']]);
            }
            $this->data['result']['pagination'] = ($count > $per_page) ? Lib_Main::pagination_ajax($count, $per_page, 4, $start_row, '/'.$this->data['config']['site']['admin'].'/security/get_list', 'list', '#security__list',  'security__form') : null;
        }
        $response['security__list'] = Core_View::load('security_list', $this->data);
        return $response;
    }
}