<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Model_Deposits extends Core_Model {

    public function __construct() {
        parent::__construct();
        $this->table = DB_PREF.strtolower(substr(__CLASS__, 6));
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
        $result = Lib_Db::query("   SELECT d.*, u.`login`, p.`amount` AS pool_amount
                                    FROM `".$this->table."` AS d
									JOIN `".$this->tables['users']."` AS u
                                    ON d.`user_id` = u.`id`
									JOIN `".$this->tables['pools']."` AS p
                                    ON d.`pool_id` = p.`id`
									WHERE d.`user_id` = '".$data['user_id']."'
                                    ORDER BY d.`datetime` DESC
                                    LIMIT ".$data['start_row'].", ".$data['per_page']);
        return $result;
    }

    // Получение записи по id
    public function get_by_id($data) {
        $result = Lib_Db::query("	SELECT d.*, p.`amount` AS pool_amount
	                                FROM ".$this->table." AS d
	                                JOIN `".$this->tables['pools']."` AS p
                                    ON d.`pool_id` = p.`id`
                                    WHERE d.`id`='".$data['id']."'");
        return $result[0];
    }

    // Получение последней записи
    public function last_sold($data) {
        $result = Lib_Db::query("   SELECT `last_sold`
									FROM `".$this->table."`
									WHERE `user_id` = '".$data['user_id']."'
									ORDER BY `last_sold`
									LIMIT 1");
        return $result[0]['last_sold'];
    }
    
     // Получение суммы пулов
    public function get_sum_by_user($data) {
        $result = Lib_Db::query("   SELECT  SUM(`amount`) AS amount, SUM(`payout`) AS payout, SUM(`accrued`) AS accrued
									FROM `".$this->table."`
									WHERE `user_id` = '".$data['user_id']."'");
        return $result[0];
    }
    
    // Получение количества записей
    public function cnt() {
        $result = Lib_Db::query("   SELECT COUNT(`id`) AS count
                                        FROM `".$this->table."`
                                        WHERE '".time()."' > `datetime`");
        return $result[0]['count'];
    }
    
    // Список пополнений
    public function get_list($data) {
        $result = Lib_Db::query("   SELECT d.*, u.`login`
                                    FROM `".$this->table."` AS d, `".$this->tables['users']."` AS u
                                    WHERE d.`user_id` = u.`id`
                                    AND d.`pool_id` = '".$data['pool_id']."'
                                    ORDER BY d.`datetime` DESC");
        return $result;
    }

    // Получение депозитов для крона
    public function get_list_by_time() {
        $result = Lib_Db::query("  SELECT d.*, p.`amount` AS pool_amount
                                   FROM `".$this->table."` AS d
                                   JOIN `".$this->tables['pools']."` AS p
                                   ON d.`pool_id` = p.`id`
        		                   WHERE d.`next` < '".time()."'");
        return $result;
    }
}