<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */
 
defined('SW_CONSTANT') or die;

class Controller_Admin_Settings {

	public function __construct($data = null){
		$this->data = $data;
		
		// Подключение ядра настроек
		require_once(DIR_ROOT.'/core/core_settings.php');
			
		$this->obj['core_settings'] = new Core_Settings();
	}

	// Метод по умолчанию
	public function index() {
		
		// Получение списка категорий и значений
		$this->data['result'] = $this->get_data();
	
		// Формирование вида
        $page = Core_View::load('settings', $this->data);
        return $page;
	}
	
	// Вывод списка настроек
	public function get_list() {
		
		// Получение списка категорий и значений
		$this->data['result'] = $this->get_data();
	
		// Формирование вида
		$response['settings__list'] = Core_View::load('settings_list', $this->data);
        return $response;
	}
	
	// Получение списка категорий и значений
	public function get_data() {
		$result['category'] = $this->obj['core_settings']->obj['model_settings_category']->get();
		$result['values'] = $this->obj['core_settings']->obj['model_settings']->get();

		$this->data['level'] = 0;
		$this->view_result($result, '0');
		return $result;
	}
	
	// Вывод дерева в массив результатов
	public function view_result(&$result, $parent_id) {
		$item = $this->get_category($result, $parent_id);
		foreach($item as $val) {
			$val['level'] = $this->data['level'];
			$this->data['list'][] = $val;
			$this->data['level'] += 1;
			$this->view_result($result, $val['id']);
			$this->data['level'] -= 1;
		}
	}
	
	// Вывод категорий по родителю
	public function get_category(&$result, $parent_id) {
		$item = array();
		foreach($result['category'] as $category) {
			if($category['parent_id'] !== $parent_id) continue;
			$item[] = $category;
		}
		if($result['values']) {
			foreach($result['values'] as $values) {
				if($parent_id !== $values['category']) continue;
				$this->data['list'][] = $values;
			}
		}
		return $item;
	}
	
	// Добавление категории
	public function add_setting_category() {
		
		// Проверка уровня доступа
		if($this->data['user']['access'] < 5){
			$response['handler'] = Lib_Main::rew_page('errors/access');
			return $response;
		}
		
		// Проверка и обработка данных
		$parent_id = Lib_Main::clear_num($_POST['parent_id'], 0);
		if(($comment = Lib_Main::clear_str($_POST['comment'])) == '') {
			$response['handler'] = '<script>messager("error", "Введите описание");</script>'; 
			return $response;
		}
		$name = $_POST['name'];
		if($name !== ($err = Lib_Validation::key($name))){
			$response['handler'] = '<script>messager("error", "'.$this->data['lang']['lib--validation'][$err].'");</script>'; 
			return $response;
		}
		
		// Проверка существования ключа в данной категории
		if($this->obj['core_settings']->obj['model_settings_category']->check_key(['name' => $name])) {
			$response['handler'] = '<script>messager("error", "Ключ занят");</script>';
			return $response;
		}
		$sort = Lib_Main::clear_num($_POST['sort'], 0);
		
		// Добавление категории
		$this->obj['core_settings']->obj['model_settings_category']->insert(['parent_id' => $parent_id, 'name' => $name, 'comment' => $comment, 'sort' => $sort]);
		
		// Обновление файла JSON
		$this->upd_json();
		
		// Получение списка категорий и значений
		$this->data['result'] = $this->get_data();
		
		// Формирование вида
		$response['settings__list'] = Core_View::load('settings_list', $this->data);
		$response['handler'] = '<script>$("#settings__form_new_setting_cat input").val(""); messager("success", "Категория успешно добавлена");</script>';
		return $response;
	}
	
