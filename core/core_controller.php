<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */
 
defined('SW_CONSTANT') or die;

class Core_Controller {
	
	// Конструктор
	protected function __construct() {
		$this->data['config'] = Config::instance()->config;
		Lib_Lang::detection();
		$this->data['current_lang'] = Lib_Lang::instance()->current_lang;
		$this->data['lang'] = Lib_Lang::get_parse($this->data['current_lang']);
	}
	
	// Главный метод
	protected function main($route){

		$this->data['route'] = $route;

		// Проверка на черный IP
		if(Core_Security::check_black()) $this->errors('access');
		
		// Формирование мета тегов
		$this->data['meta_title'] = $this->data['lang'][$this->data['route']['controller']]['meta_title'] ?? $this->data['config']['site']['name'];
		$this->data['meta_description'] = $this->data['lang'][$this->data['route']['controller']]['meta_description'] ?? '';
		$this->data['meta_keywords'] = $this->data['lang'][$this->data['route']['controller']]['meta_keywords'] ?? '';

		if(isset($this->data['route']['admin'])) {	
		
			// Формирование пути
			$path = DIR_ROOT.'/admin';
			$prefix = 'admin_';
		} else {
			
			// Проверка доступности сайта
			if(!$this->data['config']['site']['enabled']) $this->errors('maintenance');
			
			// Формирование пути
			$path = DIR_ROOT;
			$prefix = '';
		}
		
		// Обработка и запись параметров
		if(isset($this->data['route']['param'])) {
			$array = explode('/', $this->data['route']['param']);
			$this->data['route']['param'] = array();
			foreach($array as $key => $val){
				if(($key % 2) == 0) {
					$last_key = $val;
				} else {
					$this->data['route']['param'][$last_key] = $val;
				}
			}
		}

		// Подключение контроллера пользователя, если он существует
		if(file_exists($path.'/controllers/controller_'.$prefix.'users.php')) {
			
			require_once($path.'/controllers/controller_'.$prefix.'users.php');
			
			// Создание объекта пользователя
			$user_class = 'Controller_'.ucfirst($prefix).'Users';
			$obj_users = new $user_class($this->data);

			// Извлечение данных пользователя
			try {
				$this->data['user'] = $obj_users->get_user();
			} catch(Exception $e) {
				if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
					$response['handler'] = $e->getMessage();
					exit(json_encode($response));
				}
				exit($e->getMessage());
			}

			// Запись спонсора
			if(isset($this->data['route']['sponsor'])) $obj_users->sponsor($this->data['route']['sponsor'], $this->data['user']);

			// Определение источника перехода
			if(!isset($this->data['route']['admin']) && $this->data['route']['controller'] == 'index') $obj_users->url();
		}

		// Проверка на существование контроллера
		if(!file_exists($path.'/controllers/controller_'.$prefix.str_replace("-", "_", $this->data['route']['controller']).'.php')) {
			$page = Core_View::load(str_replace("-", "_", $this->data['route']['controller']), $this->data);
			if(!$page) exit(Lib_Main::rew_page('errors/404'));
			exit($page);
		} 
		
		// Создание объекта контроллера
		require_once($path.'/controllers/controller_'.$prefix.str_replace("-", "_", $this->data['route']['controller']).'.php');
		$controller = 'Controller_'.ucfirst($prefix).ucfirst(str_replace("-", "_", $this->data['route']['controller']));
		$object = new $controller($this->data);
		
		// Проверка на существование метода
		$method = $this->data['route']['method'] ?? 'index';
		if(!method_exists($object, $method)) {
			exit(Lib_Main::rew_page('errors/data'));
		}
		
		// Обращение к методу
		$response = $object->$method();

		// Проверка на запрос от Ajax
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
			exit(json_encode($response));
		}
		
		// Если метод не от Ajax и в результате массив - на ошибку
		if(is_array($response)) exit(Lib_Main::rew_page('errors/data'));
		
		// Вывод результата
		exit($response);
	}
	
	// Метод обработки ошибок
	protected function errors($action){
		if ($action == 'access' || $action == 'blocked' || $action == 'data') {
			Core_Security::dest_data();
		}
		if ($action == 'maintenance') {
			
			// Проверка доступности сайта
			if($this->data['config']['site']['enabled'] == '1') exit(Lib_Main::rew_page(''));
		}
		
		$data = $this->data;
		
		// Формирование мета тегов
		$data['meta_title'] = $this->data['lang']['errors--pages'][$action] ?? '';
		$data['meta_description'] = '';
		$data['meta_keywords'] = '';
		
		$template = $this->data['config']['site']['template'];
		
		$path = DIR_ROOT.'/views/'.$this->data['config']['site']['template'].'/pages/errors/errors_'.$action.'.php';
		if(!file_exists($path)) {
			$path_404 = DIR_ROOT.'/views/'.$this->data['config']['site']['template'].'/pages/errors/errors_404.php';
			if(file_exists($path_404)) {
				ob_start();
				require_once($path_404);
				$result = ob_get_contents();
				ob_end_clean();
				echo Lib_Main::view_page($result);
				exit;
			}
			exit('Missing file display error : '.$action);
		}
		ob_start();
		require_once($path);
		$result = ob_get_contents();
		ob_end_clean();
		echo Lib_Main::view_page($result);
		exit;
	}
	
	// Метод обработки крон-запросов
	protected function cron($cron_action){

		// Проверка разрешения доступа по IP
		if(is_array($this->data['config']['site']['cron_ip']) && !in_array(Lib_Main::get_ip(), $this->data['config']['site']['cron_ip_arr'])) {
			Core_Security::write_warning(['category' => 1, 'text' => '[m-core--security|not-access-cron]'.' &#8594 '.__FILE__.' ('.__LINE__.')']);
			$this->errors('data');
		} else {
			if(Lib_Main::get_ip() !== $this->data['config']['site']['cron_ip']) {
				Core_Security::write_warning(['category' => 1, 'text' => '[m-core--security|not-access-cron]'.' &#8594 '.__FILE__.' ('.__LINE__.')']);
				$this->errors('data');
			}
		}
		
		// Проверка существования файла
		$cron_file = DIR_ROOT.'/cron/cron_'.$cron_action.'.php';
		if(!file_exists($cron_file)) {
			Core_Security::write_warning(['category' => 1, 'text' => '[m-core--security|empty-cron]'.' &#8594 '.__FILE__.' ('.__LINE__.')']);
			$this->errors('data');
		}
		
		$data = $this->data;
		
		// Запись в лог файл о начале выполнения
		$start_time = time();
		$cron_file = DIR_ROOT.'/txt/crons/'.date('Y-m-d', time()).'.txt';
		if(!file_exists($cron_file)) {
			file_put_contents($cron_file, '['.date("Y-m-d H:i:s", time()).'] Start Cron File => '.$cron_action.PHP_EOL, LOCK_EX);
		} else {
			file_put_contents($cron_file, '['.date("Y-m-d H:i:s", time()).'] Start Cron File => '.$cron_action.PHP_EOL.file_get_contents($cron_file), LOCK_EX);
		}

		// Подключение крон-файла
		require_once(DIR_ROOT.'/cron/cron_'.$cron_action.'.php');

		// Завершение выполнения и запись в лог файл
		file_put_contents($cron_file, '['.date("Y-m-d H:i:s", time()).'] End Cron File => '.$cron_action.' (Run Time => '.date("i:s", (time() - $start_time)).' / '.date("i:s", $max_execution_time).')'.PHP_EOL.file_get_contents($cron_file), LOCK_EX);
		exit;
	}
}