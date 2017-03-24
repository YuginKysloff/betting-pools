<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Controller_News {

    public function __construct($data) {
        $this->data = $data;

        // Подключение моделей
        require_once(DIR_ROOT.'/models/model_news.php');

        // Определение объектов
        $this->obj['model_news'] = new Model_News();
    }

    // Метод по умолчанию
    public function index() {
		
		if(isset($this->data['route']['param']['id'])) {
			$this->data['result']['view'] = $this->data['route']['param']['id'];
		}

        // Формирование вида
        $page = Core_View::load('news', $this->data);
        return $page;
    }

    // Получение списка новостей с пагинацией
    public function get_list() {

		// Количество новостей на странице
		$per_page = 3;

		// Общее количество новостей
        $count = $this->obj['model_news']->cnt(['current_lang' => $this->data['current_lang']]);
		if($count) {

			// Стартовая новость
			$start_row = Lib_Main::clear_num($this->data['route']['param']['p'] ?? 0, 0);
			if($start_row < 0 || $start_row >= $count) $start_row = 0;

			// Список новостей
			$this->data['result']['list'] = $this->obj['model_news']->get_list(['current_lang' => $this->data['current_lang'], 'start_row' => $start_row, 'per_page' => $per_page]);
			if($this->data['result']['list']) {

				// Временная зона
				$timezone = $_COOKIE['timezone'] ?? 0;
				$timezone = Lib_Main::clear_num(($this->data['user']['timezone'] ?? $timezone) * 60, 0);

				// Формирование результатов
				foreach ($this->data['result']['list'] as &$val) {
					$val['year'] = date("Y", ($val['datetime'] + $timezone));
					$val['month'] = date("m", ($val['datetime'] + $timezone));
					$val['day'] = date("d", ($val['datetime'] + $timezone));
					$val['datetime'] = date($this->data['config']['formats']['date_full'], ($val['datetime'] + $timezone));
				}

				// Пагинация
				if($count > $per_page) $this->data['result']['pagination'] = Lib_Main::pagination($count, $per_page, 5, $start_row, 'news');
			}
		}

		// Вывод результатов
        $response['news__list'] = Core_View::load('news_list', $this->data);
        return $response;
    }
	
	// Формирование новости по ID
    public function view() {
		$id = Lib_Main::clear_num($this->data['route']['param']['id'] ?? 0, 0);
		if(!$id) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
			$response['handler'] = Lib_Main::rew_page('errors/data');
			return $response;
		}
		$this->data['result'] = $this->obj['model_news']->get_id(['id' => $id, 'current_lang' => $this->data['current_lang']]);
		if(!$this->data['result']) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
			$response['handler'] = Lib_Main::rew_page('errors/404');
			return $response;
		}
		$timezone = $_COOKIE['timezone'] ?? 0;
		$timezone = Lib_Main::clear_num(($this->data['user']['timezone'] ?? $timezone) * 60, 0);
		$this->data['result']['datetime'] = date($this->data['config']['formats']['date_full'], ($this->data['result']['datetime'] + $timezone));
		
		// Формирование вида
		$response['news__list'] = Core_View::load('news_view', $this->data);
        return $response;
	}
}