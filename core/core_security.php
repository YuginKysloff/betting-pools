<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */
 
defined('SW_CONSTANT') or die;

class Core_Security {
	
	// Удаляем сессии и куки
	public static function dest_data() {
		if(isset($_SESSION['login'])) unset($_SESSION['login']);
		if(isset($_SESSION['password'])) unset($_SESSION['password']);
		if(isset($_SESSION['hash'])) unset($_SESSION['hash']);
		if(isset($_COOKIE['login'])) setcookie("login", "", time()-3600, "/");
		if(isset($_COOKIE['password'])) setcookie("password", "", time()-3600, "/");
		if(isset($_COOKIE['hash'])) setcookie("hash", "", time()-3600, "/");
	}
	
	// Проверка на черный ip
    public static function check_black() {
		
		// Формирование пути
		$path = DIR_ROOT.'/json/black_ip.json';
		
		// Проверка на доступность файла
		if (!file_exists($path)) {
			
			// Подключение модели
			require_once(DIR_ROOT.'/models/model_black_ip.php');
			
			// Определение объекта
			$obj['model_black_ip'] = new Model_Black_Ip();

			$black_ip_arr = [];
			$list = $obj['model_black_ip']->get();
            if($list) {
                foreach($list as $val) {
                    $black_ip_arr[] = $val['ip'];
                }
            }
            file_put_contents(DIR_ROOT.'/json/black_ip.json', json_encode($black_ip_arr), LOCK_EX);
		}
		
		// Парсинг файла
		$black_ip = json_decode(file_get_contents($path), true);
		
		// Проверка на присутствие ip в списке
		if(!is_array($black_ip)) {
			return;
		}
		if(in_array(Lib_Main::get_ip(), $black_ip)) {
			return true;
		}
    }
	
	// Запись предупреждения в таблицу warning
    public static function write_warning($data) {

		// Определение переменных
		$ip = Lib_Main::get_ip();

		// Подключение модели
		require_once(DIR_ROOT.'/models/model_black_ip.php');
		require_once(DIR_ROOT.'/models/model_warning.php');
		
		// Определение объекта
		$obj['model_black_ip'] = new Model_Black_Ip();
		$obj['model_warning'] = new Model_Warning();
		
		if(file_exists(DIR_ROOT.'/models/model_users.php')) {
			require_once(DIR_ROOT.'/models/model_users.php');
			$obj['model_users'] = new Model_Users();
		}
		
		$data['user_id'] = $data['user_id'] ?? 0;

		// Обработка и очитска данных
		$text = Lib_Db::escape_string($data['text']);
		
		// Запись нарушения в базу данных
		$obj['model_warning']->insert(['category' => $data['category'], 'user_id' => $data['user_id'], 'ip' => $ip, 'text' => $text, 'datetime' => time()]);

        //Если уровень ошибки 1, то проверка количества записей в таблицу от одного юзера за 15 мин
        if($data['category'] == 1) {
			$where = $data['user_id'] == 0 ? "`ip`='".$ip."'" : "`user_id`='".$data['user_id']."'";
			if($obj['model_warning']->get_last_cnt(['where' => $where]) > 10) {
				
				// Блокировка IP и пользователя, и обновление JSON файла
				$obj['model_warning']->insert(['category' => 2, 'user_id' => $data['user_id'], 'ip' => $ip, 'text' => '[m-core--security|auto-block-ip]', 'datetime' => time()]);
				if(!$obj['model_black_ip']->check(['ip' => $ip])) {
					$obj['model_black_ip']->insert(['ip' => $ip]);
					$black_ip_arr = [];
					$list = $obj['model_black_ip']->get();
					if($list) {
						foreach($list as $val) {
							$black_ip_arr[] = $val['ip'];
						}
					}
					file_put_contents(DIR_ROOT.'/json/black_ip.json', json_encode($black_ip_arr), LOCK_EX);
				}
				if($data['user_id'] !== 0) $obj['model_users']->update(['set' => ['access' => 1], 'where' => $data['user_id']]);
			}
        }
    }
}