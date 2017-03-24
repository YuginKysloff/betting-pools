<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */
 
defined('SW_CONSTANT') or die;

class Lib_Db {
	
	static private $_instance = null;

	private function __construct() {
		$this->db = new Mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		if($this->db->connect_error) exit(Lib_Main::rew_page('errors/data'));
		$this->db->set_charset("utf8");
		$this->db->query("SET @@sql_mode = ''");
		$this->db->query("SET time_zone = '+03:00'");
		$this->db->query("SET GLOBAL time_zone = '+03:00'");
		$this->db->query("SET LOCAL time_zone = '+03:00'");

		// Получение всех имен таблиц базы данных
		$tables = $this->db->query("SELECT `TABLE_NAME` FROM `INFORMATION_SCHEMA`.`TABLES` WHERE `TABLE_NAME` LIKE '".DB_PREF."%'");
		if($tables) {
			foreach($tables as $val) {
				$this->tables[substr($val['TABLE_NAME'], strlen(DB_PREF))] = $val['TABLE_NAME'];
			}
		}
	}

	private function __clone() {}
	
	private function __wakeup() {}

	static function instance() {
		if(self::$_instance == null) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	
	// Возврат списка таблиц
	public static function tables() {
		
		// Создание объекта класса
        $obj = self::$_instance;
		
		return $obj->tables;
	}
	
	// Обработка запроса
	public static function query($sql) {
        
		// Создание объекта класса
        $obj = self::$_instance;

		// Очистка предыдущего результата
		if (isset($obj->result) && is_object($obj->result)) {
			$obj->result->free();
		}

		// Записываем результат
		$obj->result = $obj->db->query($sql);
		
		// Если есть ошибки - перенаправляем их в лог
		if($obj->db->errno) {
			trigger_error("mysqli error #".$obj->db->errno.": ".$obj->db->error, E_USER_WARNING);
			exit(Lib_Main::rew_page('errors/data'));
		}

		// Возврат id при вставке(insert)
		if($obj->db->insert_id) {
			return $obj->db->insert_id;
		}

		// Возврат данных
		// ВНИМАНИЕ! данные всегда возвращаются в массиве, даже если запрос возвращает одну запись
		$data = false;
		if (is_object($obj->result)) {
			while ($row = $obj->result->fetch_assoc()) {
				$data[] = $row;
			}
			return $data;
		} else if ($obj->result == FALSE) {
			return false;
		} else {
			$res = $obj->db->affected_rows;
			return $res;
		}
    }   

	// Экранирование специальных символов в строке
	public static function escape_string($text) {
		
		// Создание объекта класса
        $obj = self::$_instance;
		
		return $obj->db->real_escape_string($text);
	}
}