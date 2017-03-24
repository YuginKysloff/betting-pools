<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Model_White extends Core_Model {
	
	public function __construct() {
		parent::__construct();
		$this->table = DB_PREF.strtolower(substr(__CLASS__, 6));
	}
	
	/**
	 * ADMIN
	 */
	
	// Общее количество записей
	public function cnt() {
        $result = Lib_Db::query("	SELECT COUNT(`id`) AS count 
									FROM `".$this->table."`");
        return $result[0]['count'];
    }
	
	// Вывод записей
	public function get($data) {
		$result = Lib_Db::query("   SELECT w.*, u.`login`, u.`avatar`, u.`sponsor_id`
                                    FROM `".$this->table."` AS w
									LEFT OUTER JOIN `".$this->tables['users']."` AS u
									ON w.`user_id` = u.`id`
                                    ORDER BY `id` DESC
                                    LIMIT ".$data['start_row'].", ".$data['per_page']);
        return $result;
	}
	
	// Проверка нахождения пользователя в списке
    public function check($data) {
        $result = Lib_Db::query("	SELECT * 
									FROM `".$this->table."` 
									WHERE `user_id` = '".$data['user_id']."'");
        return $result[0];
    }
	
	// Проверка существования записи
    public function check_by_id($data) {
        $result = Lib_Db::query("	SELECT * 
									FROM `".$this->table."` 
									WHERE `id` = '".$data['id']."'");
        return $result[0];
    }
	
	// Общее количество пользователей с пополнениями
	public function active_cnt() {
        $result = Lib_Db::query("	SELECT COUNT(DISTINCT w.`id`) AS count
									FROM `".$this->table."` AS w
									RIGHT OUTER JOIN `".$this->tables['fill']."` AS f
									ON w.`user_id` = f.`user_id`");
        return $result[0]['count'];
    }
	
	// Вывод ID пользователя с пополнениями
	public function get_active_rand($data) {
		$result = Lib_Db::query("   SELECT DISTINCT w.`user_id`
                                    FROM `".$this->table."` AS w
									LEFT OUTER JOIN `".$this->tables['fill']."` AS f
									ON w.`user_id` = f.`user_id`
                                    LIMIT ".$data['rand'].", 1");
        return $result[0]['user_id'];
	}
}