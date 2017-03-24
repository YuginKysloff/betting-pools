<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Controller_Refsys {

    public function __construct($data) {
        $this->data = $data;
		
		// Проверка доступа в закрытый раздел
		if(!$this->data['user']) exit(Lib_Main::rew_page('login'));

        // Подключение моделей
        require_once(DIR_ROOT.'/models/model_users.php');
        require_once(DIR_ROOT.'/models/model_referral_income.php');

        // Определение объектов
        $this->obj['model_users'] = new Model_Users();
        $this->obj['model_referral_income'] = new Model_Referral_Income();
	}

    // Метод по умолчанию
    public function index() {

        // Доход от рефералов
        $result = $this->obj['model_referral_income']->income(['sponsor_id' => $this->data['user']['id']]);
        if($result) {
            foreach($result as $val) {
                $this->data['result']['referral_income'][$val['valute']] = Lib_Main::beauty_number($val['amount']);
            }
        }

		// Количество рефералов
		$this->data['result']['ref']['count'] = $this->obj['model_users']->ref_count(['sponsor_id' => $this->data['user']['id']]);
		  
		// Формирование вида
        $page = Core_View::load('refsys', $this->data);
        return $page;
    }
		
	// Получение списка новостей с пагинацией
    public function get_list() {
		$this->data['result']['ref']['count'] = $this->obj['model_users']->ref_count(['sponsor_id' => $this->data['user']['id']]);
		$count = $this->data['result']['ref']['count'];
        $per_page = 50;
        $start_row = Lib_Main::clear_num($this->data['route']['param']['p'] ?? 0, 0);
        if($start_row < 0 || $start_row >= $count) $start_row = 0;
        $this->data['result']['list'] = $this->obj['model_users']->ref_list(['sponsor_id'=>$this->data['user']['id'],'start_row' => $start_row, 'per_page' => $per_page]);
		if($this->data['result']['list']) {
            $timezone = $_COOKIE['timezone'] ?? 0;
            $timezone = Lib_Main::clear_num(($this->data['user']['timezone'] ?? $timezone) * 60, 0);
            foreach ($this->data['result']['list'] as &$val) {
                $result = $this->obj['model_referral_income']->ref_income_by_id(['sponsor_id' => $this->data['user']['id'], 'user_id' => $val['id']]);
                if($result) {
                    foreach($result as $item) {
                        $val['ref_income'][$item['valute']] = Lib_Main::beauty_number($item['amount']);
                    }
                }
                $val['datetime'] = date($this->data['config']['formats']['datetime_full'], ($val['datetime'] + $timezone));
				$val['activity'] = date($this->data['config']['formats']['datetime_full'], ($val['activity'] + $timezone));
			}
            $this->data['result']['pagination'] = ($count > $per_page) ? Lib_Main::pagination($count, $per_page, 5, $start_row, 'log'):'';
        }

		// Формирование вида
        $response['refsys__list'] = Core_View::load('refsys_list', $this->data);
		return $response;
    }

    // Сброс количества переходов
    public function reset() {

        // Закрытый раздел
        if(!$this->data['user']) {
            $response['handler'] = Lib_Main::rew_page('refsys');
            return $response;
        }

        $this->obj['model_users']->update(['set' => ['visit' => '0'], 'where' => $this->data['user']['id']]);

        $response['handler'] = '<script>$("#refsys__reset_button").remove("span"); $("#refsys__reset_counter").text("0");</script>';
        $response['refsys__reset_counter'] = 0;
        return $response;
    }
}