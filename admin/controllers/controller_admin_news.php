<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Controller_Admin_News { 

    public function __construct($data) {
        $this->data = $data;

        // Подключение моделей
        require_once(DIR_ROOT.'/models/model_news.php');

        // Определение объектов
        $this->obj['model_news'] = new Model_News();
    }

    // Метод по умолчанию
    public function index(){

        // Формирование вида
        $page = Core_View::load('news', $this->data);
        return $page;
    }

    // Получение списка новостей с пагинацией
    public function get_list() {

        // Определение текущей даты для отображения по умолчанию
        $this->data['result']['current_date'] = date('Y-m-d\TH:i', (time() + Lib_Main::clear_num($this->data['user']['timezone'] * 60, 0)));
        $count = $this->obj['model_news']->cnt(['current_lang' => $this->data['current_lang']]);
        $per_page = 5;
        $start_row = Lib_Main::clear_num($this->data['route']['param']['p'] ?? 0, 0);
        if($start_row < 0 || $start_row >= $count) $start_row = 0;

        // Получение и обработка новостей
        $this->data['result']['list'] = $this->obj['model_news']->get(['current_lang' => $this->data['current_lang'], 'start_row' => $start_row, 'per_page' => $per_page]);
        if($this->data['result']['list']) {
            foreach($this->data['result']['list'] as &$val) {
				$val['datetime_base'] = $val['datetime'];
				foreach($this->data['config']['lang']['list'] as $lang) {
					if($lang == $val['lang']) continue;
					if(!$this->obj['model_news']->check(['current_lang' => $lang, 'datetime' => $val['datetime']])) {
						$val['others'][] = 'Не обнаружена локализация '.strtoupper($lang);
					}
				}
                $val['datetime_input'] = date('Y-m-d\TH:i', ($val['datetime'] + Lib_Main::clear_num($this->data['user']['timezone'] * 60, 0)));
                $val['datetime'] = date($this->data['config']['formats']['admin_datetime'], ($val['datetime'] + Lib_Main::clear_num($this->data['user']['timezone'] * 60, 0)));
                $val['text'] = htmlspecialchars_decode($val['text']);
            }
			$this->data['result']['pagination'] = ($count > $per_page) ? Lib_Main::pagination_ajax($count, $per_page, 4, $start_row, '/'.$this->data['config']['site']['admin'].'/news/get_list', 'list', '#news__list') : '';
        }
		
		// Формирование вида
        $response['news__list'] = Core_View::load('news_list', $this->data);
        return $response;
    }
	
	// Вывод формы добавления новости
    public function add() {
		$this->data['result']['current_date'] = date('Y-m-d\TH:i', (time() + Lib_Main::clear_num($this->data['user']['timezone'] * 60, 0)));

        // Формирование вида
        $response['news__wysiwyg'] = Core_View::load('news_save', $this->data);
        return $response;
    }
	
	// Вывод формы редактирования новости
    public function edit() {
		$datetime = Lib_Main::clear_num($this->data['route']['param']['datetime'] ?? 0, 0);
		$news = $this->obj['model_news']->get_by_date(['datetime' => $datetime]);
		if(!$news) {
			$message['error'][] = 'Ошибка : новость не найдена';
			$response['news__wysiwyg'] = Core_View::message($message);
			return $response;
		}
		$this->data['result']['current_date'] = date('Y-m-d\TH:i', ($news[0]['datetime'] + Lib_Main::clear_num($this->data['user']['timezone'] * 60, 0)));
		foreach($news as $val) {
			$this->data['result']['news'][$val['lang']] = $val;
		}

        // Вывод результатов
        $response['handler'] = '<script>$("body,html").animate({scrollTop: 0}, 800);</script>';
        $response['news__wysiwyg'] = Core_View::load('news_save', $this->data);
        return $response;
    }

    // Добавление или редактирование новости
    public function save() {
		
		// Получение и проверка данных из формы
		$control = false;
		foreach($this->data['config']['lang']['list'] as $lang) {
			$title[$lang] = Lib_Main::clear_str($_POST['title'][$lang]);
			$text[$lang] = Lib_Db::escape_string(Lib_Main::clear_text($_POST['text'][$lang]));
			if((!empty($title[$lang]) && empty($text[$lang])) || (empty($title[$lang]) && !empty($text[$lang]))) {
				$message['error'][] = 'В новости должен быть указан заголовок и текст';
				$response['news__error'] = Core_View::message($message);
				return $response;
			}
			if(!empty($title[$lang]) && !empty($text[$lang])) $control = true;
		}
		if($control == false) {
			$message['error'][] = 'Введите заголовок и текст новости хотя бы на одном из языков';
			$response['news__error'] = Core_View::message($message);
			return $response;
		}
		
		// Обработка даты
		if(empty($_POST['datetime'])) {
			$message['error'][] = 'Введите дату и время новости';
			$response['news__error'] = Core_View::message($message);
			return $response;
        }
		$datetime = (strtotime(preg_replace('[T]', ' ', Lib_Main::clear_num($_POST['datetime'], 0)))) - Lib_Main::clear_num($this->data['user']['timezone'] * 60, 0);
		if(date("d-m-Y", $datetime) == '01-01-1970') {
			$message['error'][] = 'Дата указана некорректно';
			$response['news__error'] = Core_View::message($message);
			return $response;
		}
		
		// Сохранение данных
		$control = false;
		$edit = Lib_Main::clear_num((!empty($_POST['edit']) ? $_POST['edit'] : $datetime), 0);
		foreach($this->data['config']['lang']['list'] as $lang) {
			if(empty($title[$lang]) && empty($text[$lang])) continue;
			$id = $this->obj['model_news']->check(['current_lang' => $lang, 'datetime' => $edit]);
			if($id) {
				$this->obj['model_news']->update(['set' => ['title' => $title[$lang], 'text' => $text[$lang], 'lang' => $lang, 'datetime' => $datetime], 'where' => $id]);
			} else {
				$this->obj['model_news']->insert(['title' => $title[$lang], 'text' => $text[$lang], 'lang' => $lang, 'datetime' => $datetime]);
			}
			$control = true;
		}
		if($control == false) {
			$message['error'][] = 'Изменений не обнаружено';
			$response['news__error'] = Core_View::message($message);
			return $response;
		}
		
		// Вывод результатов
		$response = $this->get_list();
		$response['handler'] = '<script>$("body,html").animate({scrollTop: 0}, 800);</script>';
		$message['success'][] = 'Новость успешно сохранена';
        $response['news__wysiwyg'] = Core_View::message($message);
        return $response;
    }

    // Удаление новости
    public function delete() {
		$datetime = Lib_Main::clear_num($this->data['route']['param']['datetime'] ?? 0, 0);
		$id = $this->obj['model_news']->check(['datetime' => $datetime]);
		if(!$id) {
			$response['handler'] = '<script>messager("error", "Ошибка : новость не найдена");</script>';
			return $response;
		}
		$this->obj['model_news']->delete(['where' => [['datetime', '=', $datetime]]]);
		
        // Вывод результатов
		$response = $this->get_list();
		$response['news__wysiwyg'] = '';
        return $response;
    }
}