	// Добавление настройки
	public function add_setting() {
		
		// Проверка уровня доступа
		if($this->data['user']['access'] < 5){
			$response['handler'] = Lib_Main::rew_page('errors/access');
			return $response;
		}
		
		// Проверка и обработка данных
		$category = Lib_Main::clear_num($_POST['category'], 0);
		if(!$this->obj['core_settings']->obj['model_settings_category']->check(['id' => $category])) {
			$response['handler'] = '<script>messager("error", "Произошла ошибка. Категории не существует");</script>';
			return $response;
		}
		if(($comment = Lib_Main::clear_str($_POST['comment'])) == '') {
			$response['handler'] = '<script>messager("error", "Введите описание");</script>';
			return $response;
		}
		$name = $_POST['name'];
		if($name !== ($err = Lib_Validation::key($name))) {
			$response['handler'] = '<script>messager("error", "'.$this->data['lang']['lib--validation'][$err].'");</script>';
			return $response;
		}
		if(($value = Lib_Main::clear_str($_POST['value'])) == '') {
			$response['handler'] = '<script>messager("error", "Введите значение");</script>';
			return $response;
		}
		$sort = Lib_Main::clear_num($_POST['sort'] ?? 0, 0);
		
		// Добавление настройки
		$this->obj['core_settings']->obj['model_settings']->insert(['category' => $category, 'name' => $name, 'value' => $value, 'comment' => $comment, 'sort' => $sort]);
		
		// Обновление файла JSON
		$this->upd_json();
		
		// Получение списка категорий и значений
		$this->data['result'] = $this->get_data();
		
		// Формирование вида
		$response['settings__list'] = Core_View::load('settings_list', $this->data);
		$message['success'][] = 'Настройка успешно добавлена';
        $response['settings_error'] = Core_View::message($message);
		$response['handler'] = '<script>$("#settings__form_new_setting input").val(""); messager("success", "Настройка успешно добавлена");</script>';
		return $response;
	}
	
	// Удаление категории
	public function delete_settings_category() {
		
		// Проверка уровня доступа
		if($this->data['user']['access'] < 5){
			$response['handler'] = Lib_Main::rew_page('errors/access');
			return $response;
		}
		
		$id = Lib_Main::clear_num($this->data['route']['param']['id'], 0);
		
		// Проверка и обработка данных
		if(!$this->obj['core_settings']->obj['model_settings_category']->check(['id' => $id])) {
			$response['handler'] = '<script>messager("error", "Произошла ошибка. Категории не существует");</script>';
			return $response;
		}

		// Проверка наличия подкатегорий
		if($this->obj['core_settings']->obj['model_settings_category']->subcat(['parent_id' => $id])) {
			$response['handler'] = '<script>messager("error", "Удалите подкатегории");</script>';
			return $response;
		}
		
		// Проверка наличия настроек категории
		if($this->obj['core_settings']->obj['model_settings']->value_by_category(['category' => $id])) {
			$response['handler'] = '<script>messager("error", "Удалите записи категории");</script>';
			return $response;
		}
		
		// Удаление категории
		$this->obj['core_settings']->obj['model_settings_category']->delete(['where' => $id]);
		
		$response['handler'] = '<script>$("#settings_category_str'.$id.'").fadeOut(500);</script>';
		return $response;
	}
	
	// Удаление настройки
	public function delete_settings() {
		
		// Проверка уровня доступа
		if($this->data['user']['access'] < 5){
			$response['handler'] = Lib_Main::rew_page('errors/access');
			return $response;
		}
		
		$id = Lib_Main::clear_num($this->data['route']['param']['id'], 0);
		
		// Проверка и обработка данных
		if(!$this->obj['core_settings']->obj['model_settings']->check(['id' => $id])) {
			$response['handler'] = '<script>messager("error", "Произошла ошибка. Настройки не существует");</script>';
			return $response;
		}
		
		// Удаление записи
		$this->obj['core_settings']->obj['model_settings']->delete(['where' => $id]);
		
		$response['handler'] = '<script>$("#settings_str'.$id.'").fadeOut(500);</script>';
		return $response;
	}
	
