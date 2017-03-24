<?php
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */
 
defined('SW_CONSTANT') or die;

class Model_Warning extends Core_Model {
	
	public function __construct() {
		parent::__construct();
		$this->table = DB_PREF.strtolower(substr(__CLASS__, 6));
	}
	
	/**
	 * ADMIN, core_settings, core_security
	 */
	
	// Количество записей за последнее время
	public function get_last_cnt($data) {
		$result = Lib_Db::query("	SELECT COUNT(`id`) AS count 
									FROM `".$this->table."` 
									WHERE ".$data['where']." AND `category` = '1' AND ('".time()."' - `datetime` < '900')");
        return $result[0]['count'];
	}
	
	// Количество записей
	public function cnt() {
		$result = Lib_Db::query("	SELECT COUNT(`id`) AS count
                					FROM `".$this->table."`");
		return $result[0]['count'];
	}
	
	// Количество записей по логину
	public function cnt_by_login($data) {
		$result = Lib_Db::query("	SELECT COUNT(w.`id`) AS count
                					FROM `".$this->table."` AS w
                					LEFT JOIN `".$this->tables['users']."` AS u
									ON w.`user_id` = u.`id`
									WHERE u.`login` = '".$data['login']."'");
		return $result[0]['count'];
	}
	
	// Количество записей по IP
	public function cnt_by_ip($data) {
		$result = Lib_Db::query("	SELECT COUNT(`id`) AS count
                					FROM `".$this->table."`
									WHERE `ip` = '".$data['ip']."'");
		return $result[0]['count'];
	}

	// Список записей
	public function get_list($data) {
		$result = Lib_Db::query("	SELECT w.*, u.`login` 
                					FROM `".$this->table."` AS w
                					LEFT JOIN `".$this->tables['users']."` AS u 
									ON w.`user_id` = u.`id`
                					ORDER BY w.`id` DESC
                					LIMIT ".$data['start_row'].", ".$data['per_page']);
		return $result;
	}
	
	// Список записей по логину
	public function get_list_by_login($data) {
		$result = Lib_Db::query("  	SELECT w.*, u.`login` 
                					FROM `".$this->table."` AS w
                					LEFT JOIN `".$this->tables['users']."` AS u 
									ON w.`user_id` = u.`id`
									WHERE u.`login` = '".$data['login']."'
                					ORDER BY w.`id` DESC
                					LIMIT ".$data['start_row'].", ".$data['per_page']);
		return $result;
	}
	
	// Список записей по IP
	public function get_list_by_ip($data) {
		$result = Lib_Db::query("	SELECT w.*, u.`login` 
                					FROM `".$this->table."` AS w
                					LEFT JOIN `".$this->tables['users']."` AS u 
									ON w.`user_id` = u.`id`
									WHERE w.`ip` = '".$data['ip']."'
                					ORDER BY w.`id` DESC
                					LIMIT ".$data['start_row'].", ".$data['per_page']);
		return $result;
	}
}