<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Model_Multi extends Core_Model {
	
	public function __construct() {
		parent::__construct();
		$this->table = DB_PREF.strtolower(substr(__CLASS__, 6));
	}
	
	/**
	 * ADMIN
	 */
	 
	// Количество записей
    public function cnt($data) {
        $result = Lib_Db::query("	SELECT COUNT(DISTINCT `first`) as count
									FROM `".$this->table."`
									WHERE `archive` = '".$data['archive']."'");
        return $result[0]['count'];
    } 
	
	// Количество записей
    public function get_first($data) {
        $result = Lib_Db::query("	SELECT DISTINCT m.`first`, m.`archive`, u.`id`, u.`login`, u.`email`, u.`avatar`, u.`sponsor_id`, u.`usd`, u.`instant`, u.`access`, u.`ip`, u.`timezone`, u.`datetime`, u.`activity`, u2.`login` AS `sponsor`
                                    FROM `".$this->table."` AS m
                                    JOIN `".$this->tables['users']."` AS u
									ON m.`first` = u.`id`
                                    LEFT OUTER JOIN `".$this->tables['users']."` AS u2 
									ON u.`sponsor_id` = u2.`id`
                                    WHERE m.`archive` = '".$data['archive']."'
                                    LIMIT ".$data['start_row'].", ".$data['per_page']);
        return $result;
    }
	
	// Получение second'ов
    public function get_second($data) {
        $result = Lib_Db::query("   SELECT m.*, u.`login`, u.`email`, u.`avatar`, u.`sponsor_id`, u.`usd`, u.`instant`, u.`access`, u.`ip`, u.`timezone`, u.`datetime`, u.`activity`, u2.`login` AS `sponsor`
                                    FROM `".$this->table."` AS m
                                    JOIN `".$this->tables['users']."` AS u ON m.`second` = u.`id`
                                    LEFT OUTER JOIN `".$this->tables['users']."` AS u2 ON u.`sponsor_id` = u2.`id`
                                    WHERE m.`first` = '".$data['first']."'
                                    AND m.`archive` = '".$data['archive']."'");
        return $result;
    }
	
	// Получение списка first для архива или удаления
	public function check_first($data) {
		$result = Lib_Db::query("	SELECT DISTINCT m.".$data['type'].", u.`access`
									FROM `".$this->table."` AS m
									JOIN `".$this->tables['users']."` AS u ON m.`".$data['type']."` = u.`id`
									WHERE `".$data['type']."`='".$data['user_id']."'");
		return $result[0];
	}

	// Получение списка second для архива или удаления
	public function check_second($data) {
		$result = Lib_Db::query("	SELECT u.`access`
									FROM `".$this->table."` AS m
									JOIN `".$this->tables['users']."` AS u ON m.`second` = u.`id`
									WHERE m.`id`='".$data['id']."'");
		return $result[0];
	}
}