	// Сохранение настроек
	public function save() {	
	
		// Проверка уровня доступа
		if($this->data['user']['access'] < 5){
			$response['handler'] = Lib_Main::rew_page('errors/access');
			return $response;
		}
	
		$response['handler'] = '';
		$control = false;

		foreach($this->obj['core_settings']->obj['model_settings_category']->get() as $val) {
			if(!isset($_POST['settings_category']['name'][$val['id']])) continue;
			if($val['name'] !== $_POST['settings_category']['name'][$val['id']] || $val['comment'] !== $_POST['settings_category']['comment'][$val['id']] || $val['sort'] !== $_POST['settings_category']['sort'][$val['id']]) {
				$this->obj['core_settings']->obj['model_settings_category']->update(['set' => ['name' => $_POST['settings_category']['name'][$val['id']], 'comment' => $_POST['settings_category']['comment'][$val['id']], 'sort' => $_POST['settings_category']['sort'][$val['id']]], 'where' => $val['id']]);
				$control = true;
			}
		}
		foreach($this->obj['core_settings']->obj['model_settings']->get() as $val) {
			if(!isset($_POST['settings']['name'][$val['id']])) continue;
			if($val['name'] !== $_POST['settings']['name'][$val['id']] || $val['value'] !== $_POST['settings']['value'][$val['id']] || $val['comment'] !== $_POST['settings']['comment'][$val['id']] || $val['sort'] !== $_POST['settings']['sort'][$val['id']]) {
				if($_POST['settings']['name'][$val['id']] == '' || $_POST['settings']['comment'][$val['id']] == '' || $_POST['settings']['sort'][$val['id']] == '' || $_POST['settings']['value'][$val['id']] == '') {
					$response['handler'] .= '<script>messager("error", "Поля не должны быть пустыми");</script>';
					continue;
				}
				if($val['name'] == 'admin') {
					$response['handler'] .= Lib_Main::rew_page($_POST['settings']['value'][$val['id']].'/settings');
				}
				$this->obj['core_settings']->obj['model_settings']->update(['set' => ['name' => $_POST['settings']['name'][$val['id']], 'value' => $_POST['settings']['value'][$val['id']], 'comment' => $_POST['settings']['comment'][$val['id']], 'sort' => $_POST['settings']['sort'][$val['id']]], 'where' => $val['id']]);
				$control = true;
			}
		}

		if($control == true) {
			
			// Обновление файла JSON
			$this->upd_json();

			$response['handler'] .= '<script>messager("success", "Изменения успешно сохранены");</script>';
		} else {
			$response['handler'] .= '<script>messager("error", "Изменений не обнаружено");</script>';
		}
		return $response;
	}
	
	// Панель управления
	public function panel() {
		switch($this->data['route']['param']['action']) {
			case 'enabled':
				$value = $this->data['config']['site']['enabled'] == '0' ? 1 : 0;
				$category = $this->obj['core_settings']->obj['model_settings_category']->check_key(['name' => 'site'])[0]['id'];
				$setting = $this->obj['core_settings']->obj['model_settings']->get_id_by_name(['name' => 'enabled', 'category' => $category]);
				$this->obj['core_settings']->obj['model_settings']->update(['set' => ['value' => $value], 'where' => $setting]);
				$this->upd_json();
			break;
			case 'signup':
				$value = $this->data['config']['site']['signup'] == '0' ? 1 : 0;
				$category = $this->obj['core_settings']->obj['model_settings_category']->check_key(['name' => 'site'])[0]['id'];
				$setting = $this->obj['core_settings']->obj['model_settings']->get_id_by_name(['name' => 'signup', 'category' => $category]);
				$this->obj['core_settings']->obj['model_settings']->update(['set' => ['value' => $value], 'where' => $setting]);
				$this->upd_json();
			break;
			default:
				Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.($this->data['user']['login'] ?? '#empty').' &#8594 '.__FILE__.' ('.__LINE__.')', 'datetime' => time()]);
				$response['handler'] = Lib_Main::rew_page('errors/data');
				return $response;
			break;
		}
		$response['handler'] = '<script>if($("#settings__list").length != 0) {process(\'/'.$this->data['config']['site']['admin'].'/settings/get_list\', \'list\', \'#settings__list\');}</script>';
		return $response;
	}
	
	// Обновление файла JSON
	public function upd_json() {
		file_put_contents(DIR_ROOT.'/json/config.json', json_encode($this->obj['core_settings']->get_tree()), LOCK_EX);
	}
}