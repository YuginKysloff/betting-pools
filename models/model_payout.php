<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Model_Payout extends Core_Model {

    public function __construct() {
        parent::__construct();
		$this->table = DB_PREF.strtolower(substr(__CLASS__, 6));
    }
    
    // Общая сумма выплат
    public function get_paid() {
        $result = Lib_Db::query("   SELECT SUM(`amount`) AS amount, `valute`
                                    FROM `".$this->table."`
									WHERE `status` = '1'
									GROUP BY `valute`");
        return $result;
    }
    
    // Получение времени последней удачной выплаты пользователя
    public function get_last_user_sort($data) {
        $result = Lib_Db::query("	SELECT `sort` 
	                                FROM `".$this->table."`
									WHERE `user_id`='".$data['user_id']."'
									AND `status`='3'
									ORDER BY `sort` DESC
									LIMIT 1");
        return $result[0]['sort'];
    }
    
    // Получение id последней выплаты
    public function get_last_id() {
        $result = Lib_Db::query("	SELECT `id` 
	                                FROM `".$this->table."`
									ORDER BY `id` DESC
									LIMIT 1");
        return $result[0]['id'];
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
                                    ORDER BY f.`datetime` DESC
                                    LIMIT ".$data['start_row'].", ".$data['per_page']."");
        return $result;
    }
  
	/**
	 * ADMIN
	 */
	 
	// Количество ожидающих выплат
    public function waiting_cnt() {
        $result = Lib_Db::query("  	SELECT COUNT(`id`) AS count
									FROM `".$this->table."`
									WHERE `status` = '0'");
        return $result[0]['count'];
    } 
	
	// Список ожидающих выплат
    public function waiting($data) {
        $result = Lib_Db::query("  	SELECT p.*, u.`login`
									FROM `".$this->table."` AS p
									LEFT OUTER JOIN `".$this->tables['users']."` AS u
									ON p.`user_id` = u.`id`
									WHERE p.`status` = '0'
									LIMIT ".$data['start_row'].", ".$data['per_page']);
        return $result;
    }
	
	// Запись по ID
    public function get_by_id($data) {
        $result = Lib_Db::query("  	SELECT *
									FROM `".$this->table."`
									WHERE `id` = '".$data['id']."'");
        return $result[0];
    }
	
	// ID последней записи
    public function last_id() {
        $result = Lib_Db::query("	SELECT `id` 
									FROM `".$this->table."`
									ORDER BY `id` DESC
									LIMIT 1");
        return $result[0]['id'];
    }
	
	// Общая сумма выплат
	public function all($data = null) {
		$wl = isset($data['wl']) ? "AND `wl` = '".$data['wl']."'" : '';
        $result = Lib_Db::query("  	SELECT SUM(`amount`) AS amount, `valute`
									FROM `".$this->table."`
									WHERE `status` = '1'
									".$wl."
									GROUP BY `valute`");
        return $result;
    }
	
	// Сумма выплат пользователя
	public function user_all($data) {
        $result = Lib_Db::query("  	SELECT SUM(`amount`) AS amount, `valute`
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
	
	// Количество выплат
	public function all_count() {
        $result = Lib_Db::query("  	SELECT COUNT(`id`) AS count
									FROM `".$this->table."`
									WHERE `status` = '1'");
        return $result[0]['count'];
    }
	
	// Список выплат
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