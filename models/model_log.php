<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Model_Log extends Core_Model {
	
	public function __construct() {
		parent::__construct();
		$this->table = DB_PREF.strtolower(substr(__CLASS__, 6));
	}
	
	/**
	 * ADMIN
	 */
	 
	// Количество записей
    public function cnt($data = null) {
		$where = isset($data['where']) ? "WHERE `user_id`='".$data['where']['user_id']."'" : '';
        $result = Lib_Db::query("	SELECT COUNT(`id`) as count
									FROM `".$this->table."`
									".$where);
        return $result[0]['count'];
    }
	
	// Список записей
    public function get_list($data = null) {
		$where = isset($data['user_id']) && $data['user_id'] != 0 ? "WHERE `user_id`='".$data['user_id']."'" : '';
        $result = Lib_Db::query("	SELECT l.*, u.`login`
									FROM `".$this->table."` as l
									LEFT OUTER JOIN `".$this->tables['users']."` AS u
									ON l.`user_id` = u.`id`
									".$where."
									ORDER BY `id` DESC
									LIMIT ".$data['start_row'].", ".$data['per_page']);
        return $result;
    }
}