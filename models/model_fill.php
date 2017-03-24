<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Model_Fill extends Core_Model {
	
	public function __construct() {
		parent::__construct();
		$this->table = DB_PREF.strtolower(substr(__CLASS__, 6));
	}
    
    // Подсчет неудачных пополнений за последние 15 минут
    public function cnt_last($data) {
        $result = Lib_Db::query("	SELECT COUNT(`id`) AS cnt 
									FROM `".$this->table."`
									WHERE `user_id`='".$data['user_id']."'
									AND `status` = '0'
									AND (".time()." - `datetime` < 900)");
        return $result[0]['cnt'];
    }
    
    // Количество записей пользователя
    public function user_count($data) {
        $result = Lib_Db::query("   SELECT COUNT(`id`) AS count
                                    FROM `".$this->table."`
                                    WHERE `user_id`='".$data['user_id']."'");
        return  $result[0]['count'];
    }
    
    // Список записей пользователя
    public function user_list($data) {
        $result = Lib_Db::query("   SELECT f.*, u.`login`
                                    FROM `".$this->table."` AS f
									JOIN `".$this->tables['users']."` AS u
                                    ON f.`user_id` = u.`id`
									WHERE f.`user_id` = '".$data['user_id']."'
									AND f.`status` = '1'
                                    ORDER BY f.`datetime` DESC
                                    LIMIT ".$data['start_row'].", ".$data['per_page']."");
        return $result;
    }

	// Получение записи из пополнений по id
	public function get_by_id($data) {
		$result = Lib_Db::query("	SELECT * 
									FROM `".$this->table."`
                                    WHERE `id` = '".$data['id']."'");
		return $result[0];
	}
	
	/**
	 * ADMIN
	 */
	
	// Общая сумма пополнений
	public function all($data = null) {
		$wl = isset($data['wl']) ? "AND `wl` = '".$data['wl']."'" : '';
        $result = Lib_Db::query("  	SELECT SUM(`amount`) AS amount, `valute`
									FROM `".$this->table."`
									WHERE `status` = '1'
									".$wl."
									GROUP BY `valute`");
        return $result;
    }
	
	// Сумма пополнений пользователя
	public function user_all($data) {
        $result = Lib_Db::query("  	SELECT SUM(`amount`) AS amount, `valute`, `payment`
									FROM `".$this->table."`
									WHERE `status` = '1'
									AND `user_id` = '".$data['user_id']."'
									GROUP BY `valute`");
        return $result;
    }
	
	// Список за период
    public function period($data) {
        $result = Lib_Db::query("  	SELECT *
									FROM `".$this->table."`
									WHERE `sort` >= '".$data['start']."'
									AND `sort` < '".$data['end']."'
									AND `status` = '1'");
        return $result;
    }
	
	// Время первой записи
	public function first() {
        $result = Lib_Db::query("  	SELECT `datetime`
									FROM `".$this->table."`
									WHERE `status` = '1'
									ORDER BY `sort`
									LIMIT 1");
        return $result[0]['datetime'];
    }
	
	// Количество пополнений
	public function all_count() {
        $result = Lib_Db::query("  	SELECT COUNT(`id`) AS count
									FROM `".$this->table."`
									WHERE `status` = '1'");
        return $result[0]['count'];
    }
	
	// Список пополнений
    public function last($data) {
        $result = Lib_Db::query("   SELECT f.*, u.`login`
                                    FROM `".$this->table."` AS f, `".$this->tables['users']."` AS u
                                    WHERE f.`user_id` = u.`id`
                                    AND f.`status` = '1'
                                    ORDER BY f.`sort` DESC
                                    LIMIT ".$data['start_row'].", ".$data['per_page']);
        return $result;
    }
}