<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Model_Pools extends Core_Model {

    public function __construct() {
        parent::__construct();
        $this->table = DB_PREF.strtolower(substr(__CLASS__, 6));
    }
    
    // Получение списка
    public function get_list($data) {
        $result = Lib_Db::query("   SELECT *
									FROM `".$this->table."`
									WHERE '".time()."' > `datetime`
									ORDER BY `datetime` DESC
									LIMIT ".$data['start_row'].", ".$data['per_page']);
        return $result;
    }
    
    // Получение количества записей
    public function cnt() {
        $result = Lib_Db::query("   SELECT COUNT(`id`) AS count
                                    FROM `".$this->table."`
                                    WHERE '".time()."' > `datetime`");
        return $result[0]['count'];
    }
    
    // Получение записи по id
    public function get_by_id($data) {
        $result = Lib_Db::query("	SELECT *
	                                FROM ".$this->table."
                                    WHERE `id`='".$data['id']."'");
        return $result[0];
    }
    
    // Получение последнего открытыго пула
    public function get_last() {
        $result = Lib_Db::query("   SELECT *
									FROM `".$this->table."`
									WHERE '".time()."' > `datetime`
									AND `datetime` < `end`
									ORDER BY `datetime` DESC
									LIMIT 1");
        return $result[0];
    }
}

    