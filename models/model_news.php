<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

// Запрет на прямой доступ к файлу
defined('SW_CONSTANT') or die;

class Model_News extends Core_Model {

    public function __construct() {
        parent::__construct();
		$this->table = DB_PREF.strtolower(substr(__CLASS__, 6));
    }
    
    // Получение списка новостей
    public function get_list($data) {
        $result = Lib_Db::query("   SELECT *
									FROM `".$this->table."`
									WHERE `lang` = '".$data['current_lang']."'
									AND '".time()."' > `datetime`
									ORDER BY `datetime` DESC, `id` DESC
									LIMIT ".$data['start_row'].", ".$data['per_page']);
        return $result;
    }
    
    // Получение новости по id
    public function get_id($data) {
        $result = Lib_Db::query("   SELECT *
									FROM `".$this->table."`
									WHERE `id` = '".$data['id']."'
									AND `lang` = '".$data['current_lang']."'");
        return $result[0];
    }
    
	/**
	 * ADMIN
	 */
	 
	// Получение количества новостей
    public function cnt($data) {
        $result = Lib_Db::query("   SELECT COUNT(`id`) AS count
                                    FROM `".$this->table."`
									WHERE `lang` = '".$data['current_lang']."'
									AND '".time()."' > `datetime`");
        return $result[0]['count'];
    }
	
	// Получение списка новостей
    public function get($data) {
        $result = Lib_Db::query("   SELECT *
									FROM `".$this->table."`
									WHERE `lang` = '".$data['current_lang']."'
									ORDER BY `datetime` DESC, `id` DESC
									LIMIT ".$data['start_row'].", ".$data['per_page']);
        return $result;
    }
	
	// Проверка новости на существование 
    public function check($data) {
		$and = isset($data['current_lang']) ? "AND `lang` = '".$data['current_lang']."'" : '';
        $result = Lib_Db::query("   SELECT `id`
									FROM `".$this->table."`
									WHERE `datetime` = '".$data['datetime']."'
									".$and."
									LIMIT 1");
        return $result[0]['id'];
    }
	
	// Получение новостей по дате
    public function get_by_date($data) {
        $result = Lib_Db::query("   SELECT *
									FROM `".$this->table."`
									WHERE `datetime` = '".$data['datetime']."'");
        return $result;
    }
}