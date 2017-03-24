<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Model_Users extends Core_Model {
	
	public function __construct() {
		parent::__construct();
		$this->table = DB_PREF.strtolower(substr(__CLASS__, 6));
	}
    
    // Получение ID по email
    public function id_by_email($data) {
        $result = Lib_Db::query("   SELECT `id` 
									FROM `".$this->table."` 
                                    WHERE BINARY `email`='".$data['email']."'");
        return  $result[0]['id'];
    }
    
    // Получение пользователя по логину и паролю
    public function get_by_log_pass($data) {
        $result = Lib_Db::query("	SELECT * 
									FROM `".$this->table."` 
									WHERE BINARY `login`='".$data['login']."' AND `password`='".$data['password']."'");
        return $result[0];
    }
    
    // Получение пользователя по email
    public function get_by_email($data){
        $result = Lib_Db::query("	SELECT * 
									FROM `".$this->table."`
									WHERE BINARY `email` = '".$data['email']."'");
        return $result[0];
    }
    
    // Получение общего дохода от рефералов
    public function ref_income($data) {
        $result = Lib_Db::query("   SELECT SUM(`".$data['valute']."_ref`) AS ".$data['valute']."_sum
                                    FROM `".$this->table."`
                                    WHERE `sponsor_id`='".$data['sponsor_id']."'");
        return  $result[0][$data['valute'].'_sum'];
    }
    
    // Получение списка рефералов
    public function ref_list($data){
        $result = Lib_Db::query("   SELECT *
                                    FROM `".$this->table."`
        		                    WHERE `sponsor_id`='".$data['sponsor_id']."'
                                    LIMIT ".$data['start_row'].", ".$data['per_page']);
        return $result;
    }
    
	/**
	 * ADMIN
	 */
	 
	// Получение ID по логину
	public function id_by_login($data) {
		$result = Lib_Db::query("   SELECT `id` 
									FROM `".$this->table."` 
                                    WHERE BINARY `login`='".$data['login']."'");
        return  $result[0]['id'];
	}
	
	// Получение пользователя по ID
    public function get_by_id($data) {
		$result = Lib_Db::query("	SELECT * 
									FROM `".$this->table."` 
									WHERE `id`='".$data['id']."'");
        return $result[0];
    }
	
	// Количество всех пользователей
	public function all() {
		$result = Lib_Db::query("   SELECT COUNT(`id`) AS count 
									FROM `".$this->table."`");
        return  $result[0]['count'];
	}

	// Количество рефералов пользователя
    public function ref_count($data) {
        $result = Lib_Db::query("   SELECT COUNT(`id`) AS count
                                    FROM `".$this->table."`
                                    WHERE `sponsor_id` = '".$data['sponsor_id']."'");
        return  $result[0]['count'];
    }

	// Дата первой регистрации
    public function first() {
        $result = Lib_Db::query("   SELECT `datetime`
                                    FROM `".$this->table."`
									ORDER BY `datetime`
									LIMIT 1");
        return  $result[0]['datetime'];
    }
	
	// Список за период
    public function period($data) {
        $result = Lib_Db::query("  	SELECT `datetime`
									FROM `".$this->table."`
									WHERE `datetime` >= '".$data['start']."'
									AND `datetime` < '".$data['end']."'");
        return $result;
    }
	
	// Список регистраций
    public function last($data) {
        $result = Lib_Db::query("   SELECT u1.`id`, u1.`login`, u1.`email`, u1.`url`, u1.`datetime`, u2.`login` AS sponsor
                                    FROM `".$this->table."` AS u1
									LEFT OUTER JOIN `".$this->table."` AS u2
									ON u1.`sponsor_id` = u2.`id`
                                    ORDER BY `datetime` DESC
                                    LIMIT ".$data['start_row'].", ".$data['per_page']);
        return $result;
    }
	
	// Список url
    public function url() {
        $result = Lib_Db::query("   SELECT COUNT(`id`) AS count, `url`
                                    FROM `".$this->table."`
									GROUP BY `url`
                                    ORDER BY count DESC");
        return $result;
    }
	
	// Количество пользователей (с фильтром)
    public function filter_cnt($data) {
        $result = Lib_Db::query("   SELECT COUNT(u1.`id`) AS count
                                    FROM `".$this->table."` u1
									LEFT OUTER JOIN `".$this->table."` AS u2
									ON u1.`sponsor_id` = u2.`id`
									".$data['where']);
        return $result[0]['count'];
    }
	
	// Список пользователей (с фильтром)
    public function filter_list($data) {
        $result = Lib_Db::query("   SELECT u1.*, u2.`login` AS sponsor
                                    FROM `".$this->table."` u1
									LEFT OUTER JOIN `".$this->table."` AS u2
									ON u1.`sponsor_id` = u2.`id`
									".$data['where']." 
									ORDER BY `id` DESC
                                    LIMIT ".$data['start_row'].", ".$data['per_page']);
        return $result;
    }
	
	// Вывод всех подтвержденных email
    public function get_emails($data = null) {
        $result = Lib_Db::query("   SELECT `email`
                                    FROM `".$this->table."`
									WHERE `email_confirm` = '1'
									ORDER BY `id` DESC");
        return $result;
    }
}